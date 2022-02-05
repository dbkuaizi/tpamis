<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminLog extends Model
{
    // 保存日志
    public static function write($title = '',$desc = '')
    {
        $create_log = [];
        $create_log['path'] = request()->path;
        $create_log['method'] = request()->method();
        $create_log['uid'] = request()->uid;
        $create_log['title'] = $title ?: AdminMenu::getUrlName(request()->path) ?? '未知';
        $create_log['ip'] = request()->ip();
        $create_log['ua'] = request()->header('User-Agent');
        $create_log['datetime'] = date('Y-m-d H:i:s');
        $create_log['desc'] = $desc;
        $create_log['param']= json_encode(request()->param(),JSON_UNESCAPED_UNICODE);
        self::create($create_log);
    }

}
