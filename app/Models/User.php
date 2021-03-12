<?php


namespace App\Models;


use App\Http\Base\BaseModel;

class User extends BaseModel
{

    protected $table = 'user';

    public $timestamps = false;

    protected $guarded = [];


}