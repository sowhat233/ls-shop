<?php


namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Repositories\CategoryRepository;
use App\Http\Admin\V1\Requests\CategoryRequest;
use App\Http\Admin\V1\Services\CategoryService;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{

    private $name = '分类';

    /**
     * @param Request $request
     * @param CategoryService $categoryService
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, CategoryService $categoryService)
    {
        return $this->responseAsSuccess($categoryService->getCategoryPaginate($request->all()));
    }


    /**
     * @param CategoryRequest $request
     * @param CategoryRepository $categoriesRepo
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request, CategoryRepository $categoriesRepo)
    {

        $result = $categoriesRepo->create($request->only(['name', 'description']));

        return $this->responseAsCreated($result, $this->combineMessage("{$this->name}创建"));
    }


    /**
     * @param $id
     * @param CategoryRepository $categoriesRepo
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id, CategoryRepository $categoriesRepo)
    {
        return $this->responseAsSuccess($categoriesRepo->findCategoryById($id));
    }


    /**
     * @param $id
     * @param CategoryRepository $categoriesRepo
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, CategoryRepository $categoriesRepo)
    {

        $category = $categoriesRepo->findCategoryById($id);

        return $this->responseAsSuccess($category);

    }

    /**
     * @param $id
     * @param CategoryRequest $request
     * @param CategoryRepository $categoriesRepo
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     */
    public function update($id, CategoryRequest $request, CategoryRepository $categoriesRepo)
    {

        $categoriesRepo->update($id, $request->only('name', 'description'));

        return $this->responseAsUpdated($categoriesRepo->findCategoryById($id), $this->combineMessage("{$this->name}编辑"));

    }


    /**
     * @param $id
     * @param CategoryService $categoriesService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy($id, CategoryService $categoriesService)
    {

        $categoriesService->deleteCategory($id);

        return $this->responseAsDeleted($this->combineMessage("{$this->name}删除"));
    }


    /**
     * @param CategoryRepository $categoryRepo
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(CategoryRepository $categoryRepo)
    {
        return $this->responseAsSuccess(['category_list' => $categoryRepo->getCategoryList(['id', 'name'])]);
    }

}