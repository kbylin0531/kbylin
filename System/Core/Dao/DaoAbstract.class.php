<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/17
 * Time: 10:38
 */
namespace System\Core\Dao;
use PDO;
use System\Core\BylinException;

/**
 * Class DaoAbstract Dao
 * @package System\Core\Dao
 */
abstract class DaoAbstract extends PDO {

    /**
     * 保留字段转义字符
     * mysql中是 ``
     * sqlserver中是 []
     * oracle中是 ""
     * @var string
     */
    protected $_l_quote = null;
    protected $_r_quote = null;

    /**
     * PDO驱动器名称
     * @var string
     */
    protected $driverName = null;

    /**
     * 禁止访问的PDO函数的名称
     * @var array
     */
    protected $forbidMethods = [
        'forbid','getColumnMeta'
    ];


    /**
     * 创建驱动类对象
     * DatabaseDriver constructor.
     * @param array $config
     * @throws BylinException 未设置
     */
    public function __construct(array $config){
        parent::__construct($this->buildDSN($config),$config['username'],$config['password'],$config['options']);
        if(null === $this->_l_quote) throw new BylinException('该数据库驱动未设置左转义符');
        if(null === $this->_r_quote) throw new BylinException('该数据库驱动未设置右转义符');
    }


    /**
     * 调用不存在的方法时
     * 需要注意的是，访问了禁止访问的方法时将返回false
     * @param string $name 方法名称
     * @param array $args 方法参数
     * @return mixed
     */
    public function __call($name,$args){
        if(in_array($name,$this->forbidMethods,true))  return false;
        return call_user_func_array([$this,$name],$args);
    }

    /**
     * 转义保留字字段名称
     * @param string $fieldname 字段名称
     * @return string
     */
    abstract public function escape($fieldname);
    /**
     * 根据配置创建DSN
     * @param array $config 数据库连接配置
     * @return string
     */
    abstract public function buildDSN(array $config);

    /**
     * 编译组件成适应当前数据库的SQL字符串
     * @param array $components  复杂SQL的组成部分
     * @return string
     */
    abstract public function compile(array $components);

}