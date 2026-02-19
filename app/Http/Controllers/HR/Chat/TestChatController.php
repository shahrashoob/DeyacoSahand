<?php

namespace App\Http\Controllers\HR\Chat;

use App\Events\TestMessageSent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestChatController extends Controller
{
    public function index()
    {
        return view('hr/chat/test/chat');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'message' => 'required'
        ]);

        broadcast(new TestMessageSent($request->username, $request->message));

        return ['status' => 'Message Sent!'];
    }
}
