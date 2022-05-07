<?php

namespace App\Http\Resources;

use App\Models\Message;
use App\Models\User;
use Auth;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ConvoListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            $this->collection->map(function ($data) {
                $msg = Message::where(function ($query) use ($data) {
                    $query->where('from_user_id', $data->user_one)->where('to_user_id', $data->user_two);
                })->orWhere(function ($query) use ($data) {
                    $query->where('from_user_id', $data->user_two)->where('to_user_id', $data->user_one);
                })->latest()->first();

                if ($data->user_one == Auth::user()->id) {
                    $user_id = $data->user_two;
                } elseif ($data->user_two == Auth::user()->id) {
                    $user_id = $data->user_one;
                }

                $user = User::find($user_id);
                return [
                    'user_id' => $user_id,
                    'username' => $user->username,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'profile_image' => asset('uploads/avtars/' . $user->profile_image),
                    'recent' => $msg->body,
                ];
            }
            ),
        ];
    }
}
