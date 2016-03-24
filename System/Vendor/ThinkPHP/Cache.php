<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace System\Vendor\ThinkPHP;
use System\Traits\Crux;
use System\Vendor\ThinkPHP\Cache\File;

/**
 * Class Cache
 * @package think
 */
class Cache{

    use Crux;

    const CONF_NAME = 'thinkcache';
    const CONF_CONVENTION = [
        'DRIVER_CLASS_LIST' => [
            File::class,
        ],//驱动类列表
        'DRIVER_CONFIG_LIST' => [
            [
                'expire'        => 0,
                'cache_subdir'  => false,
                'path_level'    => 1,
                'prefix'        => '',
                'length'        => 0,
                'path'          => RUNTIME_PATH.'ThinkCache/',
                'data_compress' => false,
            ]
        ],//驱动类配置数组列表,如果不存在对应的但存在唯一的一个配置数组，则上面的driver类均使用该配置项
    ];

    protected static $instance = [];
    public static $readTimes   = 0;
    public static $writeTimes  = 0;



//    public static function __callStatic($method, $params)
//    {
//        if (is_null(self::$handler)) {
//            // 自动初始化缓存
//            self::connect(Config::get('cache'));
//        }
//        return call_user_func_array([self::$handler, $method], $params);
//    }


    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public static function get($name){
        return self::getInstance()->get($name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return bool
     */
    public static function set($name, $value, $expire = null){
        return self::getInstance()->set($name, $value, $expire);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool|\string[]
     */
    public static function rm($name){
        return self::getInstance()->rm($name);
    }

    /**
     * 清除缓存
     * @access public
     * @return bool
     */
    public static function clear(){
        return self::getInstance()->clear();
    }

}
