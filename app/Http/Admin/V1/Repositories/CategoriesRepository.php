<?php

namespace App\Http\Admin\V1\Repositories;

use App\Http\Admin\V1\Exceptions\CategoriesException;
use App\Http\Base\BaseRepository;
use App\Interfaces\notFoundExceptionInterface;
use App\Models\Category;

class CategoriesRepository extends BaseRepository implements notFoundExceptionInterface
{

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    /**
     * @return mixed|void
     * @throws CategoriesException
     */
    public function notFoundException()
    {
        throw new CategoriesException('该分类不存在!', $this->not_found_code);
    }

    /**
     * @param array $column
     * @param string $order
     * @param string $sort
     * @return mixed
     */
    public function getCategoriesList($column = ['*'], $order = 'id', $sort = 'desc')
    {
        return $this->model->select($column)->orderBy($order, $sort)->get();
    }

    /**
     * @param array $where
     * @param array $column
     * @param string $order
     * @param string $sort
     * @return mixed
     */
    public function getCategoriesPaginate($where = [], $column = ['*'], $order = 'id', $sort = 'desc')
    {
        return $this->model->where($where)->select($column)->orderBy($order, $sort)->paginate();
    }


    /**
     * @param $id
     * @param array $column
     * @param array $with
     * @return CategoriesRepository|CategoriesRepository[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findCategoryById($id, $column = ['*'], $with = [])
    {
        return $this->findOneOrFail($id, $this, $column, $with);
    }


}
