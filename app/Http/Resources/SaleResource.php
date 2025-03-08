<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'order' => $this->order,
            'items' => $this->items,
            'customer' => $this->customer,
            'resellerBusiness' => $this->resellerBusiness,
            'user' => $this->user,
            'shipping' => $this->shipping,
            'delivery' => $this->delivery,
            'shipping_charge' => $this->shipping_charge,
            'deduction_amount' => $this->deduction_amount,
            'vat_percent' => $this->vat_percent,
            'vat_amount' => $this->vat_amount,
            'subtotal_amount' => $this->subtotal_amount,
            'total_amount' => $this->total_amount,
            'commission_percent' => $this->commission_percent,
            'commission_amount' => $this->commission_amount,
            'reseller_amount' => $this->reseller_amount,
            'status' => $this->status,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ];
    }
}
