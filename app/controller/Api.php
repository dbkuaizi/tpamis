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
    /**
     * 通用数据获取接口
     *
     * @param [type] $code
     * @return void
     */
    public function get($code)
    {
        $api_data = Db::table('sys_api')->where('code',$code)->find();
        // 获取接口配置
        $api_config = ['tree' => [],'curd' => [],'option'=> [],'find' => []];
        $api_config = array_merge($api_config,json_decode($api_data['config'],true) ?: []);

        try {
            switch ($api_data['type']) {
                case 'find': // 单条数据
                    $ret_data = $this->getFindData($api_data);
                    break;
                case 'curd': // 多条数据
                    $ret_data = $this->getCurdData($api_data,$api_config['curd']);
                    break;
                case 'tree':
                    $ret_data = $this->getTreeData($api_data,$api_config['tree']);
                    break;
                case 'option': // 多条数据
                    $ret_data = $this->getOptionData($api_data);
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

        // 如果没有通过路由传递id，但通过了 get 或 post 传递了 id
        if(empty($id) && $request->has('id')) {
            $id = $request->param('id');
        }

        $com_data = Db::table('sys_com')->where('code',$code)->field('tables,pri_field')->find();
        $table_name = $com_data['tables'];
        $data = $request->post();

        // 如果属性中存在数组则使用 JSON 编码
        foreach($data as &$val)
        {
            if(is_array($val)) {
                $val = json_encode($val,JSON_UNESCAPED_UNICODE);
            }
        }

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
        if (empty($com_data['tables'])) return $this->error('组件数据表不能为空');

        // 判断是否存在软删除字段
       $table_fields = array_column(DB::query("DESC ".$com_data['tables']),'Field');
       $pri_key = $com_data['pri_field'] ?: 'id';
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

    // 通用排序接口
    public function sort(Request $request, $code)
    {
        $com_data = Db::table('sys_com')->where('code',$code)->field('tables,pri_field')->find();
        // 如果组件不存在
        if (!isset($com_data)) return $this->error('组件不存在');
        if (empty($com_data['tables'])) return $this->error('组件数据表不能为空');

        $sort_arr = explode(',',$request->post('ids'));
        $sort_field = $request->post('sort_field','sort');
        $pri_key = $com_data['pri_field'] ?: 'id';

        try {
            Db::startTrans();
            foreach($sort_arr as $key => $val)
            {
                Db::table($com_data['tables'])->where([$pri_key => $val])->update([$sort_field =>$key]);
            }
            Db::commit();
        }catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $this->error('保存失败');
        }

        return $this->success('保存成功');

    }

    // 列表获取接口
    public function getCurdData($api_data,$config)
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

        $curd_data = Db::query($sql);
        // 如果有配置 JSON 转换，就调用转换
        if(!empty($config['jsonto']))
        {
            $this->CurdToJson($curd_data,$config['jsonto']);
        }

        return [
            'items' => $curd_data,
            'total' => Db::query($total_sql)[0]['total'],
        ];
    }

    // 获取选项数据
    public function getOptionData($api_data)
    {
        $sql = View::display($api_data['sql_string']);
        $ret_data['options'] = Db::query($sql);
        return $ret_data;
    }

    // 单条获取数据
    public function getFindData($api_data)
    {
        $sql = View::display($api_data['sql_string']);
        return  Db::query($sql)[0]??[];
    }

    // 获取树形数据
    public function getTreeData($api_data,$config)
    {
        $sql = View::display($api_data['sql_string']);
        $tree_data = Db::query($sql);
        $tree = Tree::makeTree($tree_data,$config);
        return ['options' => $tree];
    }

    // 查询的数据进行JSON 转换
    private function CurdToJson(&$curd_data,$json_to)
    {
        $json_to = explode(',',$json_to);
        foreach ($curd_data as &$item)
        {
         
            foreach ($item as $key => &$val)
            {

                if(in_array($key,$json_to))
                {
                    $val = json_decode($val,true);
                }
                
            }
        }
    }



}