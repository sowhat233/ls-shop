<?php


namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Repositories\ProductRepository;
use App\Http\Admin\V1\Requests\ProductRequest;
use App\Http\Admin\V1\Services\ProductService;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ProductController extends ApiController
{

    private $name = '商品';

    /**
     * @param Request $request
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, ProductService $productService)
    {
        return $this->responseAsSuccess($productService->getProductList($request->all()));
    }


    /**
     * @param $id
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, ProductService $productService)
    {

        $product = $productService->productShow($id);

        return $this->responseAsSuccess($product);
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

        $result = $productService->createProduct($request->only(['product_info', 'product_sku']));

        return $this->responseAsCreated($this->constituteMessage("{$this->name}创建"), $result);

    }


    /**
     * @param $id
     * @param ProductRequest $request
     * @param ProductService $productService
     * @param ProductRepository $productRepo
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, ProductRequest $request, ProductService $productService, ProductRepository $productRepo)
    {

        $productService->update($id, $request->only(['product_info', 'product_sku']));

        return $this->responseAsSuccess($this->constituteMessage("{$this->name}编辑"), $productRepo->findProductById($id));

    }


    /**
     * @param Request $request
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Common\CommonException
     */
    public function changeStatus(Request $request, ProductService $productService)
    {

        $status = $productService->changeStatus($request->input('id'));

        return $this->responseAsSuccess("{$this->name}已$status!");
    }

}