<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ReplyCollection extends ResourceCollection
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
                return [
                    // 'reply'=>$data->replies,
                    'comment_id' => $data->id,
                    'video_id' => $data->commentable_id,
                    'comment' => $data->comment,
                    'parent_id' => $data->parent_id,
                    'user_id' => $data->user->id,
                    'username' => $data->user->username,
                    'profile_image' => asset('uploads/avtars/' . $data->user->profile_image),
                    'isReplyLiked'=>$data->liked(Auth::user()->id),
                    'total_likes' => $data->likeCount,
                    'created_at'=>$data->created_at,
                    'nested_replies'=>new ReplyCollection($data->replies)
                ];
            }),
        ];
    }
}
