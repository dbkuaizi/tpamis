<?php
// Admin 路由
use think\facade\Route;
use think\Config;
Route::get('login',function (){
    return '登录';
});
Route::group(function(){
    // 组件通用接口
    Route::get('com/:code','/Com/getConfig');
    Route::any('api/form_page','/Api/form_page'); // 通用数据获取接口
    Route::get('api/:code','/Api/get'); // 通用数据获取接口
    Route::post('api/:code/[:id]','/Api/save'); // 通用修改/保存
    // 所有get请求 返回页面
    Route::get('sys/[:code]',function(){
        return view('amis/amis_app');
    });
    Route::get('/',function(){
       return redirect('/admin/sys/index');
    });


})->prefix('admin');
