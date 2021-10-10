<?php

namespace App\Http\Admin\V1\Repositories;

use App\Http\Base\BaseRepository;
use App\Http\Common\CommonException;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Sku;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SkuRepository extends BaseRepository  implements NotFoundExceptionInterface
{

    protected $model;

    public function __construct(Sku $sku)
    {
        $this->model = $sku;
    }


    /**
     * @return mixed|void
     * @throws CommonException
     */
    public function notFoundException()
    {
        throw new CommonException('该商品的sku不存在!', null, $this->httpNotFound);
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


}
