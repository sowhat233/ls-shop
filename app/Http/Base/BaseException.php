<?php


namespace App\Http\Base;


use Exception;

class BaseException extends Exception
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json(['message' => $this->message], $this->code);
    }

    /**
     * @param $message
     * @param $e
     * @return mixed
     */
    public function handleErrorMessage($message, $e)
    {

        if ($e !== false) {

            $message = config('app.debug') ? $e->getMessage() : $message;
        }

        return $message;
    }

}