<?php


namespace App\Http\Admin\V1\Controllers;


use App\Http\Admin\V1\Repositories\ProductRepository;
use App\Http\Admin\V1\Repositories\CategoryRepository;
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

        $product = $productService->showProduct($id);

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
        return $this->responseAsCreated($productService->createProduct($request->only(['product_info', 'product_sku'])), $this->combineMessage("{$this->name}创建"));
    }


    /**
     * @param $id
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id, ProductService $productService)
    {
        $product_column = ['id', 'name', 'category_id', 'description', 'detail', 'image', 'carousels', 'stock', 'price', 'cost_price', 'is_multiple_spec'];
        $sku_column     = ['product_id', 'price', 'cost_price', 'stock', 'image', 'attrs'];
        return $this->responseAsSuccess($productService->handleProductEdit($id, $product_column, $sku_column));
    }


    /**
     * @param $id
     * @param ProductRequest $request
     * @param ProductService $productService
     * @param ProductRepository $productRepo
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Admin\V1\Exceptions\ProductException
     */
    public function update($id, ProductRequest $request, ProductService $productService, ProductRepository $productRepo)
    {

        $productService->updateProduct($request->only(['product_info', 'product_sku']),$id);

        return $this->responseAsSuccess($productRepo->findProductById($id), $this->combineMessage("{$this->name}编辑"));

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

        return $this->responseAsSuccess([], "{$this->name}已$status!");
    }

}