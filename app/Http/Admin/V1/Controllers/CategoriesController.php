<?php


namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Repositories\CategoriesRepository;
use App\Http\Admin\V1\Requests\CategoriesRequest;
use App\Http\Admin\V1\Resources\CategoriesResource;
use App\Http\Admin\V1\Services\CategoriesService;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;


class CategoriesController extends ApiController
{

    /**
     * @param Request $request
     * @param CategoriesService $categoriesService
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, CategoriesService $categoriesService)
    {
        return CategoriesResource::collection($categoriesService->getCategoriesPaginate($request->all()));
    }


    /**
     * @param $id
     * @param CategoriesRepository $categoriesRepo
     * @return CategoriesResource
     */
    public function show($id, CategoriesRepository $categoriesRepo)
    {

        $category = $categoriesRepo->findCategoryById($id);

        return new CategoriesResource($category);

    }


    /**
     * @param CategoriesRequest $request
     * @param CategoriesRepository $categoriesRepo
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoriesRequest $request, CategoriesRepository $categoriesRepo)
    {

        $result = $categoriesRepo->create($request->only(['name', 'description']));

        return responseJsonAsCreated($result);
    }


    /**
     * @param $id
     * @param CategoriesRequest $request
     * @param CategoriesRepository $categoriesRepo
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     */
    public function update($id, CategoriesRequest $request, CategoriesRepository $categoriesRepo)
    {

        $categoriesRepo->update($id, $request->only('name', 'description'));

        return responseJsonAsDeleted($categoriesRepo->findCategoryById($id));

    }


    /**
     * @param $id
     * @param CategoriesService $categoriesService
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, CategoriesService $categoriesService)
    {

        $categoriesService->deleteCategory($id);

        return responseJsonAsDeleted();
    }

}