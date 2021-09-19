<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * 封装响应返回
 * Trait ApiResponse
 * @package App\Common\traits
 *
 */
trait  ApiResponse
{

    protected $code = FoundationResponse::HTTP_OK;

    protected $message = 'ok';

    protected $data = [];

    protected $expand = [];


    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }


    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }


    /**
     * @param $data
     * @return $this
     */
    public function setReturnData($data)
    {
        $this->data = $data;
        return $this;
    }


    /**
     * @param $expand
     * @return $this
     */
    public function setExpand($expand)
    {
        $this->expand = $expand;
        return $this;
    }


    /**
     * @param $message
     * @param $data
     * @param $expand
     * @return $this
     */
    public function setResponseData($message, $data, $expand)
    {

        $this->setMessage($message)->setReturnData($data)->setExpand($expand);

        return $this;
    }


    /**
     * @param $message
     * @return string
     */
    public function constituteMessage($message)
    {
        return $message.'成功!';
    }


    /**
     * 请求成功 200
     * @param array $data
     * @param $message
     * @param array $expand
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseAsSuccess($data = [], $message = 'success', $expand = [])
    {

        return $this->setCode(FoundationResponse::HTTP_OK)
                    ->setResponseData($message, $data, $expand)
                    ->responseJson();
    }


    /**
     * 创建成功  201
     * @param array $data
     * @param string $message
     * @param array $expand
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseAsCreated($data = [], $message = 'created success', $expand = [])
    {

        return $this->setCode(FoundationResponse::HTTP_CREATED)
                    ->setResponseData($message, $data, $expand)
                    ->responseJson();
    }


    /**
     * 删除成功  204
     * @param $message
     * @param array $data
     * @param array $expand
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseAsDeleted($message, $data = [], $expand = [])
    {

        return $this->setCode(FoundationResponse::HTTP_NO_CONTENT)
                    ->setResponseData($message, $data, $expand)
                    ->responseJson();
    }


    /**
     * 响应返回
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseJson()
    {
        $response = ['code' => $this->code, 'message' => $this->message, 'data' => $this->data];

        return response()->json(array_merge($response, $this->expand));
    }


}
