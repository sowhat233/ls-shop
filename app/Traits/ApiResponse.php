<?php

namespace App\Traits;
/**
 * 封装返回的统一消息 目前用不上
 */

use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Response;

trait ApiResponse
{
    /**
     * @var int
     */
    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {

        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return mixed
     */
    public function respond($data, $header = [])
    {

        return Response::json($data, $this->getStatusCode(), $header);
    }

    /**
     * @param $msg
     * @param array $data
     * @param null $code
     * @return mixed
     */
    public function status($msg, array $data, $code = null)
    {

        if ($code) {
            $this->setStatusCode($code);
        }

        $status = [
            'msg'  => $msg,
            'code' => $this->statusCode,
        ];

        $data = array_merge($status, $data);

        return $this->respond($data);

    }

    /**
     * @param $message
     * @param int $code
     * @param string $status
     * @return mixed
     */
    public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error')
    {

        return $this->setStatusCode($code)->message($message, $status);
    }


    /**
     * @param $message
     * @param string $status
     * @return mixed
     */
    public function message($message, $status = "success")
    {

        return $this->status($status, [
            'message' => $message,
        ]);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function internalError($message = "Internal Error!")
    {

        return $this->failed($message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function created($message = "created success", $data = [], $extFields = [])
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
                    ->respond($data);
    }

    /**
     * @param $data
     * @param string $msg
     * @return mixed
     */
    public function success($data, $msg = "success")
    {

        return $this->status($msg, compact('data'));
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function notFound($message = 'Not Found!')
    {
        return $this->failed($message, Foundationresponse::HTTP_NOT_FOUND);
    }
}
