<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Session;
use app\model\AdminUser;

class Sys extends BaseController
{
    // 菜单获取接口
    public function menu()
    {
        // 获得菜单数据
        $menu_where[] = ['delete_time','=',null];
        // 查询类型不是操作的
        $menu_where[] = ['type','<>','action'];
        $menu_list = Db::table('admin_menu')->where($menu_where)->order('sort')->select()->toArray();

        // 菜单权限逻辑处理
        $this->menuPermissions($menu_list);

        // 根据选项渲染节点内容
        $menu_list = $this->menuType($menu_list);
        // 构建菜单树
        $tree_config = ['parent_key' => 'parent_id','primary_key'=>'id'];
        $menu_tree = \Tree::makeTree($menu_list,$tree_config);
        return $this->success('菜单获取成功',['pages' => [['children' => $menu_tree]]]);
    }

    // 
    private function menuPermissions(&$menu_list)
    {
       $uid = request()->uid;
       $admin_user = AdminUser::find($uid);
        // 如果是超级管理员，就不校验权限
       if(isset($admin_user->super_admin) && $admin_user->super_admin === 1)
       {
            return;
       }

       // 获取当前用户对应角色的权限
       $permissions_arr = $admin_user->getPermissions();
       // 循环处理权限
       foreach($menu_list as $menu_key => $menu_item)
       {
            // 跳过目录 和 无需授权的
            if($menu_item['type'] == 'dir' || !$menu_item['verify'])
            {
                continue;
            }

            if(in_array($menu_item['id'],$permissions_arr))
            {
                continue;
            }

            unset($menu_list[$menu_key]);
       }

       // 判断目录下面是否有子菜单
       $parent_id = array_column($menu_list, 'parent_id');
       foreach($menu_list as $menu_key => $menu_item)
       {
            // 跳过不是目录的
            if($menu_item['type'] != 'dir')
            {
                continue;
            }

            if(in_array($menu_item['id'],$parent_id))
            {
                continue;
            }

            unset($menu_list[$menu_key]);
       }

    }

    /**
     * 菜单类型转换
     * 根据菜单、目录、外链的不同类型转换为不同的菜单结构
     *
     * @param array $menu_list 数据库查询的菜单类型
     * @return void 返回转换后的数组
     */
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
