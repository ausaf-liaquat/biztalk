<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

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
                    'user_id'=>$data->user->id,
                    'username' => $data->user->username,
                    'profile_image'=>asset('uploads/avtars/'.$data->user->profile_image)
                ];
            }),
        ];
    }
}
