<?php
/**
 * User: linzh
 * Date: 2016/3/9
 * Time: 16:02
 */
namespace System\Core;

/**
 * Class Response 输出控制类
 * @package System\Core
 */
class Response {
    /**
     * 清空输出缓存
     * @return void
     */
    public static function cleanOutput(){
        ob_get_level() > 0 and ob_end_clean();
    }

    public static function flushOutput(){
        ob_get_level() and ob_end_flush();
    }

}