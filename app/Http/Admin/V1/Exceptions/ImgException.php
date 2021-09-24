<?php

namespace App\Http\Admin\V1\Exceptions;

use App\Http\Base\BaseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class ImgException extends BaseException
{

    /**
     * ImgException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message = "", $code = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR)
    {
        parent::__construct($message, $code);
    }

}