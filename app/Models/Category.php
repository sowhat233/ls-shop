<?php


namespace App\Models;


use App\Http\Base\BaseModel;

class Category extends BaseModel
{
    protected $table = 'category';

    public $timestamps = false;

    protected $guarded = [];


    public function products()
    {
        return $this->belongsTo(Product::class, 'id', 'category_id');
    }


}