<?php

namespace App\Http\Admin\V1\Exceptions;

use App\Http\Base\BaseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class TokenException extends BaseException
{

    /**
     * 默认抛出401异常
     * TokenException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message = "",$code = FoundationResponse::HTTP_UNAUTHORIZED)
    {
        parent::__construct($message, $code);
    }

}