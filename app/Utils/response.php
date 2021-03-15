<?php

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * 创建成功使用的状态码  201
 *
 * @param array $data
 * @param string $message
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsCreated($message = 'created success', $data = [], $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_CREATED, $ext_fields);
}

/**
 *  修改成功  204
 *
 * @param string $message
 * @param null $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsDeleted($data = null, $message = 'deleted success', $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_NO_CONTENT, $ext_fields);
}

/**
 * 表单验证错误 422
 *
 * @param string $message
 * @param array $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsBadRequest($message = 'bad request', $data = [], $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_UNPROCESSABLE_ENTITY, $ext_fields);
}


/**
 * 身份验证失败 401
 *
 * @param string $message
 * @param array $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsUnAuthorized($message = 'un authorized', $data = [], $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_UNAUTHORIZED, $ext_fields);
}


/**
 * 用户身份过期, 需重新登录 402
 *
 * @param string $message
 * @param array $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsAccountExpired($message = 'account expired', $data = [], $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_PAYMENT_REQUIRED, $ext_fields);
}

/**
 * 权限不足 403
 *
 * @param string $message
 * @param array $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsForbidden($message = 'forbidden', $data = [], $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_FORBIDDEN, $ext_fields);
}


/**
 * 未找到 404
 *
 * @param string $message
 * @param array $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsNoFound($message = 'no found', $data = [], $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_NOT_FOUND, $ext_fields);
}


/**
 * 服务器未知错误 500
 *
 * @param string $message
 * @param array $data
 * @param array $ext_fields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsServerError($message = 'server error', $data = [], $ext_fields = [])
{
    return responseJson($data, $message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR, $ext_fields);
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
