<?php

// Admin 路由
use think\facade\Route;

Route::group(function(){
    // 获取组件 Json
    Route::get('com/get/:code','/Com/getConfig');

    // 数据增删改查接口
    Route::get('api/:code','/Api/get');         // 通用数据获取接口
    Route::post('api/sort/:code','/Api/sort');  // 保存排序
    Route::post('api/:code','/Api/save');       // 通用保存接口
    Route::delete('api/:code/:id','/Api/del'); // 通用删除接口

    Route::any('upload','/Upload/run'); // 通用文件上传接口

    // 组件路由，根据 code 返回组件编码
    Route::view('view/[:code]','amis/amis_app');
    
    // 根目录 跳转到 后台首页
    Route::get('/',function(){
        return redirect('view/index');
    });


});
