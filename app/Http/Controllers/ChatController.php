<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Resources\MessageResource;
use App\Http\Requests\Chat\IndexRequest;
use App\Http\Requests\Chat\FetchRequest;
use App\Http\Requests\Chat\StoreRequest;

class ChatController extends Controller
{
    public function index(IndexRequest $request)
    {
        return inertia('chat/Index');
    }

    /**
     * Fetch the latest messages.
     */
    public function fetch(FetchRequest $request)
    {
        $messages = Message::latest()->take(50)->get()->reverse()->values();

        return response()->json([
            'messages' => MessageResource::collection($messages),
        ]);
    }

    /**
     * Store a new chat message.
     */
    public function store(StoreRequest $request)
    {
        $data    = $request->validated();
        $message = Message::create([
            'user_name' => $data['user_name'],
            'content'   => $data['content'],
        ]);

        return to_route('chat.index');
    }
}
