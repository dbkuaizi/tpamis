<?php
namespace app\admin\taglib;

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
        $result = Db::table('sys_instances')->where('v_Code',$code)->find();
        // 如果查询为空 就使用不存在的 实例
        if (empty($result)) {
            $result = Db::table('sys_instances')->where('v_Code','com_ins_empty')->find();
            $result['v_Body'] = str_replace('[code]',$code, $result['v_Body']);
        }
        return View::display($result['v_Body'],$result);
    }

}

