<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2016/2/2
 * Time: 9:40
 */
namespace System\Core;
use System\Core\Dao;
use System\Traits\Crux;

/**
 * Class Model 模型类
 *
 * @package System\Core
 */
class Model {

    use Crux;

    /**
     * 模型对应的数据表的实际名称
     * @var string
     */
    protected $_table = null;

    /**
     * 数据表的字段
     * @var array
     */
    protected $_fields = null;

    /**
     * 数据表主键，如果是复合主键则为array形式
     * @var string|array
     */
    protected $_primary_key = 'id';

    /**
     * 当前操作的Dao对象
     * @var Dao
     */
    protected $_dao = null;

    /**
     * Model constructor.
     */
    public function __construct(){
        $classname = static::class;
        defined("{$classname}::TABLE_NAME") and $this->_table = $classname::TABLE_NAME;

    }

    /**
     * 获取模型对应的Dao
     * 期中包含了Dao对象的创建工作
     * @param array|null $config
     * @return Dao
     */
    public function getDao(array $config=null){
        isset($this->dao) and $this->_dao = Dao::getDriverInstance($config);
        return $this->_dao;
    }



}