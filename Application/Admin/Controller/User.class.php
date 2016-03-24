<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/24
 * Time: 15:00
 */
namespace Application\Admin\Controller;
use Application\Admin\General;
use System\Core\Router;

class User extends General{


    public function __construct()
    {
        parent::__construct();

        $this->assign('subnav',[
            [
                'name'      => '配置管理',
                'submenus'  => [
                    [
                        'url'  => Router::create('Admin','User','group'),
                        'title'  => '配置分组',
                    ],
                    [
                        'url'  => Router::create('Admin','User','index1'),
                        'title'  => 'index1',
                    ],
                    [
                        'url'  => Router::create('Admin','User','index2'),
                        'title'  => 'index2',
                    ],
                ],
            ],
            [
                'name'      => '测试2',
                'submenus'  => [
                    [
                        'url'  => Router::create('Admin','User','index3'),
                        'title'  => 'index3',
                    ],
                ],
            ]
        ]);
    }

    public function index1(){$this->assign('index',11);$this->display('index');}
    public function index2(){$this->assign('index',22);$this->display('index');}
    public function index3(){$this->assign('index',33);$this->display('index');}

    public function updatePasswd(){}

    public function updateNickname(){}

    public function login(){}

    public function logout(){}

}