<?php
/**
 * Email: linzhv@qq.com
 * Date: 2016/1/21
 * Time: 16:26
 */
namespace System\Core;
use System\Core\Cache\File;
use System\Core\Cache\Memcache;
use System\Traits\Crux;

defined('BASE_PATH') or die('No Permission!');
/**
 * Class Cache 缓存管理类
 * @package System\Library
 */
class Cache{

    use Crux;

    const CONF_NAME = 'cache';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,
        'DRIVER_CLASS_LIST' => [
            File::class,
            Memcache::class,
        ],
        'DRIVER_CONFIG_LIST' => [
            [
                //选自THinkPHP，大小写保持原样
                'expire'        => 0,
                'cache_subdir'  => false,
                'path_level'    => 1,
                'prefix'        => '',
                'length'        => 0,
                'path'          => RUNTIME_PATH.'/Cache/',
                'data_compress' => false,
            ],
            [
                'host'      => '127.0.0.1',
                'port'      => 11211,
                'expire'    => 0,
                'prefix'    => '',
                'timeout'   => 0, // 超时时间（单位：毫秒）
                'persistent'=> true,
                'length'    => 0,
            ],
        ],
    ];

    /**
     * 读取次数
     * @var int
     */
    protected static $readTimes   = 0;
    /**
     * 写入次数
     * @var int
     */
    protected static $writeTimes  = 0;

    /**
     * 使用的驱动的角标
     * 如果为null，则Crux使用配置中默认的设置
     * @var int|string|null
     */
    protected static $index = null;

    /**
     * 选择缓存驱动，在实际读写之前有效
     * @param $index
     */
    public static function using($index){
        self::$index = $index;
    }

    /**
     * 获取读取缓存次数
     * @return int
     */
    public static function getReadingTimes(){
        return self::$readTimes;
    }

    /**
     * 获取写入缓存的速度
     * @return int
     */
    public static function getWriteTimes(){
        return self::$writeTimes;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $replacement 值不存在时的替代
     * @return mixed
     */
    public static function get($name,$replacement=null){
        ++ self::$readTimes;
        $result = self::getDriverInstance(self::$index)->get($name);
        return $result === null ? $replacement : $result;
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 0为永久
     * @return boolean
     */
    public static function set($name, $value, $expire = null){
        ++ self::$writeTimes;
        return self::getDriverInstance(self::$index)->set($name, $value, $expire);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public static function rm($name){
        ++ self::$writeTimes;
        return self::getDriverInstance(self::$index)->rm($name);
    }

    /**
     * 清除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public static function clear($name=null){
        ++ self::$writeTimes;
        return self::getDriverInstance(self::$index)->clear($name);
    }

}