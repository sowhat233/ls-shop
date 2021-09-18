<?php


namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Repositories\CategoryRepository;
use App\Http\Admin\V1\Requests\ProductRequest;
use App\Http\Admin\V1\Resources\CategoryResource;
use App\Http\Admin\V1\Resources\ProductResource;
use App\Http\Admin\V1\Services\ProductService;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ProductController extends ApiController
{

    /**
     * @param Request $request
     * @param ProductService $productService
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, ProductService $productService)
    {
        return ProductResource::collection($productService->getProductList($request->all()));
    }


    /**
     * @param $id
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, ProductService $productService)
    {

        $product = $productService->productShow($id);

        return response()->json($product);
    }


    /**
     * @param ProductRequest $request
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Admin\V1\Exceptions\ProductException
     * @throws \Throwable
     */
    public function store(ProductRequest $request, ProductService $productService)
    {

//        $result = $productService->createProduct($request->only(['product_info', 'product_sku']));

        return responseJsonAsCreated($result = []);

    }


    /**
     * @param CategoryRepository $categoryRepo
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function categoryList(CategoryRepository $categoryRepo)
    {
        return CategoryResource::collection($categoryRepo->getCategoryList(['id', 'name']));
    }


    /**
     * @param Request $request
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Admin\V1\Exceptions\ProductException
     */
    public function changeStatus(Request $request, ProductService $productService)
    {

        $result = $productService->changeStatus($request->only('id'));

        return responseJsonAsDeleted($result, '修改成功！');
    }
}