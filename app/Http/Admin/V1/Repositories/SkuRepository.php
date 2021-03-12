<?php

namespace App\Http\Admin\V1\Repositories;

use App\Http\Base\BaseRepository;
use App\Models\Sku;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SkuRepository extends BaseRepository
{

    protected $model;

    public function __construct(Sku $sku)
    {
        $this->model = $sku;
    }

    /**
     * @param string $order
     * @param string $sort
     * @return mixed
     */
    public function getSkuList($order = 'id', $sort = 'desc')
    {
        return $this->model->orderBy($order, $sort)->get();
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function deleteProduct()
    {
        return $this->model->delete();
    }


}
