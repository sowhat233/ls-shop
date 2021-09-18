<?php

namespace App\Http\Admin\V1\Exceptions;

use App\Http\Base\BaseException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class CategoryException extends BaseException
{

    /**
     * CategoryException constructor.
     * @param string $message
     * @param bool $e
     * @param int $code
     */
    public function __construct($message = "", $e = false, $code = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR)
    {
        parent::__construct($this->handleErrorMessage($message, $e), $code);
    }

}