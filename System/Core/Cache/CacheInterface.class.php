<?php
/**
 * User: linzh_000
 * Date: 2016/3/17
 * Time: 9:06
 */
namespace System\Core\Cache;

/**
 * Interface CacheInterface 缓存驱动接口
 * @package System\Library\Cache
 */
interface CacheInterface {

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name);

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 0为永久
     * @return boolean
     */
    public function set($name, $value, $expire = null);

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name);

    /**
     * 清除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function clear($name=null);
}