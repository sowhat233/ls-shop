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
     * @param CategoryRequest $request
     * @param CategoryRepository $categoriesRepo
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request, CategoryRepository $categoriesRepo)
    {

        $result = $categoriesRepo->create($request->only(['name', 'description']));

        return $this->responseAsCreated($this->constituteMessage("{$this->name}创建"), $result);
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

        return $this->responseAsSuccess($this->constituteMessage("{$this->name}编辑"), $categoriesRepo->findCategoryById($id));

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

        return $this->responseAsDeleted($this->constituteMessage("{$this->name}删除"));
    }

}