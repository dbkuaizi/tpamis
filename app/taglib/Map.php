<?php

namespace app\taglib;
use think\Exception;
use think\facade\Db;
use think\facade\View;
use think\template\TagLib;

// 字典标签库
class Map extends TagLib
{
    // 自定义标签
    protected $tags = [

        'colnum' =>  ['attr' => 'code,field,key', 'close' => 0],
        'config' =>  ['attr' => 'name,default', 'close' => 0],
        'option' =>  ['attr' => 'code,label,value', 'close' => 0],
    ];

    /**
     * 字典列表,用法类似于 array_colnum()
     */
    public function tagColnum($tag)
    {
        $map_code = $tag['code'];
        $field = $tag['field'] ?? 'map_val';
        $key = $tag['key']?? '';
        $result = Db::table('sys_map')->where('map_code',$map_code)->order('sort')->column($field,$key);
        return json_encode($result,JSON_FORCE_OBJECT);
    }

    /**
     * 字典配置，用于获取字典中的配置项
     */
    public function tagConfig($tag)
    {
        $map_arr = explode('.',$tag['name']);
        $where['map_code'] = $map_arr[0];
        $where['map_val'] = $map_arr[1];
        $value = Db::table('sys_map')->where($where)->value('map_val2');
        // 如果没有字典值 与 默认值 就抛出错误
        if (!isset($value) && !isset($tag['default'])) {
            throw new Exception('字典配置项 ['. $tag['name'] .'] 不存在');
        }
        // 有字典值显示字典值，没有就显示默认值
        return $value ?: $tag['default'];
    }

    /**
     * 字典选项，返回 OPTION 格式
     */
    public function tagOption($tag)
    {
        $map_code = $tag['code'];
        $label_field = $tag['label'] ?? 'map_name';
        $value_field = $tag['value'] ?? 'map_val';
        $field = [
            $label_field => 'label',
            $value_field => 'value',
        ];
        return Db::table('sys_map')->where('map_code',$map_code)->order('sort')->field($field)->select();

    }

}