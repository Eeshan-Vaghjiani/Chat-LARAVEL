<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use App\Models\Message;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PusherController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $messages = Message::orderBy('created_at', 'asc')->get();
        Log::info('Messages loaded:', ['count' => $messages->count(), 'messages' => $messages->toArray()]);
        return view('index', compact('messages'));
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function broadcast(Request $request): Factory|View|Application
    {
        $message = $request->get('message');

        // Store the message
        $newMessage = Message::create([
            'message' => $message,
            'sender_type' => 'user'
        ]);

        Log::info('Message broadcasted:', ['message' => $newMessage->toArray()]);

        broadcast(new PusherBroadcast($message))->toOthers();

        return view('broadcast', ['message' => $message]);
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function receive(Request $request): Factory|View|Application
    {
        $message = $request->get('message');

        // Store the message
        $newMessage = Message::create([
            'message' => $message,
            'sender_type' => 'system'
        ]);

        Log::info('Message received:', ['message' => $newMessage->toArray()]);

        return view('receive', ['message' => $message]);
    }
}
