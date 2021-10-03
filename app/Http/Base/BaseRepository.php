<?php

namespace App\Http\Base;


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
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = ['*'], $orderBy = 'id', $sortBy = 'asc')
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->model->find($id);
    }


    /**
     * @param $where
     * @param $value
     * @return mixed
     */
    public function findValue($where, $value)
    {
        return $this->model->where($where)->value($value);
    }


    /**
     * @param $data
     * @return mixed
     */
    public function findBy($data)
    {
        return $this->model->where($data)->get();
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
