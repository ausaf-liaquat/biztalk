<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
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
                    'user_id'=>$data->id,
                    'username' => $data->username,
                    'full_name'=> $data->first_name.' '.$data->last_name,
                    'profile_image'=>asset('uploads/avtars/'.$data->profile_image)
                ];
            }),
        ];
    }
}
