<?php

// Admin 路由
use think\facade\Route;

Route::group(function(){
    // 组件通用接口
    Route::get('com/:code','/Com/getConfig');
    // 数据增删改查接口
    Route::get('api/:code','/Api/get'); // 通用数据获取接口
    Route::post('api/sort/:code','/Api/sort'); // 保存排序
    Route::post('api/:code/[:id]','/Api/save'); // 通用修改/保存
    Route::delete('api/:code/[:id]','/Api/del'); // 通用删除接口

    // 组件路由，根据 code 返回组件编码
    Route::view('sys/[:code]','amis/amis_app');
    // 根目录 跳转到 后台首页
    Route::get('/',function(){
       return redirect('/admin/sys/index');
    });


})->prefix('admin');
