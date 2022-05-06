<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use App\Traits\ApiResponser;
use App\Http\Resources\UserCollection;

class ChatController extends Controller
{

    use ApiResponser;
    public function show(Request $request)
    {
        $userid = Auth::user()->id;
        $from = Message::where('from_user_id', $userid)->pluck('to_user_id')->toArray();
        $to = Message::where('to_user_id', $userid)->pluck('from_user_id')->toArray();

        $messages = Message::where(function ($query) use ($userid) {
            $query->where('from');
        })->orWhere(function ($query) use ($userid) {
            $query->where('to', $userid);
        })->get();
        // if (($key = array_search($userid, $from)) !== false) {
        //     unset($from[$key]);
            
        // }
        $message_list = array_values(array_unique(array_merge($from,$to)));


        return $this->success(['list'=>new UserCollection(User::whereIn('id',$message_list)->get())],'messages',200);

        
    }
    public function store(Request $request)
    {
        $message = $request->user()->messages()->create([
            'body' => $request->body,
        ]);

        return response()->json($message);
    }
}
