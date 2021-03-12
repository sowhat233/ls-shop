<?php


namespace App\Http\Admin\V1\Services;


use App\Enums\ProductEnums;
use App\Http\Admin\V1\Exceptions\ProductException;
use App\Http\Admin\V1\Repositories\ProductRepository;
use App\Http\Admin\V1\Repositories\SkuRepository;
use DB;

class ProductService
{
    private $productRepo;
    private $skuRepo;


    public function __construct(ProductRepository $productRepository, SkuRepository $skuRepository)
    {
        $this->productRepo = $productRepository;
        $this->skuRepo     = $skuRepository;
    }

    /**
     * @param $params
     * @return mixed
     * @throws ProductException
     */
    public function createProduct($params)
    {

        $insert_product = $this->getProductInsertData($params['product_info'], $params['product_sku']);

        //开启事务
        DB::beginTransaction();

        try {

            $product = $this->productRepo->create($insert_product);

            $product_sku = $this->getProductSkuInsertData($params['product_sku']['sku_list'], $product->id);

            $this->skuRepo->insert($product_sku);

            DB::commit();

            return $product;

        } catch (\Exception $e) {

            DB::rollBack();

            throw new ProductException('添加失败!');

        }


    }

    /**
     * @param $params
     * @param array $column
     * @return mixed
     */
    public function getProductList($params, $column = ['id', 'name', 'sale_status', 'sales', 'category_id', 'price', 'is_enable_spec', 'stock'])
    {

        $where = $this->handleProductIndexParams($params);

        $data = $this->productRepo->getProductPaginate($where, $column);

        return $this->assemblyProductList($data);

    }


    /**
     * @param $params
     * @return array
     */
    public function handleProductIndexParams($params)
    {

        $where = [];

        //查询name
        if (isset($params['query'])) {

            $where[] = [
                'name', 'like', $params['query'].'%',
            ];
        }

        return $where;
    }

    /**
     * @param $product_id
     * @return mixed
     */
    public function productShow($product_id)
    {

        $data = $this->productRepo->findProductById($product_id, ['*'], ['category', 'sku']);

        $product = $this->transformProductShowData($data);

        return $product;

    }

    /**
     * @param $data
     * @return mixed
     */
    private function transformProductShowData($data)
    {

        $data['category_name'] = $data['category']['name'];
        $data['status_name']   = ProductEnums::ProductStatusName[$data['sale_status']];
        $data['carousels']     = explode(',', $data['carousels']);
        $data['attrs_title']   = array_keys(json_decode($data['spec_items'], true));


        if ($data['sku'] !== null) {

            foreach ($data['sku'] as $key => $value) {
                $value['attrs'] = json_decode($value['attrs'], true);
            }

        }


        return $data;
    }

    /**
     * @param $info
     * @param $sku
     * @return mixed
     */
    private function getProductInsertData($info, $sku)
    {

        //如果启用多规格
        if ($sku['is_enable_spec'] == ProductEnums::EnableSpec) {

            //启用多规格 price就是sku的最低价格
            $price = $this->getSkuLowestPrice($sku['sku_list']);

            $product['is_enable_spec'] = ProductEnums::EnableSpec;
            $product['stock']          = 0; //启用多规格 这个库存就不处理了
            $product['price']          = $price; //sku最低价格
            $product['spec_items']    = json_encode($sku['spec_list'], true); //参数列表

        }
        else {

            $product['is_enable_spec'] = ProductEnums::NotEnableSpec;
            $product['stock']          = $sku['stock']; //库存
            $product['price']          = $sku['price'];
            $product['spec_items']    = null; //不启用多规格 这个参数为null

        }


        $product['category_id'] = $info['category_id'];
        $product['name']        = $info['name'];
        $product['image']       = $info['image']; //主图
        $product['description'] = $info['description']; //简介
        $product['detail']      = $info['detail']; //详情
        $product['sale_status'] = $info['sale_status']; //是否上架
        $product['carousels']   = implode(',', $info['carousels']); //轮播图
        $product['sales']       = 0; //销量
        $product['comments']    = 0; //评论量
        $product['created_at']  = time(); //创建时间

        return $product;

    }

    /**
     * @param $sku_list
     * @param $product_id
     * @return array
     */
    private function getProductSkuInsertData($sku_list, $product_id)
    {

        $insert_arr = [];

        foreach ($sku_list as $key => $value) {

            $insert_arr[$key]['product_id'] = $product_id;
            $insert_arr[$key]['price']      = $value['price'];
            $insert_arr[$key]['cost_price'] = $value['cost_price'];
            $insert_arr[$key]['stock']      = $value['stock'];
            $insert_arr[$key]['image']      = $value['image'];
            $insert_arr[$key]['attrs']      = json_encode($value['attrs'], true);
            $insert_arr[$key]['sales']      = 0;

        }

        return $insert_arr;

    }

    /**
     * @param $sku_list
     * @return mixed
     */
    private function getSkuLowestPrice($sku_list)
    {

        //此处用collect处理会简洁些

        $price_arr = [];

        foreach ($sku_list as $key => $value) {

            $price_arr[] = $value['price'];
        }

        return min($price_arr);

    }

    /**
     * @param $product_list
     * @return mixed
     */
    private function assemblyProductList($product_list)
    {

        foreach ($product_list as $key => $value) {

            $category_name = $value['category']['category_name'];

            $value['category_name'] = $category_name;

            $value['key'] = $value['id'];

            //如果启用了多规格
            if ($value['is_enable_spec'] == ProductEnums::EnableSpec) {

                //启用多规格的情况下 库存和销量都是拿所有sku的总和

                $sku_list = $value['sku'];

                $value['stock'] = $this->getSkuSTotalBy($sku_list, 'stock');

                $value['sales'] = $this->getSkuSTotalBy($sku_list, 'sales');

                foreach ($sku_list as $k => $sku) {

                    $sku['key'] = $value['id'].'-'.++$k;

                    //json转字符串
                    $sku['name'] = $this->transformAttrs($sku['attrs']);

                    //分类名称
                    $sku['category_name'] = $category_name;

                }

            }


        }

        return $product_list;

    }

    /**
     * @param $sku_list
     * @param $field
     * @return float|int
     */
    private function getSkuSTotalBy($sku_list, $field)
    {

        $arr = [];

        foreach ($sku_list as $key => $value) {

            $arr[] = $value[$field];

        }

        return array_sum($arr);

    }

    /**
     * @param $attrs
     * @return string
     */
    private function transformAttrs($attrs)
    {

        $data = json_decode($attrs, true);

        $result = [];

        foreach ($data as $key => $value) {

            $result[] = $key.'：'.$value;

        }

        return implode('，', $result);


        /*
          $attrs = {"内存": "128G", "颜色": "白色"}

          转换成

          $result = '内存：128G,颜色：白色';

         */

    }

    /**
     * @param $product_id
     * @return ProductRepository|ProductRepository[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @throws ProductException
     */
    public function changeStatus($product_id)
    {

        $product = $this->productRepo->findProductById($product_id);

        $this->checkSaleRequirement($product);

        $new_sale_status = $this->getNewSaleStatus($product['sale_status']);

        $this->productRepo->updat($product_id, ['sale_status' => $new_sale_status]);

        return $this->productRepo->findProductById($product_id);

    }

    /**
     * @param $sale_status
     * @return int
     */
    private function getNewSaleStatus($sale_status)
    {

        $new_sale_status = ProductEnums::on_sale;

        //如果是上架状态
        if ($sale_status === ProductEnums::on_sale) {

            //改成下架
            $new_sale_status = ProductEnums::not_on_sale;

        }

        return $new_sale_status;

    }

    /**
     * @param $product
     * @throws ProductException
     */
    private function checkSaleRequirement($product)
    {

        //检查分类是否设置
        if ($product['category_id'] === null) {

            throw new ProductException('分类未设置,无法上架！');

        }

    }

}
