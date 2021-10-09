<?php

namespace App\Http\Wechat\V1\Exceptions;

use App\Http\Base\BaseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class TokenException extends BaseException
{
    
    /**
     * 会抛出403异常
     * TokenException constructor.
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct($message = "", $code = FoundationResponse::HTTP_UNAUTHORIZED, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}