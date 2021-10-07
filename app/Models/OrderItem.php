<?php


namespace App\Models;


use App\Http\Base\BaseModel;

class OrderItem extends BaseModel
{

    protected $table = 'order_item';

    public $timestamps = false;

    protected $guarded = [];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}