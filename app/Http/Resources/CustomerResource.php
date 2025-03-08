<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact_name' => $this->contact_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'type' => $this->type,
            'opening_due' => $this->opening_due,
            'shipping_address' => $this->shipping_address,
            'address' => $this->address,
            'shipping' => $this->shipping,
            'user' => $this->user,
            'status' => $this->status,
        ];
    }
}
