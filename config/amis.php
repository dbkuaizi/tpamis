<?php
// +----------------------------------------------------------------------
// | AMIS配置
// +----------------------------------------------------------------------
return [
    // AMIS 配置信息
    'amis_name'     =>  "ThinkAmis",
    // amis 使用模式、目前仅支持 app
    'amis_mode'     =>  'app',
    // amis 主题 antd、cxd、dark(夜间模式)、ang
    'amis_theme'    =>  'antd',
    // login (推荐 50px * 50px)
    "logo"  =>       "/static/logo.png",
    "login" =>      [
        // 登录窗口是否浮动 （默认高度 100%）
        'float'     => true,
        // 登录窗口位置（left、right）
        'align'     => 'left',
        // 登录窗口标题
        'title'     =>  "Think Amis",
        // 登录窗口版权
        'footer'    => "Think Amis 低代码开发框架 © 2021",
        // 登录窗口背景图
        'bg'        => 'url("http://cn.bing.com/th?id=OHR.ChurchillBears_EN-US8757524982_1920x1080.jpg&rf=LaDigue_1920x1080.jpg&pid=hp")',
        // 登录表单宽度，仅pc有效
        'width'     => '400px'
    ],
];