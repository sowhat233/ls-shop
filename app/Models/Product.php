<?php


namespace App\Models;


use App\Http\Base\BaseModel;

class Product extends BaseModel
{

    protected $table = 'products';

    public $timestamps = false;

    protected $guarded = [];


    public function getCreatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", $value) : '';
    }


    public function sku()
    {
        return $this->hasMany(Sku::class, 'product_id', 'id');
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }


}