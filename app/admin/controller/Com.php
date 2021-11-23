<?php

namespace app\admin\controller;
use think\facade\Config;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\View;

/**
 * Class Com 用于提供组件的基本操作功能
 * @package app\admin\controller
 */
class Com extends BaseController
{
    // 获取组件配置
    public function getConfig($code)
    {
        $result = Db::table('sys_com')->where('code',$code)->find();

        // 如果查询为空 就使用不存在的 实例
        if (empty($result)) {
            $result = Db::table('sys_com')->where('code','com_ins_empty')->find();
            $result['body'] = str_replace('[code]',$code, $result['body']);
        }

        $body = View::display($result['body'],$result);
        return response($body);
    }
}