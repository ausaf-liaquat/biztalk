<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConvoListCollection;
use App\Http\Resources\MessageCollection;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ReportVideo;
use App\Models\User;
use App\Traits\ApiResponser;
use Auth;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use ApiResponser;

    public function show(Request $request)
    {
        $conversation = Conversation::where(function ($query) use ($request) {
            $query->where('user_two', Auth::user()->id);
        })->orWhere(function ($query) use ($request) {
            $query->where('user_one', Auth::user()->id);
        })->get();

        return $this->success(['Conversations list' => new ConvoListCollection($conversation)], 'messages', 200);
    }
    public function store(Request $request)
    {
        $conv = Conversation::where(function ($query) use ($request) {
            $query->where('user_one', $request->get('userid'))->where('user_two', Auth::user()->id);
        })->orWhere(function ($query) use ($request) {
            $query->where('user_one', Auth::user()->id)->where('user_two', $request->get('userid'));
        })->first();

        if (!empty($conv)) {
            Message::create([
                'body' => $request->body,
                'from_user_id' => Auth::user()->id,
                'to_user_id' => $request->get('userid'),
                'conversation_id' => $conv->id,
            ]);
        } else {
            $conversation = Conversation::create(['user_one' => Auth::user()->id, 'user_two' => $request->get('userid')]);
            Message::create([
                'body' => $request->body,
                'from_user_id' => Auth::user()->id,
                'to_user_id' => $request->get('userid'),
                'conversation_id' => $conversation->id,
            ]);
        }

        return $this->success([], 'message sent', 200);
    }

    public function details(Request $request)
    {
        $message_details = Message::where(function ($query) use ($request) {
            $query->where('from_user_id', $request->get('userid'))->where('to_user_id', Auth::user()->id);
        })->orWhere(function ($query) use ($request) {
            $query->where('from_user_id', Auth::user()->id)->where('to_user_id', $request->get('userid'));
        })->get();

        return $this->success([new MessageCollection($message_details)], 'message details', 200);
    }
    public function reportVideo(Request $request)
    {
        ReportVideo::create([
            'reason' => $request->get('reason'),
            'detail' => $request->get('details'),
            'video_id' => $request->get('video_id'),
            'user_id' => Auth::user()->id,
            'status' => 'to_be_review',
        ]);
        return $this->success([], 'video reported', 200);
    }
}
