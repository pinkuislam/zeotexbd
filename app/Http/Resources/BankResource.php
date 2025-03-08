<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
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
            'account_name' => $this->account_name,
            'account_no' => $this->account_no,
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name,
            'opening_balance' => $this->opening_balance,
            'status' => $this->status,
        ];
    }
}
