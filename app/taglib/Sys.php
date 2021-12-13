<?php

namespace app\taglib;
use think\Exception;
use think\facade\Db;
use think\template\TagLib;

// 系统标签库
class Sys extends TagLib
{
    // 自定义标签
    protected $tags = [
        'op' => ['attr' => 'url','close' => 0],
    ];

    // 判断是否有权限
    public function tagOp($tag)
    {
        return true;
    }

}