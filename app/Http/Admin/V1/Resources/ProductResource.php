<?php

namespace App\Http\Admin\V1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id'             => $this->id,
            'key'            => $this->key,
            'name'           => $this->name,
            'price'          => $this->price,
            'sales'          => $this->sales,
            'sku'            => $this->sku,
            'stock'          => $this->stock,
            'category_name'  => $this->category_name,
            'is_enable_spec' => $this->is_enable_spec,
            'sale_status'    => $this->sale_status,
        ];

    }


}
