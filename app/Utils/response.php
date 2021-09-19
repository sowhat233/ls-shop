<?php

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * 创建成功  201
 *
 * @param array $data
 * @param string $message
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsCreated($data = [], $message = 'created success', $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_CREATED, $ext_fields);
}

/**
 *  删除成功  204
 *
 * @param string $message
 * @param null $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsDeleted($data = [], $message = 'deleted success', $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_NO_CONTENT, $ext_fields);
}

/**
 * 正常状态 200
 *
 * @param array $data
 * @param int $code
 * @param string $message
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJson($data = [], $message = 'success', $code = FoundationResponse::HTTP_OK, $ext_fields = [])
{
    $response_data = compact('code', 'message', 'data');
    $response_data = array_merge($response_data, $ext_fields);

    return response()->json($response_data);
}
