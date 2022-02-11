<?php
namespace app\taglib;

use think\facade\Db;
use think\facade\View;
use think\template\TagLib;
use app\model\AdminUser;
use app\model\AdminMenu;

/**
 * Class Com 组件标签扩展
 * @package app\admin\common
 */
class Com extends TagLib
{
    protected $tags = [
        // 插槽功能
        'slot'      =>  ['attr' => 'code', 'close' => 0],
        'page'      =>  ['attr' => '', 'close' => 0],
        'pagesize'      =>  ['attr' => '', 'close' => 0],
        'auth'      =>  ['attr' => 'path', 'close' => 0],
    ];

    /**
     * 组件插槽
     * @param $tag
     */
    public function tagSlot($tag)
    {
        $code = $tag['code'];
        unset($tag['code']);

        if(
           // 如果传递了平台 并且
            isset($tag['platform']) && ( 
                // (只在pc平台显示 且当前请求是移动端）或
                ($tag['platform'] == 'pc' && request()->isMobile()) ||
                // (只在移动端显示，且当前请求是pc端)
                ($tag['platform'] == 'mobile' && !request()->isMobile())
            )
           )
        {
            return '{}';
        }

        $result = Db::table('sys_com')->where('code',$code)->find();
        // 如果查询为空 就使用不存在的 实例
        if (empty($result)) {
            $result = Db::table('sys_com')->where('code','sys_com_empty')->find();
            $result['body'] = str_replace('[code]',$code, $result['body']);
        }
        $view_param = array_merge($result,$tag);
        return View::display($result['body'],$view_param);
    }

    // 返回组件参数
    public function tagPage($tag)
    {
        $where['map_code'] = 'SysComConfig';
        $where['map_val'] = 'PageSizeDefault';
        return Db::table('sys_map')->where($where)->value('map_val2');
    }

    // 返回组件参数
    public function tagPageSize($tag)
    {
        $where['map_code'] = 'SysComConfig';
        $where['map_val'] = 'PageSize';
        return Db::table('sys_map')->where($where)->value('map_val2');
    }

    // 权限
    public function tagAuth($tag)
    {
        $url = $tag['path'];
        
        // 获取登录用户信息
        $admin_user = AdminUser::find(request()->uid);

        // 超级管理员 返回true
        if($admin_user->super_admin) return ' true';

        $admin_menu = AdminMenu::where('path','=', $url)->find();

        // 如果免校验
        if(!$admin_menu->verify) return ' true';

        // 获取该用户的授权
        $permissions_arr = $admin_user->getPermissions();
        if(in_array($admin_menu->id,$permissions_arr)) return ' true';

        return ' false';

    }

}

