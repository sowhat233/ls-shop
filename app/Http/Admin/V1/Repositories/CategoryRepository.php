<?php

namespace App\Http\Admin\V1\Repositories;

use App\Http\Admin\V1\Exceptions\CategoryException;
use App\Http\Base\BaseRepository;
use App\Interfaces\NotFoundExceptionInterface;
use App\Models\Category;

/**
 * Class CategoryRepository
 * @package App\Http\Admin\V1\Repositories
 */
class CategoryRepository extends BaseRepository implements NotFoundExceptionInterface
{

    /**
     * CategoryRepository constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->model = $category;
    }


    /**
     * @return mixed|void
     * @throws CategoryException
     */
    public function notFoundException()
    {
        throw new CategoryException('该分类不存在!', $this->httpNotFound);
    }


    /**
     * @param array $column
     * @param string $order
     * @param string $sort
     * @return mixed
     */
    public function getCategoryList($column = ['*'], $order = 'id', $sort = 'desc')
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
    public function getCategoryPaginate($where = [], $column = ['*'], $order = 'id', $sort = 'desc')
    {
        return $this->model->where($where)->select($column)->orderBy($order, $sort)->paginate();
    }


    /**
     * @param $id
     * @param array $column
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findCategoryById($id, $column = ['*'], $with = [])
    {
        return $this->findOneOrFail($id, $this, $column, $with);
    }


}
