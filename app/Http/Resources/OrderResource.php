<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'code' => $this->code,
            'date' => $this->date,
            'type' => $this->type,
            'note' => $this->note,
            'items' => $this->items,
            'images' => $this->images,
            'customer' => $this->customer,
            'resellerBusiness' => $this->resellerBusiness,
            'user' => $this->user,
            'shipping' => $this->shipping,
            'shipping_charge' => $this->shipping_charge,
            'amount' => $this->amount,
            'status' => $this->status,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ];
    }
}
