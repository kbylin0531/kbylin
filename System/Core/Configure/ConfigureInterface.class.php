<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2016/1/21
 * Time: 20:01
 */
namespace System\Core\Configure;

/**
 * Interface ConfigureInterface 配置处理接口
 * 继承该借口的类将使用其实现的方法读取和写入配置
 * @package System\Core\Config
 */
interface ConfigureInterface {

    /**
     * 读取单个的配置
     * @param string $item 配置文件名称
     * @param bool $iscustom 是否是自定义的配置，为true时表示读取的是自定义写入的配置
     * @return array|null 返回配置数组，不存在指定配置时候返回null
     */
    public function read($item, $iscustom=true);

    /**
     * 写入单个持久化的配置
     * @param string $item 配置文件名称
     * @param array $config 写入的配置信息
     * @param bool $cover 是否覆盖原先的配置,默认为true（是）
     * @return bool 返回false表示写入失败
     */
    public function write($item,array $config,$cover=true);

    /**
     * 将配置内容存储到缓存中
     * @param array $config 配置数组
     * @param int $expire 以秒计算的缓存时间,缓存时间为0表示永不过期
     * @return bool 缓存是否成功
     */
    public function store(array $config,$expire=0);

    /**
     * 加载缓存的配置信息并返回
     * @param array|null $confcache 外部的配置缓存（有失不使用该驱动类自身的配置缓存的情况下），
     *                              非null时将参数一的配置作为实际的缓存配置
     * @return bool 表示是否加载成功
     */
    public function load(array $confcache=null);

}