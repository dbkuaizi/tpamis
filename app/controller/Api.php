<?php

namespace app\controller;
use app\Request;
use think\facade\Config;
use app\BaseController;
use think\facade\Db;
use think\facade\View;
use Tree;
use think\facade\Log;
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
        $api_data = Db::table('sys_api')->where('code',$code)->find();

        try {
            switch ($api_data['type']) {
                case 'find': // 单条数据
                    $ret_data = $this->getFindData($api_data);
                    break;
                case 'curd': // 多条数据
                    $ret_data = $this->getCurdData($api_data);
                    break;
                case 'tree':
                    $ret_data = $this->getTreeData($api_data);
                    break;
                case 'list': // 多条数据
                    $ret_data = $this->getListData($api_data);
                    break;
            }
        }catch (\Exception $e) {
            $msg = 'Api Error ['. $code .']: '. $e->getMessage();
            return $this->error($msg);
        }

        return $this->success('获取成功',$ret_data);
    }

    /**
     * 数据保存接口
     */
    public function save(Request $request,$code,$id = '')
    {
        $ret_data = ['status' => 1 ,'msg' => '保存失败'];
        $com_data = Db::table('sys_com')->where('code',$code)->field('tables,pri_field')->find();
        $table_name = explode(',',$com_data['tables'])[0];
        $data = $request->post();
        // 获取操作表字段
        $table_fields = array_column(DB::query("DESC ".$com_data['tables']),'Field');
        if ($id){
            $pri_key = $com_data['pri_field'] ?: 'id';
            // 是否需要补上修改时间
            if (in_array('update_time',$table_fields)) {
                $data['update_time'] =  date('Y-m-d H:i:s');
            }

            $res = Db::table($table_name)->where([$pri_key => $id])->update($data);
        } else {

            // 是否需要补上新增时间
            if (in_array('create_time',$table_fields)) {
                $data['create_time'] =  date('Y-m-d H:i:s');
                $data['update_time'] =  date('Y-m-d H:i:s');
            }

            $res = Db::table($table_name)->insert($data);
        }
        if ($res) {
            $ret_data = ['status' => 0 ,'msg' => '保存成功'];
        }
        return $ret_data;

    }

    // 通用删除接口
    public function del($code,$id = '')
    {
        $com_data = Db::table('sys_com')->where('code',$code)->field('tables,pri_field')->find();
        // 如果组件不存在
        if (!isset($com_data)) return $this->error('组件不存在');
        if (empty($com_data['tables'])) return $this->error('主键数据表不能为空');

        // 判断是否存在软删除字段
       $table_fields = array_column(DB::query("DESC ".$com_data['tables']),'Field');
       $pri_key = $com_data['pri_field'] ?: 'i_Id';
       // 如果存在软删除字段，就走软删除
        $del_where[$pri_key] = $id;

        if (in_array('delete_time',$table_fields)) {
           $del_where['delete_time'] = NULL;
           $ret = Db::table($com_data['tables'])->where($del_where)->update(['delete_time' => date('Y-m-d H:i:s')]);
       } else {
          $ret = Db::table($com_data['tables'])->where($del_where)->delete();
       }
        return $ret ? $this->success('删除成功') : $this->error('删除失败');
    }

    // 列表获取接口
    public function getCurdData($api_data)
    {
        $sql = View::display($api_data['sql_string']);

        if ($api_data['sql_total']) {
            $total_sql = $api_data['sql_total'];
        } else {
            $total_sql = 'SELECT count(*) as total FROM ('.$api_data['sql_string'].') as total_table';
        }
        $total_sql = View::display($total_sql);

        // 如果有传递分页 就拼接
        if ($this->request->has('page','get'))
        {
            $page = $this->request->get('page');
            $size = $this->request->get('perPage');
            $start_index = (($page - 1) * $size);
            $limit_sql = " LIMIT $start_index,$size";
            $sql .= $limit_sql;
        }

        return [
            'items' => Db::query($sql),
            'total' => Db::query($total_sql)[0]['total'],
        ];
    }

    public function getListData($api_data)
    {
        $sql = View::display($api_data['sql_string']);
        return Db::query($sql);
    }
    // 单条获取数据
    public function getFindData($api_data)
    {
        $sql = View::display($api_data['sql_string']);
        return  Db::query($sql)[0]??[];
    }

    // 获取树形数据
    public function getTreeData($api_data)
    {
        $sql = View::display($api_data['sql_string']);
        $tree_data = Db::query($sql);
        $tree_config = json_decode($api_data['config'],true);
        $tree = Tree::makeTree($tree_data,$tree_config);
        return ['options' => $tree];
    }




}