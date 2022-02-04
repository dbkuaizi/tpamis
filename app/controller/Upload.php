<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\SysUpload;
use app\Request;
use think\facade\Config;
use think\facade\Filesystem;

// 通用上传接口
class Upload extends BaseController
{
    public function run(Request $request)
    {
        $type = $request->get('type','uplodas');

        // 如果上传对应的方法存在,就使用方法存储
        if(method_exists($this,$type))
        {
            // 该方法返回一个字符串格式的路径
            $path = $this->$type($request); 

        } else { 
        // 如果不存在，就将 type 当作一个目录    
            $file = request()->file('file');
            $path = '/storage/' . Filesystem::putFile($type,$file);
        }
        
        
        // 将上传文件写入附件表
        $sysUploadModel = new SysUpload();
        $sysUploadModel->uid = $request->uid;
        $sysUploadModel->mime = $file->getMime();
        $sysUploadModel->md5 = $file->md5();
        $sysUploadModel->path = $path;
        $sysUploadModel->size = $file->getSize();
        $sysUploadModel->upload_url = str_replace($request->domain(),'',$request->header('referer'));
        $sysUploadModel->type = $type;
        $sysUploadModel->upload_time = date("Y-m-d H:i:s");
        $sysUploadModel->name = $file->getOriginalName();
        $sysUploadModel->save();

        // 返回文件
        return $this->success('上传成功',['value' => $path]);
    }

}
