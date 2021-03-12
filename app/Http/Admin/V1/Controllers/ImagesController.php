<?php


namespace App\Http\Admin\V1\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Admin\V1\Requests\ImagesRequest;
use App\Http\Admin\V1\Services\ImagesUploadService;

class ImagesController extends ApiController
{

    /**
     * @param ImagesRequest $request
     * @param ImagesUploadService $uploader
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Admin\V1\Exceptions\ImgException
     */
    public function store(ImagesRequest $request, ImagesUploadService $uploader)
    {

        $result = $uploader->save($request->images);

        return responseJsonAsCreated($result, '上传成功!');

    }

}