<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show(Request $request)
    {
        $userid = Auth::user()->id;
        $messages = Message::where(function ($query) use ($userid) {
            $query->where('from');
        })->orWhere(function ($query) use ($userid) {
            $query->where('to', $userid);
        })->get();

        return response()->json($messages);
    }
    public function store(Request $request)
    {
        $message = $request->user()->messages()->create([
            'body' => $request->body,
        ]);

        return response()->json($message);
    }
}
