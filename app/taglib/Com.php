<?php
namespace app\taglib;

use think\facade\Db;
use think\facade\View;
use think\template\TagLib;

/**
 * Class Com 组件标签扩展
 * @package app\admin\common
 */
class Com extends TagLib
{
    protected $tags = [
        // 插槽功能
        'slot'      =>  ['attr' => 'code', 'close' => 0],
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

}

