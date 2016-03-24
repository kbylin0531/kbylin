<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/22
 * Time: 9:48
 */
namespace System\Traits\Model;
use System\Core\Model\Dao;

/**
 * Class Combination
 * @package System\Traits\Model
 */
trait Combination {

    /**
     * 当前操作的Dao对象
     * @var Dao
     */
    protected $_dao = null;




    /**
     * 获取错误
     * @return string|null 有错误发生时返回string，否则返回null
     */
    public function getCombinationError(){
        return isset($this->_dao)?$this->_dao->getErrorInfo():null;
    }


}