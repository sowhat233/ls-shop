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
     * @throws \Throwable
     */
    public function store($params)
    {

        $product_info = $params['product_info'];
        $product_sku  = $params['product_sku'];

        //开启事务
        DB::beginTransaction();

        try {

            //添加product数据
            $product = $this->productRepo->create($this->getProductColumnData($product_info, $product_sku));

            //如果是多规格
            if ($product_info['is_multiple_spec'] == ProductEnums::IsMultipleSpec) {

                //添加sku数据
                $this->skuRepo->insert($this->getProductSkuColumnData($product_sku['sku_list'], $product->id));

            }

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
    public function getProductList($params, $column = ['id', 'name', 'is_launched', 'sales', 'category_id', 'price', 'cost_price', 'is_multiple_spec', 'stock'])
    {

        $product_list = $this->productRepo->getProductPaginate($this->handleProductIndexParams($params), $column)->toArray();;

        return $this->assemblyProductList($product_list);

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
                'name', 'like', $params['query'] . '%',
            ];
        }

        return $where;
    }

    /**
     * @param $product_id
     * @return mixed
     */
    public function show($product_id)
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
        $data['status_name']   = ProductEnums::ProductStatusName[$data['is_launched']];
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
     * @param bool $create
     * @return mixed
     */
    private function getProductColumnData($info, $sku, $create = true)
    {

        //如果是多规格
        if ($info['is_multiple_spec'] == ProductEnums::IsMultipleSpec) {

            $product['is_multiple_spec'] = ProductEnums::IsMultipleSpec;
            $product['stock']            = 0; //多规格情况下 库存暂定为0
            $product['price']            = $this->getSkuLowestPrice($sku['sku_list']);//多规格情况下 price就是sku的最低价格
            $product['cost_price']       = 0; //多规格情况下 进价暂定为0.00
            $product['spec_items']       = json_encode($sku['spec_items'], true); //参数列表

        }
        else {

            $product['is_multiple_spec'] = ProductEnums::NotMultipleSpec; //多规格情况下 库存暂定为0
            $product['stock']            = $info['stock']; //库存
            $product['price']            = $info['price'];//售价
            $product['cost_price']       = $info['cost_price'];//进价
            $product['spec_items']       = null; //单规格 这个参数为null

        }

        $product['category_id'] = $info['category_id'];
        $product['name']        = $info['name'];
        $product['image']       = $info['image']; //主图
        $product['description'] = $info['description']; //简介
        $product['detail']      = $info['detail']; //详情
        $product['is_launched'] = $info['is_launched']; //是否上架
        $product['carousels']   = implode(',', $info['carousels']); //轮播图

        if ($create) {

            $product['sales']      = 0; //销量
            $product['comments']   = 0; //评论量
            $product['created_at'] = time(); //创建时间
        }


        return $product;

    }

    /**
     * @param $sku_list
     * @param $product_id
     * @return array
     */
    private function getProductSkuColumnData($sku_list, $product_id)
    {

        $data = [];

        foreach ($sku_list as $key => $value) {

            $data[$key]['product_id'] = $product_id;
            $data[$key]['price']      = $value['price'];
            $data[$key]['cost_price'] = $value['cost_price'];
            $data[$key]['stock']      = $value['stock'];
            $data[$key]['image']      = $value['image'];
            $data[$key]['attrs']      = json_encode($value['attrs'], true);
            $data[$key]['sales']      = 0;

        }

        return $data;

    }

    /**
     * @param $sku_list
     * @return mixed
     */
    private function getSkuLowestPrice($sku_list)
    {

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

        foreach ($product_list['data'] as $key => &$value) {

            $category_name = $value['category']['category_name'];

            $value['category_name'] = $category_name;

            $value['type'] = ProductEnums::ProductTypeName[$value['is_multiple_spec']];

            //如果是多规格
            if ($value['is_multiple_spec'] == ProductEnums::IsMultipleSpec) {

                //多规格的情况下 库存和销量都是拿所有sku的总和
                $value['stock'] = $this->getSkuSTotalBy($value['sku'], 'stock');

                $value['sales'] = $this->getSkuSTotalBy($value['sku'], 'sales');

                foreach ($value['sku'] as $k => $sku) {

                    //规格属性名
                    $value['sku'][$k]['attrs'] = $this->transformAttrs($sku['attrs']);

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
     * @return array
     * 转数组
     */
    private function transformAttrs($attrs)
    {

        $data = json_decode($attrs, true);

        $result = [];

        foreach ($data as $key => $value) {

            $result[] = $key . '：' . $value;

        }

        return $result;

    }

    /**
     * @param $product_id
     * @return mixed
     * @throws \App\Http\Common\CommonException
     */
    public function changeStatus($product_id)
    {

        $product = $this->productRepo->findProductById($product_id);

        $status = $this->getNewSaleStatus($product['is_launched']);

        $this->productRepo->update($product_id, ['is_launched' => $status]);

        return ProductEnums::ProductStatusName[$status];

    }

    /**
     * @param $sale_status
     * @return int
     */
    private function getNewSaleStatus($sale_status)
    {

        $new_sale_status = ProductEnums::IsLaunched;

        //如果是上架状态
        if ($sale_status == ProductEnums::IsLaunched) {

            //改成下架
            $new_sale_status = ProductEnums::NotLaunched;

        }

        return $new_sale_status;

    }


    /**
     * @param $product_id
     * @param $product_column
     * @param $sku_column
     * @return mixed
     */
    public function handleProductEdit($product_id, $product_column, $sku_column)
    {
        return $this->transformProductEditData($this->productRepo->getProductWithSkuById($product_id, $product_column, $sku_column));
    }


    /**
     * @param $product
     * @return mixed
     */
    private function transformProductEditData($product)
    {

        foreach ($product['sku'] as $key => $value) {

            $value['attrs'] = $data = json_decode($value['attrs'], true);

        }

        return $product;

    }


    /**
     * @param $params
     * @param $product_id
     * @throws ProductException
     * @throws \Throwable
     */
    public function update($params, $product_id)
    {

        $product_info = $params['product_info'];
        $product_sku  = $params['product_sku'];

        $original_product = $this->productRepo->getProductWithSkuById($product_id, ['id', 'is_multiple_spec'], ['id', 'product_id', 'attrs']);

        //开启事务
        DB::beginTransaction();

        try {

            //更新product数据
            $this->productRepo->update($product_id, $this->getProductColumnData($product_info, $product_sku, false));

            //规格类型发生变化
            if ($original_product['is_multiple_spec'] != $product_info['is_multiple_spec']) {


                //如果是多规格的情况 那就是单规格转多规格
                if ($product_info['is_multiple_spec'] == ProductEnums::IsMultipleSpec) {

                    //添加sku数据
                    $this->skuRepo->insert($this->getProductSkuColumnData($product_sku['sku_list'], $product_id));

                }//否则就是多规格转单规格 需要删除对应的sku
                else {

                    //删除对应的sku
                    $this->deleteSkuByIds(array_column($original_product->toArray()['sku'], 'id'));

                }

            }//规格类型没有发生变化
            else {

                //如果是多规格的情况
                if ($product_info['is_multiple_spec'] == ProductEnums::IsMultipleSpec) {

                    //删除对应的sku
                    $this->deleteSkuByIds(array_column($original_product->toArray()['sku'], 'id'));

                    //重新添加sku数据
                    $this->skuRepo->insert($this->getProductSkuColumnData($product_sku['sku_list'], $product_id));

                }

                //单规格情况下不需要考虑sku的问题
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            throw new ProductException('更新失败!');

        }

    }


    /**
     * @param $ids
     * @throws \App\Http\Common\CommonException
     */
    private function deleteSkuByIds($ids)
    {

        foreach ($ids as $key => $id) {

            $this->skuRepo->delete($id);
        }
        
    }


    /**
     * @param $product_id
     * @throws ProductException
     * @throws \Throwable
     */
    public function delete($product_id)
    {

        $product = $this->productRepo->getProductWithSkuById($product_id, ['id'], ['id', 'product_id'])->toArray();

        //开启事务
        DB::beginTransaction();

        try {

            //删除对应的sku
            $this->deleteSkuByIds(array_column($product['sku'], 'id'));

            $product->delete();

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            throw new ProductException('删除失败!');
        }

    }


}
