<?php


namespace App\Http\Validators;


use App\Interfaces\ValidatorInterface;

/**
 * Class CostPriceValidator
 * @package App\Http\Validators
 */
class CostPriceValidator implements ValidatorInterface
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

        //如果进价 大于或等于 售价 则验证不通过
        return !($cost_price >= $this->getPrice($validator));

    }


    /**
     * @param $validator
     * @return mixed
     */
    private function getPrice($validator)
    {
        // 获取提交过来的全部参数
        $params = $validator->getData();

        return $params['product_info']['price'];
    }

}