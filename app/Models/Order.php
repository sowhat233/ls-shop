<?php


namespace App\Models;


use App\Http\Base\BaseModel;

class Order extends BaseModel
{

    protected $table = 'order';

    public $timestamps = false;

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}