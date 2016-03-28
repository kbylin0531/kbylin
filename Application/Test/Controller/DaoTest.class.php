<?php
/**
 * Created by PhpStorm.
 * User: linzh
 * Date: 2016/3/27
 * Time: 14:28
 */
namespace Application\Test\Controller;
use System\Core\Dao;

class DaoTest {

    /**
     * @var Dao
     */
    protected $dao = null;


    public function index(){
        $this->dao = Dao::getInstance();
//        dump(Dao::getAvailableDrivers());

//        $rst = $this->testBaiscQuery();
//        ($rst = $this->testBaiscErrorQuery()) === false and  $rst = $this->dao->getError();
        $rst = $this->testComplexQuery();
        dump($rst);
    }


    //测试Dao的query方法
    public function testBaiscQuery(){
        $sql = 'SELECT `name`,title from ot_action;';
        return $this->dao->query($sql);
    }
    public function testBaiscErrorQuery(){
        $sql = 'SELECT `name`,title from ot_action_aa;';
        return $this->dao->query($sql);
    }
    public function testComplexQuery(){
        $sql = 'SELECT `name`,title,remark from ot_action WHERE remark like :remark;';
        return $this->dao->query($sql,[':remark'    => '%积分%']);
    }







}