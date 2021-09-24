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


}