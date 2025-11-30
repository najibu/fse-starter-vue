<template>
    <div class="min-h-screen bg-gray-50 py-8 px-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <!-- Header -->
            <div class="mb-6 flex items-start justify-between">
                <div class="ml-6">
                    <h1 class="text-3xl font-bold text-gray-900">Team Chat</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Public chat room. Messages are visible to everyone.
                    </p>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <label class="block text-sm font-medium text-gray-700">User name</label>
                    <input
                        v-model="userName"
                        type="text"
                        placeholder="Enter your user name"
                        @blur="saveDisplayName"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm"
                    />
                </div>
            </div>

            <!-- Chat card -->
            <div class="flex h-[80vh] flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg">
                <!-- Messages Container -->
                <div ref="messagesContainer" class="flex-1 overflow-y-auto bg-gray-50 p-6">
                    <!-- Loading State -->
                     <div v-if="isLoading && messageList.length === 0" class="flex items-center justify-center py-12">
                        <p class="text-gray-500">Loading messages...</p>
                     </div>

                    <!-- Error State -->
                    <div v-else-if="error" class="rounded-md bg-red-50 p-4">
                        <p class="text-sm text-red-700">{{ error }}</p>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="messageList.length === 0" class="flex items-center justify-center py-12">
                        <p class="text-gray-500">No messages yet. Be the first to say hello 👋</p>
                    </div>

                    <!-- Messages List -->
                    <div v-else class="space-y-4">
                        <div v-for="message in messageList" :key="message.id" class="rounded-md bg-white p-4 shadow-sm">
                            <div class="flex items-baseline gap-2">
                                <p class="font-semibold text-gray-900">{{ message.user_name || userName  }}</p>
                                <p class="text-xs text-gray-500">{{ formatTime(message.created_at) }}</p>
                            </div>

                            <p class="mt-2 whitespace-pre-line text-gray-700">{{ message.content }}</p>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="border-t border-gray-200 bg-white p-6">
                    <form @submit.prevent="sendMessage" class="space-y-4">
                        <div>
                            <textarea
                                v-model="form.content"
                                placeholder="Type your message here..."
                                @keydown.enter.exact="sendMessage"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm"
                            />

                            <p class="mt-1 text-xs text-gray-500">
                                Press Enter to send, Shift + Enter for a new line.
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="isSending || !form.content"
                                class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-400"
                            >
                                {{ isSending ? 'Sending...' : 'Send' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Message } from '@/types'
import { ref, onMounted, computed, nextTick, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const userName = ref<string>('')
const messageList = ref<Message[]>([])
const isLoading = ref<boolean>(false)
const error = ref<string | null>(null)
const messagesContainer = ref<HTMLElement | null>(null)

let pollInterval: ReturnType<typeof setInterval> | null = null

// use Inertia useForm for the message input and processing state
const form = useForm({ content: '', user_name: userName.value })
const isSending = computed(() => form.processing)

/**
 * Load display name from localStorage
 */
function loadName() {
    userName.value = localStorage.getItem('chat_user_name') || 'Anonymous'
    form.user_name = userName.value
}

/**
 * Save display name to localStorage
 */
function saveDisplayName() {
    if (userName.value) {
        localStorage.setItem('chat_user_name', userName.value)
        form.user_name = userName.value
    }
}

/**
 * Format timestamp to HH:MM
 */
function formatTime(isoString: string | null): string {
    if (!isoString) return ''

    try {
        const date = new Date(isoString)
        return date.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        })
    } catch {
        return ''
    }
}

/**
 * Fetch latest messages from server
 */
async function fetchMessages() {
    try {
        isLoading.value = true
        error.value = null

        const response = await fetch(route('chat.fetch'))
        const data = await response.json()

        if (data.messages) {
            messageList.value = data.messages
        }

        await scrollToBottom()
    } catch (err) {
        error.value = 'Failed to load messages.'
        console.error('Error fetching messages:', err)
    } finally {
        isLoading.value = false
    }
}

async function sendMessage() {
    if (!form.content) return

    try {
        error.value = null

        form.post(route('chat.store'), {
            onSuccess: async () => {
                await fetchMessages()
                form.reset('content')
                scrollToBottom()
             },
            onError: () => {
                error.value = 'Failed to send message. Plese try again.'
             },
        })

    } catch (err) {
        error.value = (err as Error).message || 'Failed to send message.'
        console.error('Error sending message:', err);
    }
}

/**
 * Scroll messages container to bottom
 */
async function scrollToBottom() {
    await nextTick()
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

onMounted(async () => {
    loadName()
    await fetchMessages()

    // Start polling every 3 seconds
    pollInterval = setInterval(fetchMessages, 3000)

})

onUnmounted(() => {
    // Clear polling interval on component unmount
    if (pollInterval) {
        clearInterval(pollInterval)
    }
})
</script>
