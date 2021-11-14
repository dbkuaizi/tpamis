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
        $result = Db::table('sys_com')->where('v_Code',$code)->find();

        // 如果查询为空 就使用不存在的 实例
        if (empty($result)) {
            $result = Db::table('sys_com')->where('v_Code','com_ins_empty')->find();
            $result['v_Body'] = str_replace('[code]',$code, $result['v_Body']);
        }

        $body = View::display($result['v_Body'],$result);
        return response($body);
    }
}