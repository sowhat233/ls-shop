<?php

namespace App\Http\Common;

use App\Http\Base\BaseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class CommonException extends BaseException
{

    public function __construct($message = "", $previous = null, $code = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR)
    {

        if (!is_null($previous) && config('app.debug')) {
            $message = $previous->getMessage();
        }

        parent::__construct($message, $code, $previous);
    }

}