<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UnitResource;

class ProductResource extends JsonResource
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
            'code' => $this->code,
            'type' => $this->type,
            'category' => $this->category,
            'unit' => new UnitResource($this->unit),
            'unit_price' => $this->unit_price,
            'reseller_price' => $this->reseller_price,
            'alert_quantity' => $this->alert_quantity,
            'status' => $this->status,
        ];
    }
}
