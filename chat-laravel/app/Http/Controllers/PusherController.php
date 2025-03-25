<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use App\Models\Message;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PusherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $messages = Message::with('user')->orderBy('created_at', 'asc')->get();
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

        // Store the message with user_id
        $newMessage = Message::create([
            'message' => $message,
            'sender_type' => 'user',
            'user_id' => Auth::id()
        ]);

        Log::info('Message broadcasted:', ['message' => $newMessage->toArray()]);

        broadcast(new PusherBroadcast($message, Auth::user()))->toOthers();

        return view('broadcast', [
            'message' => $message,
            'user' => Auth::user()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function receive(Request $request): Factory|View|Application
    {
        $message = $request->get('message');
        $user = $request->get('user');

        // Store the received message
        Message::create([
            'message' => $message,
            'sender_type' => 'user',
            'user_id' => $user['id'] ?? null
        ]);

        return view('receive', [
            'message' => $message,
            'user' => $user
        ]);
    }
}
