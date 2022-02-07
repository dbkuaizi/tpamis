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
    public static function write()
    {
        $create_log = [];
        $create_log['path'] = request()->path;
        $create_log['method'] = request()->method();
        $create_log['uid'] = request()->uid;
        $create_log['title'] = request()->menu_title;
        $create_log['ip'] = request()->ip();
        $create_log['ua'] = request()->header('User-Agent');
        $create_log['datetime'] = date('Y-m-d H:i:s');
        $create_log['desc'] = request()->log_desc ?? '';
        $create_log['param']= json_encode(request()->param(),JSON_UNESCAPED_UNICODE);
        $create_log['function'] = request()->controller().'/'.request()->action();
        self::create($create_log);
    }

}
