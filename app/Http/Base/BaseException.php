<?php


namespace App\Http\Base;


use Exception;

class BaseException extends Exception
{

    public function render()
    {
        return response()->json(['message' => $this->message], $this->code);
    }

}