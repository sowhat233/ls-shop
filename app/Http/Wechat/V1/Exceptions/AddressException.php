<?php

namespace App\Http\Wechat\V1\Exceptions;

use App\Http\Base\BaseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class AddressException extends BaseException
{

    public function __construct($message = "", $code = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}