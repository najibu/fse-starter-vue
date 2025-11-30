<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Message;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_inertia_page()
    {
        $response = $this->get(route('chat.index'));
        $response->assertStatus(200);
        $this->assertStringContainsString('data-page', $response->getContent());
    }

    public function test_fetch_returns_messages_with_user_name_and_content()
    {
        Message::factory()->create(['user_name' => 'Bob', 'content' => '<b>First</b>']);
        Message::factory()->create(['user_name' => 'Carol', 'content' => 'Second']);

        $response = $this->getJson(route('chat.fetch'));

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

        $response = $this->post(route('chat.store'), $data);

        $response->assertRedirect(route('chat.index'));

        $this->assertDatabaseHas('messages', [
            'content'   => 'Hello world',
            'user_name' => 'John Doe',
        ]);
    }

    public function test_store_validation_failure_returns_422_json()
    {
        $response = $this->postJson(route('chat.store'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }

    public function test_store_validation_failure()
    {
        $response = $this->post(route('chat.store'), []);

        // Expect validation error for missing content (redirect back with errors)
        $response->assertSessionHasErrors('content');

        $this->assertDatabaseCount('messages', 0);
    }



}
