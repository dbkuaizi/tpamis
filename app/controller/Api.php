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
                case 'curd': // curd
                    $ret_data = $this->getCurdData($api_data,$api_config['curd']);
                    break;
                case 'tree': // 树形数据
                    $ret_data = $this->getTreeData($api_data,$api_config['tree']);
                    break;
                case 'option': // 选项数据
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
    public function save(Request $request,$code)
    {
        $ret_data = ['status' => 1 ,'msg' => '保存失败'];

        $com_data = Db::table('sys_com')->where('code',$code)->field('tables,pri_field')->find();

        // 获取主键字段名
        $pri_key = $com_data['pri_field'] ?: 'id';
        
        $id = $request->post($pri_key);     // 获取 id
        $table_name = $com_data['tables'];  // 获取组件表名
        $data = $request->post();           // 获取请求数据

        // 如果属性中存在数组则使用 JSON 编码
        foreach($data as &$val)
        {
            if(is_array($val)) {
                $val = json_encode($val,JSON_UNESCAPED_UNICODE);
            }
        }

        // 获取操作表字段
        $table_fields = array_column(DB::query("DESC ".$com_data['tables']),'Field');

        // 如果id不为空 则表示修改数据, 如果不存在就表示新增数据
        if (!empty($id)){

            // 是否需要补上修改时间
            if (in_array('update_time',$table_fields)) {
                $data['update_time'] =  date('Y-m-d H:i:s');
            }
            
            $res = Db::table($table_name)->where([$pri_key => $id])->update($data);
            $res_msg = '编辑成功';
        } else {

            // 是否需要补上新增时间
            if (in_array('create_time',$table_fields)) {
                $data['create_time'] =  date('Y-m-d H:i:s');
                $data['update_time'] =  date('Y-m-d H:i:s');
            }
            $res_msg = '添加成功';
            $res = Db::table($table_name)->insert($data);
        }
        if ($res) {
            $ret_data = ['status' => 0 ,'msg' => $res_msg];
        }
        return $ret_data;

    }

    // 通用删除接口
    public function del($code,$id)
    {
        $com_data = Db::table('sys_com')->where('code',$code)->field('tables,pri_field')->find();
        // 如果组件不存在
        if (!isset($com_data)) return $this->error('组件不存在');
        if (empty($com_data['tables'])) return $this->error('组件数据表不能为空');

        // 判断是否存在软删除字段
        $table_fields = array_column(DB::query("DESC ".$com_data['tables']),'Field');
        $pri_key = $com_data['pri_field'] ?: 'id';
        
        // 构建删除条件
        $del_where[$pri_key] = $id;
        
        // 如果存在软删除字段，就走软删除
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
        // 拼接查询条件
        if(!empty($config['search'])) {
            $this->CurdSearchSQL($sql,$config['search']);
        }

        // 获取统计SQL
        if ($api_data['sql_total']) {
            $total_sql = $api_data['sql_total'];
        } else {
            $total_sql = 'SELECT count(*) as total FROM ('.$sql.') as total_table';
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

        // 如果是调试模式且传递了debug，则输出SQL
        if(env('app_debug') && request()->has('debug'))
        {
            echo "<h4>Query SQL: </h4><pre>{$sql}</pre>";
            echo "<h4>Total SQL: </h4><pre>{$total_sql}</pre>";
            exit;
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
        $ret_field = request()->get('response_key','options');
        return [$ret_field => $tree];
    }

    // 查询的数据进行JSON 转换
    private function CurdToJson(&$curd_data,$json_to)
    {
        $json_to = explode(',',$json_to);
        foreach ($curd_data as &$item)
        {
         
            foreach ($item as $key => &$val)
            {
                // 如果转换失败
                if(in_array($key,$json_to))
                {
                    $val = json_decode($val,true)?: [];
                }
                
            }
        }
    }

    // 查询条件 SQL 拼装
    private function CurdSearchSQL(&$sql,$search_config)
    {
        // 初始化返回条件
        $where_str = '1';
        $search_arr = array_column($search_config,'type','field');
        $get_data = request()->get();

        // 遍历请求参数
        foreach($get_data as $field => $val)
        {
            // 如果本次循环参数没有在查询条件中就跳过
            if(!array_key_exists($field,$search_arr)) continue;

            // 根据不同的类型 拼接不同的传参
            switch ($search_arr[$field]) {
                case 'like': // 模糊查询
                    $where_str .= " AND `{$field}` LIKE '%{$val}%'";
                    break;
                case 'in': // in查询
                    $in_str = str_replace(',','',$val);
                    if(is_numeric($in_str))
                    {
                        $where_str .= " AND `{$field}` IN ({$val})";
                    } else {
                        $in_arr = implode("','",explode(',',$val));
                        $where_str .= " AND `{$field}` IN ('{$in_arr}')";
                    }
                    break;
                default: // 如无需特殊处理 走默认即可
                    $where_str .= " AND `{$field}` {$search_arr[$field]} " . (is_numeric($val) ? $val : "'".$val."'");
                    break;
            }
        }

        $where_str = '('.$where_str.') AND ';

        // 查找占位符,如果存在就替换
        if(strpos($sql,'[curd_where]'))
        {
            $sql = str_replace('[curd_where]',$where_str,$sql);
            // 替换完成就返回
            return;
        }

        // 没找到替换字段就找最后一个 where
        $where_index = strripos($sql,'where');
        $start_sql = substr($sql,0, $where_index);
        $end_sql = substr($sql,$where_index + 6);
        $sql = $start_sql .'WHERE '. $where_str . $end_sql;
    }


}