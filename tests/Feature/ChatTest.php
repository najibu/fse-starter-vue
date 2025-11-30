<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Message;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_renders_chat_page()
    {
        $response = $this->get('/chat');

        $response->assertStatus(200);
    }

    public function test_fetch_returns_messages_with_user_name_and_content()
    {
        Message::factory()->create(['user_name' => 'Bob', 'content' => 'First']);
        Message::factory()->create(['user_name' => 'Carol', 'content' => 'Second']);

        $response = $this->getJson('/chat/messages');

        $response->assertOk()
            ->assertJsonStructure(['messages' => [['id','user_name','content','created_at','updated_at']]]);

        $data = $response->json('messages');
        $this->assertCount(2, $data);

        $contents = array_column($data, 'content');
        $this->assertContains('First', $contents);
        $this->assertContains('Second', $contents);
    }

    public function test_store_creates_message_and_redirects()
    {
        $data = [
            'content'   => 'Hello world',
            'user_name' => 'John Doe',
        ];

        $response = $this->post('/chat/messages', $data);

        $response->assertRedirect(route('chat.index'));

        $this->assertDatabaseHas('messages', [
            'content'   => 'Hello world',
            'user_name' => 'John Doe',
        ]);
    }

    public function test_store_validation_failure()
    {
        $response = $this->post('/chat/messages', []);

        // Expect validation error for missing content (redirect back with errors)
        $response->assertSessionHasErrors('content');

        $this->assertDatabaseCount('messages', 0);
    }



}
