<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageCollection extends ResourceCollection
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
                    'message_from' => new UserResource(User::find($data->from_user_id)),
                    'message_to' => new UserResource(User::find($data->to_user_id)),
                    'message' => $data->body,
                ];
            }
            ),
        ];
    }
}
