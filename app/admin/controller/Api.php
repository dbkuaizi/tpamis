<?php

namespace app\admin\controller;
use app\Request;
use think\facade\Config;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\View;

/**
 * 后端通用数据接口
 * Class Index
 * @package app\admin\controller
 */
class Api extends BaseController
{
    // 通用数据获取接口
    public function get($code)
    {
        $res_data = ['status' => 0 ,'msg' => '数据获取成功','data' => []];
        // 拿到数据接口的配置信息
        $api_data = Db::table('sys_api')->where('v_Code',$code)->find();

        switch ($api_data['v_Type']) {
            case 'find': // 单条数据
                break;
            case 'list': // 多条数据
                $res_data['data'] = $this->getListData($api_data);
                break;
            case 'tree':
                break;

        }


        return $res_data;
    }

    /**
     * 数据保存接口
     */
    public function save(Request $request,$code,$id = '')
    {
        $ret_data = ['status' => 1 ,'msg' => '保存失败'];
        $com_data = Db::table('sys_instances')->where('v_Code',$code)->field('v_Tables,v_PriField')->find();
        $table_name = explode(',',$com_data['v_Tables'])[0];
        $data = $request->post();
        if ($id){
            $pri_key = $com_data['v_PriField'];
            $res = Db::table($table_name)->where([$pri_key => $id])->update($data);
        } else {
            $res = Db::table($table_name)->insert($data);
        }
        if ($res) {
            $ret_data = ['status' => 0 ,'msg' => '保存成功'];
        }
        return $ret_data;

    }

    /**
     * 列表获取接口
     */

    public function getListData($api_data)
    {
        $sql = View::display($api_data['v_SQLString']);
        $total_sql = View::display($api_data['v_SQLTotal']);
        return [
            'items' => Db::query($sql),
            'total' => Db::query($total_sql),
        ];
    }

    public function form_page(Request $request)
    {
        $data['name'] = '张三';
        $data['email'] = 'zhangsan@qq.com';
        return $this->success('数据获取成功',$data);

    }
}