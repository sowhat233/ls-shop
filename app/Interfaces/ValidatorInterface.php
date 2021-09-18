<?php

namespace App\Interfaces;

interface ValidatorInterface
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return mixed
     */
    public function validate($attribute, $value, $parameters, $validator);
}