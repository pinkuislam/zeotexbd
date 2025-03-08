<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveDyeing extends Model
{
    
    protected $fillable = [
        'dyeing_agent_id', 'code', 'date', 'note', 'unit_price', 'total_cost', 'grey_fabric_consume', 'created_by', 'updated_by'
    ];

    public function dyeingAgent()
    {
        return $this->belongsTo(DyeingAgent::class);
    }

    public function items()
    {
        return $this->morphMany(ProductIn::class, 'flagable');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
