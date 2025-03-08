<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ShippingRate;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','contact_name',  'mobile', 'email', 'address', 'shipping_rate_id', 'shipping_address', 'type', 'user_id', 'opening_due', 'status', 'created_by', 'updated_by'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class,'customer_id');
    }
    public function shipping()
    {
        return $this->belongsTo(ShippingRate::class, 'shipping_rate_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
