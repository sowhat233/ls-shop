<?php

namespace App\Http\Base;


use App\Exceptions\InternalException;
use App\Http\Common\CommonException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class BaseRepository
{

    protected $model;

    protected $httpNotFound = FoundationResponse::HTTP_NOT_FOUND;


    /**
     * BaseRepository constructor.
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }


    /**
     * @param array $where
     * @param array $column
     * @param string $order
     * @param string $sort
     * @return mixed
     */
    public function first($where = [], $column = ['*'], $order = 'id', $sort = 'desc')
    {
        return $this->model->where($where)->order($order, $sort)->select($column)->first();
    }


    /**
     * @param $id
     * @param $repository
     * @param $column
     * @param $with
     * @param array $where
     * @return mixed
     */
    public function findOneOrFail($id, $repository, $column, $with, $where = [])
    {

        try {

            return $this->model->where($where)->with($with)->select($column)->findOrFail($id);

        } catch (ModelNotFoundException $e) {

            $repository->notFoundException();

        }
    }


    /**
     * 减库存
     * @param $id
     * @param $amount
     * @return mixed
     * @throws CommonException
     */
    public function decreaseStock($id, $amount)
    {

        if ($amount < 0) {
            throw new CommonException('减库存不能小于0!');
        }

        return $this->model->where('id', $id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }


    /**
     * @param $id
     * @param $amount
     * @return mixed
     * @throws CommonException
     */
    public function incrementStock($id, $amount)
    {

        if ($amount < 0) {

            throw new CommonException('加库存不能小于0!');
        }

        $this->model->where('id', $id)->increment('stock', $amount);
    }


    /**
     * @param array $columns
     * @param string $order
     * @param string $sort
     * @return mixed
     */
    public function all($columns = ['*'], $order = 'id', $sort = 'desc')
    {
        return $this->model->orderBy($order, $sort)->get($columns);
    }


    /**
     * @param $where
     * @param $value
     * @return mixed
     */
    public function findValue($where, $value = 'id')
    {
        return $this->model->where($where)->value($value);
    }


    /**
     * @param $data
     * @return mixed
     */
    public function insertGetId($data)
    {
        return $this->model->insertGetId($data);
    }


    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return $this->model->create($data);
    }


    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        return $this->model->insert($data);
    }


    /**
     * @param $value
     * @param $data
     * @param string $column
     * @return mixed
     * @throws CommonException
     */
    public function update($value, $data, $column = 'id')
    {

        $result = $this->model->where($column, $value)->update($data);

        if ($result === 0) {

            throw new CommonException('更新失败!');
        }

        return $result;

    }


    /**
     * @param $id
     * @return mixed
     * @throws CommonException
     */
    public function delete($id)
    {

        $result = $this->model->where('id', $id)->delete();

        if (!$result) {

            throw new CommonException('删除失败!');
        }

        return $result;
    }
}
