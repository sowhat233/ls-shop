<?php


namespace App\Http\Admin\V1\Services;


use App\Http\Admin\V1\Repositories\CategoriesRepository;
use App\Http\Admin\V1\Repositories\ProductRepository;
use DB;

class CategoriesService
{

    private $categoriesRepo;
    private $productRepo;


    public function __construct(CategoriesRepository $categoriesRepository, ProductRepository $productRepository)
    {
        $this->categoriesRepo = $categoriesRepository;
        $this->productRepo    = $productRepository;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getCategoriesPaginate($params)
    {

        $where = $this->handleCategoriesIndexParams($params);

        return $this->categoriesRepo->getCategoriesPaginate($where);

    }

    /**
     * @param $params
     * @return array
     */
    public function handleCategoriesIndexParams($params)
    {

        $where = [];

        //仅查询name
        if (isset($params['query'])) {

            $where[] = [
                'name', 'like', $params['query'].'%',
            ];
        }

        return $where;
    }

    /**
     * @param $id
     */
    public function deleteCategory($id)
    {

        $category = $this->categoriesRepo->findCategoryById($id);

        //开启事务
        DB::beginTransaction();

        try {

            //解除与product表的关联
            $this->productRepo->dissociateCategory($id);

            $category->delete();

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

        }

    }


}
