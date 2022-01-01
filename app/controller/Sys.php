<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Sys extends BaseController
{
    // 菜单获取接口
    public function menu()
    {
        // 获得菜单
        $menu_where[] = ['delete_time','=',null];
        $menu_where[] = ['type','<>','action'];

        $menu_list = Db::table('admin_menu')->where($menu_where)->order('sort')->select();
        // 根据选项渲染节点内容
        $menu_list = $this->menuType($menu_list);
        // 构建菜单树
        $tree_config = ['parent_key' => 'parent_id','primary_key'=>'id'];
        $menu_tree = \Tree::makeTree($menu_list,$tree_config);
        return $this->success('菜单获取成功',['pages' => [['children' => $menu_tree]]]);
    }

    // 菜单类型转换
    private function menuType($menu_list)
    {
        $ret_list = [];
        $is_mobile = request()->isMobile();
        foreach ($menu_list as $menu_item)
        {
            switch ($menu_item['type']) {
                case 'menu': // 菜单
                    $ret_list[] = [
                        'id' => $menu_item['id'],
                        'parent_id' => $menu_item['parent_id'],
                        'label' => $menu_item['label'],
                        'icon' => $menu_item['icon'],
                        'url' => $menu_item['url'],
                        'schemaApi' => $menu_item['path'],
                        'visible' => ($menu_item['visible'] == 0) || ($menu_item['visible'] == 1 && $is_mobile) || (!$menu_item['visible'] == 2)
                    ];
                    break;
                case 'dir': // 目录
                    $ret_list[] = [
                        'id' => $menu_item['id'],
                        'parent_id' => $menu_item['parent_id'],
                        'label' => $menu_item['label'],
                        'icon' => $menu_item['icon']
                    ];
                    break;
                case 'link': // 外链
                    $ret_list[] = [
                        'id' => $menu_item['id'],
                        'parent_id' => $menu_item['parent_id'],
                        'label' => $menu_item['label'],
                        'icon' => $menu_item['icon'],
                        'link' => $menu_item['path'],
                        'visible' => ($menu_item['visible'] == 0) || ($menu_item['visible'] == 1 && $is_mobile) || (!$menu_item['visible'] == 2)
                    ];
                    break;
            }
        }
        return $ret_list;
    }

}
