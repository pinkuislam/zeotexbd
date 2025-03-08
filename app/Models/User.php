<?php

namespace App\Models;

use App\Services\CustomerService;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles;

    const USER_IMAGE_PATH = 'users';

    protected $appends = ['image_url'];

    protected $fillable = [
        'code', 'name', 'mobile', 'email', 'password', 'role', 'fcm_token', 'status','created_by','updated_by', 'address', 'color', 'image', 'opening_due','mobile_2','fb_page_link','gender','nid_no'
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    protected $totalCustomerDue = 0;

    public function customer()
    {
        return $this->hasMany(Customer::class, 'user_id','id');
    }
    public function totalCustomer()
    {
        return $this->customer()->count();
    }
    public function totalCustomerDue()
    {
        foreach ($this->customer as $customer) {
            $this->totalCustomerDue +=  CustomerService::due($customer->id);
        }
        return $this->totalCustomerDue;
    }
    public function customerOrder()
    {
        return $this->hasMany(Order::class, 'user_id','id');
    }
    public function totalCustomerOrder()
    {
        return $this->customerOrder()->count();
    }
    public function totalCustomerOrderAmount()
    {
        return $this->customerOrder()->sum('amount');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by','id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by','id');
    }
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url(self::USER_IMAGE_PATH . DIRECTORY_SEPARATOR . $this->image);
        }
        return null;
    }
    public function userProducts()
    {
        return $this->belongsToMany(Product::class)->withPivot(['price']);
    }
}