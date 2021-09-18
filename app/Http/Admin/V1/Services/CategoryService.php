<?php


namespace App\Http\Admin\V1\Services;


use App\Http\Admin\V1\Repositories\CategoryRepository;
use App\Http\Admin\V1\Repositories\ProductRepository;
use DB;

class CategoryService
{

    private $categoryRepo;
    private $productRepo;


    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $this->categoryRepo = $categoryRepository;
        $this->productRepo  = $productRepository;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getCategoryPaginate($params)
    {

        $where = $this->handlecategoryIndexParams($params);

        return $this->categoryRepo->getCategoryPaginate($where);

    }

    /**
     * @param $params
     * @return array
     */
    public function handleCategoryIndexParams($params)
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
     * @throws \Throwable
     */
    public function deleteCategory($id)
    {

        $category = $this->categoryRepo->findCategoryById($id);

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
