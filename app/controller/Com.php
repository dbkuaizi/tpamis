<?php

namespace app\controller;
use think\facade\Config;
use app\BaseController;
use app\Request;
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
            $result = Db::table('sys_com')->where('code','sys_com_empty')->find();
            $result['body'] = str_replace('[code]',$code, $result['body']);
        }

        $body = View::display($result['body'],$result);
        return response($body);
    }

    /**
     *  组件管理列表
     */
    public function list(Request $request)
    {
        $ret_data = [];
        $page = $request->get('page', 1);
        $size = $request->get('perPage', 15);
        // 查询条件
        $where = ['delete_time'=>null];

        // 如果有传递组件类型 就过滤组件类型
        if($request->has('type')) {
            $where['type'] = $request->get('type');
        }

        $without_field = 'body';
        // 先查找第一层数据
        $query = Db::table('sys_com')->where('parent_code', '')->where($where);
        $com_list = $query->page($page, $size)->withoutField($without_field)->select()->toArray();
        $com_total = $query->count();

        // 查找第二层数据
        $com_codes = array_column($com_list, 'code');
        $com_sub_list = Db::table('sys_com')->whereIn('parent_code', $com_codes)->where($where)
            ->withoutField($without_field)->select()->toArray();
        $com_list = array_merge($com_list,$com_sub_list);

        // 拼接树
        $tree_config = ['parent_key' => 'parent_code','primary_key'=>'code','root_index' => ''];
        $com_tree = \Tree::makeTree($com_list,$tree_config);
        $ret_data = ['items' => $com_tree, 'total' => $com_total];
        return $this->success('获取成功',$ret_data);
    }
}