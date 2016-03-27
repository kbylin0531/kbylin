<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/17
 * Time: 10:49
 */
namespace System\Core\Dao;

class MySQL extends DaoAbstract{

    /**
     * 转义保留字字段名称
     * @param string $fieldname 字段名称
     * @return string
     */
    public function escape($fieldname){
        return "`{$fieldname}`";
    }

    /**
     * 根据配置创建DSN
     * @param array $config 数据库连接配置
     * @return string
     */
    public function buildDSN(array $config){
        $dsn  =  "mysql:host={$config['host']}";
        if(isset($config['dbname'])){
            $dsn .= ";dbname={$config['dbname']}";
        }
        if(!empty($config['port'])) {
            $dsn .= ';port=' . $config['port'];
        }
        if(!empty($config['socket'])){
            $dsn  .= ';unix_socket='.$config['socket'];
        }
        if(!empty($config['charset'])){
            $dsn  .= ';charset='.$config['charset'];
        }
        return $dsn;
    }

    /**
     * 编译组件成适应当前数据库的SQL字符串
     * @param array $components  复杂SQL的组成部分
     * @return string
     */
    public function compile(array $components){

    }
}