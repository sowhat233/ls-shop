<?php

namespace App\Exceptions;

use App\Http\Admin\V1\Exceptions\TokenException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param Throwable $exception
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|FoundationResponse
     */
    public function render($request, Throwable $exception)
    {
        $error               = $this->convertExceptionToResponse($exception);
        $response['message'] = '服务器内部错误';

        //401 token相关
        if ($exception instanceof TokenException) {

            $response['message'] = $exception->getMessage();

            return response()->json($response, FoundationResponse::HTTP_UNAUTHORIZED);
        }
        //404 资源不存在
        else if ($exception instanceof NotFoundHttpException) {

            $response['message'] = '404 not found!';

            return response()->json($response, FoundationResponse::HTTP_NOT_FOUND);
        }
        //403 禁止访问
        else if ($exception instanceof HttpException && $exception->getStatusCode() === FoundationResponse::HTTP_FORBIDDEN) {

            $response['message'] = '禁止访问!';

            return response()->json($response, FoundationResponse::HTTP_FORBIDDEN);

        }
        //422 表单验证失败
        else if ($exception instanceof ValidationException) {

            $response['message'] = $exception->validator->getMessageBag()->first();

            return response()->json($response, FoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        else {

            if (config('app.debug')) {

                $response['message'] = empty($exception->getMessage()) ? 'getMessage居然获取不到!' : $exception->getMessage();
            }

        }

        return response()->json($response, $error->getStatusCode());

    }


    protected function convertExceptionToArray(Throwable $e)
    {

        $exception = [
            'message' => config('app.debug') ? $e->getMessage() : '服务器内部错误',
            'code'    => FoundationResponse::HTTP_INTERNAL_SERVER_ERROR, //统一返回500 以便axios拦截
        ];

        return $exception;

    }
}
