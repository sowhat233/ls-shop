<?php

namespace App\Http\Admin\V1\Exceptions;

use App\Http\Base\BaseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class ImgException extends BaseException
{

    /**
     * ImgException constructor.
     * @param string $message
     * @param bool $e
     * @param int $code
     */
    public function __construct($message = "", $e = false, $code = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR)
    {
        parent::__construct($this->handleErrorMessage($message, $e), $code);
    }

}