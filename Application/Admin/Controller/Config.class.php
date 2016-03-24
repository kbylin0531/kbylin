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

class Config extends General{


    public function index1(){$this->assign('index',1);$this->display('index');}
    public function index2(){$this->assign('index',2);$this->display('index');}
    public function index3(){$this->assign('index',3);$this->display('index');}



    public function __construct()
    {
        parent::__construct();

        $this->assign('subnav',[
            [
                'name'      => '配置管理',
                'submenus'  => [
                    [
                        'url'  => Router::create('Admin','Config','group'),
                        'title'  => '配置分组',
                    ],
                    [
                        'url'  => Router::create('Admin','Config','index1'),
                        'title'  => 'index1',
                    ],
                    [
                        'url'  => Router::create('Admin','Config','index2'),
                        'title'  => 'index2',
                    ],
                ],
            ],
            [
                'name'      => '测试2',
                'submenus'  => [
                    [
                        'url'  => Router::create('Admin','Config','index3'),
                        'title'  => 'index3',
                    ],
                ],
            ]
        ]);
    }


    public function group($id=0){
        $configModel = new \Application\Admin\Model\Config();
        $conf_group_list = $configModel->getConfigGroupList($id);


//        $this->display();
    }



}