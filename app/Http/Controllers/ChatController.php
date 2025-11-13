<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        // Ambil semua pesan chat
        $chatMessages = ChatMessage::with('user')->get();

        return view('main.chat', compact('chatMessages'));
    }

    public function sendMessage(Request $request)
    {
        // Validasi input pesan
        $this->validate($request, [
            'message' => 'required'
        ]);

        // Simpan pesan ke basis data
        ChatMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return redirect()->back();
    }
}
