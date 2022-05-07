<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class CommentCollection extends ResourceCollection
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
                    'comment_id' => $data->id,
                    'video_id' => $data->commentable_id,
                    'comment' => $data->comment,
                    'user' => new UserResource($data->user),
                    'created_at' => $data->created_at,
                    'total_replies' => $data->replies->count(),
                    'isCommentLiked' => $data->liked(Auth::user()->id),
                    'total_likes' => $data->likeCount,
                    'replies' => new ReplyCollection($data->childrenReplies),

                ];
            }),
        ];
    }
}
