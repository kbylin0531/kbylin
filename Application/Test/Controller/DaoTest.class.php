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

        $rst = $this->testBaiscQuery();
        dump($rst);
    }


    public function testBaiscQuery(){
        $sql = 'SELECT `name`,title from ot_action;';
        return $this->dao->query($sql);
    }

}