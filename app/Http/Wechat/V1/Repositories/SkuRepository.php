<?php

namespace App\Http\Wechat\V1\Repositories;

use App\Http\Base\BaseRepository;
use App\Http\Common\CommonException;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Sku;

class SkuRepository extends BaseRepository implements NotFoundExceptionInterface
{

    protected $model;


    /**
     * SkuRepository constructor.
     * @param Sku $sku
     */
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
        throw new CommonException('该sku不存在!', $this->httpNotFound);
    }


}
