<?php

namespace App\Http\Admin\V1\Services;

use App\Http\Admin\V1\Exceptions\ImgException;
use Str;

class ImageUploadService
{

    protected $allowed_ext = ["png", "jpg", 'jpeg'];

    /**
     * @param $file
     * @param bool $folder_name
     * @return array
     * @throws ImgException
     */
    public function save($file, $folder_name = false)
    {

        // 获取文件的后缀名
        $extension = strtolower($file->getClientOriginalExtension());

        // 如果上传图片的后缀不符合 抛出异常
        if ( !in_array($extension, $this->allowed_ext)) {

            throw new ImgException('图片不符合规则!');
        }

        if ( !$folder_name) {

            // 如：uploads/images/avatars/20201203/
            $folder_name = "uploads/images/".date("Ymd", time());

        }

        // 文件具体存储的物理路径
        $upload_path = public_path().'/'.$folder_name;

        // 加前缀 拼接文件名
        $filename = time().'_'.Str::random(5).'.'.$extension;

        // 移动图片
        $file->move($upload_path, $filename);

        return [
            'img_name' => $filename,
            'img_url'  => config('app.url')."/$folder_name/$filename",
        ];
    }


}
