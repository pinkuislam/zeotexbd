<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
//use Spatie\Activitylog\Traits\LogsActivity;
//use Spatie\Activitylog\LogOptions;
use App\Models\BasePackage;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'name', 'code', 'master_type', 'category_type', 'product_type', 'category_id', 'unit_id', 'alert_quantity', 'seat_count', 'stock_price', 'sale_price', 'reseller_price', 'status','created_by','updated_by'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(ProductBase::class, 'product_id');
    }

    public function item()
    {
        //only for base
        return $this->hasOne(ProductFabric::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function otherInfo()
    {
        return $this->hasOne(ProductOtherInfo::class, 'product_id');
    }
    public function productItem()
    {
        return $this->hasOne(ProductItem::class, 'product_id');
    }
    public function productItems()
    {
        return $this->hasMany(ProductItem::class, 'product_id');
    }
    public function otherImages()
    {
        return $this->hasMany(ProductOtherImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
    public function otherCategory()
    {
        return $this->hasOneThrough(Category::class, ProductOtherInfo::class, 'product_id', 'category_id');
    }
    public function prodductUsers()
    {
        return $this->belongsToMany(User::class)->withPivot(['price']);
    }
    public function getStock()
    {
        $data = ProductIn::where('product_id', $this->id)->where('color_id', $this->color_id)
            ->selectRaw('sum(quantity) as total_qty, sum(used_quantity) as total_used_qty, (sum(quantity)-sum(used_quantity)) as totalstock')
            ->first();
        return $data->totalstock ?? 0;
    }

    public function getComboStock()
    {
        $stock = 9999999;
        foreach($this->items as $item) {
            $data = ProductIn::where('product_id', $item->id)->where('color_id', $this->color_id)
                ->selectRaw('sum(quantity) as total_qty, sum(used_quantity) as total_used_qty, (sum(quantity)-sum(used_quantity)) as totalstock')
                ->first();
                
                if($data->totalstock) {
                if($data->totalstock < $stock){
                    $stock = $data->totalstock;
                }
            } else{
                $stock = 0;
            }
        }
        return $stock;
    }

    public function getStockPrice()
    {
        $price = 0;
        foreach($this->items as $item){
            $base = Product::find($item->base_id);
            $price += $base->stock_price * $item->quantity;
        }
        return $price;
    }

    public function getSalePrice()
    {
        $price = 0;
        foreach($this->items as $item){
            $base = Product::find($item->base_id);
            $price += $base->sale_price * $item->quantity;
        }
        return $price;
    }

    public function getResellerPrice()
    {
        $price = 0;
        foreach($this->items as $item){
            $base = Product::find($item->base_id);
            $price += $base->reseller_price * $item->quantity;
        }
        return $price;
    }

    public function scopeWithStock(Builder $query, Closure $stockInQueryClosure = null, Closure $stockOutQueryClosure = null): Builder
    {
        $stockInQuery = ProductIn::from('product_ins as ins')->selectRaw("sum(ins.quantity)")
            ->whereColumn('ins.product_id', $query->qualifyColumn('id'));
        if ($stockInQueryClosure) {
            $stockInQueryClosure($stockInQuery);
        }
        $stockOutQuery = ProductIn::from('product_ins as outs')->selectRaw("sum(outs.used_quantity)")
            ->whereColumn('outs.product_id', $query->qualifyColumn('id'));
        if ($stockOutQueryClosure) {
            $stockOutQueryClosure($stockOutQuery);
        }
        return $query->addSelect(DB::raw(sprintf(
            'ifnull((%s), 0) - ifnull((%s), 0) as stock_quantity',
            $stockInQuery->toSql(),
            $stockOutQuery->toSql(),
        )))
            ->addBinding($stockInQuery->getBindings())
            ->addBinding($stockOutQuery->getBindings());
    }
}
