<?php

namespace App\Http\Admin\V1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
