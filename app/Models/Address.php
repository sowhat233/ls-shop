<?php


namespace App\Models;


use App\Http\Base\BaseModel;

class Address extends BaseModel
{

    protected $table = 'user_address';

    public $timestamps = false;

    protected $guarded = [];


}