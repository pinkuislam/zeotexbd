<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryAgentResource extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'emergency_mobile' => $this->emergency_mobile,
            'type' => $this->type,
            'opening_due' => $this->opening_due,
            'status' => $this->status,
        ];
    }
}
