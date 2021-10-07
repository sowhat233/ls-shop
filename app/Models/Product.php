<?php


namespace App\Models;

use App\Enums\ProductEnums;
use App\Http\Base\BaseModel;

class Product extends BaseModel
{

    protected $table = 'product';

    public $timestamps = false;

    protected $guarded = [];


    public function scopeStatus()
    {
        $where[] = ['status' => ProductEnums::IsLaunched];

        return $where;
    }


    public function getCreatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", $value) : '';
    }


    public function sku()
    {
        return $this->hasMany(Sku::class);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


}