<?php


namespace App\Http\Validators;


use App\Interfaces\ValidatorInterface;

/**
 * Class PhoneValidator
 * @package App\Http\Validators
 * 判断手机号码格式
 */
class PhoneValidator implements ValidatorInterface
{

    /**
     * @param $attribute
     * @param $number
     * @param $parameters
     * @param $validator
     * @return bool
     * 判断是否为手机号码
     */
    public function validate($attribute, $number, $parameters, $validator)
    {

        return $this->check($number);

    }


    /**
     * @param $number
     * @return bool
     */
    private function check($number)
    {

        $g = "/^1[345789][0-9]{9}$/";

        if (preg_match($g, $number)) {
            return true;
        }

        return false;
    }

}