<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function getStock()
    {
        $data = AccessoryItem::where('accessory_id',$this->id)->where('status',0)->where('type', 'Purchase')
        ->selectRaw('(sum(quantity)-sum(used_quantity)) as totalstock')
        ->first();
        return $data->totalstock ?? 0;
    }
}
