<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/24
 * Time: 11:16
 */
namespace Application\Admin\Controller;
use Application\Admin\General;
use System\Core\Router;
use System\Traits\Controller\Response;

class Config extends General{

    use Response;

    public function menu(){
        if (IS_POST) {
            $this->ajaxBack([
                    [
                        "Tiger Nixon",
                        "System Architect",
                        "Edinburgh",
                        "5421",
                        "2011-04-25",
                        "$320,800"
                    ]
            ]);
        }
        $this->display();
    }

    public function index()
    {
        echo __METHOD__;
    }

    public function index1()
    {
        echo __METHOD__;
    }

    public function index2()
    {
        echo __METHOD__;
    }

    public function index3()
    {
        echo __METHOD__;
    }

    public function index4()
    {
        echo __METHOD__;
    }

    public function index5()
    {
        echo __METHOD__;
    }

    public function index6()
    {
        echo __METHOD__;
    }

    public function index7()
    {
        echo __METHOD__;
    }

    public function index8()
    {
        echo __METHOD__;
    }

    public function index9()
    {
        echo __METHOD__;
    }


    public function group($id = 0)
    {
        $configModel = new \Application\Admin\Model\Config();
        $conf_group_list = $configModel->getConfigGroupList($id);


//        $this->display();
    }


}