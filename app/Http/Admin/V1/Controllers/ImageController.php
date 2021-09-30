<?php


namespace App\Http\Admin\V1\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Admin\V1\Requests\ImageRequest;
use App\Http\Admin\V1\Services\ImageUploadService;

class ImageController extends ApiController
{

    private $name = '图片';


    /**
     * @param ImageRequest $request
     * @param ImageUploadService $imageUploadService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Http\Admin\V1\Exceptions\ImgException
     */
    public function store(ImageRequest $request, ImageUploadService $imageUploadService)
    {

        $result = $imageUploadService->store($request->image);

        return $this->responseAsCreated($result, $this->combineMessage("{$this->name}上传"));

    }


}