<?php


namespace App\Http\Validators;


use App\Interfaces\ValidatorInterface;

/**
 * Class SkuCostPriceValidator
 * @package App\Http\Validators
 */
class SkuCostPriceValidator implements ValidatorInterface
{

    /**
     * @param $attribute
     * @param $cost_price
     * @param $parameters
     * @param $validator
     * @return bool
     * 判断进价是否大于等于售价
     */
    public function validate($attribute, $cost_price, $parameters, $validator)
    {
        //$attribute的值为
        // product_sku.sku_list.0.cost_price
        // product_sku.sku_list.1.cost_price
        // 这样子
        $price_key = getStrBetween($attribute, 'product_sku.sku_list.', '.cost_price');

        //如果进价 大于或等于 售价 则验证不通过
        return !($cost_price >= $this->getPriceByKey($validator, $price_key));

    }


    /**
     * @param $validator
     * @param $price_key
     * @return mixed
     */
    private function getPriceByKey($validator, $price_key)
    {
        // 获取提交过来的全部参数
        $params = $validator->getData();

        return $params['product_sku']['sku_list'][$price_key]['price'];
    }

}