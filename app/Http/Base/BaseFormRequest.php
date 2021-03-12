<?php

namespace App\Http\Base;
/**
 * 基类
 */

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


}
