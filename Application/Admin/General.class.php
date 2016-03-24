<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/24
 * Time: 13:12
 */
namespace Application\Admin;
use System\Core\Router;
use System\Traits\Controller\Render;

class General {

    use Render;

    public function __construct(){
        defined('ADMIN_URI') or define('ADMIN_URI',PUBLIC_URI.'Admin/');
        defined('STATIC_URI') or define('STATIC_URI',PUBLIC_URI.'static/');

        $this->generalAssign();
    }

    protected function generalAssign(){

        $this->assign('user_info',[]);
        $this->assign('uri',[
            'update_password'   => Router::create('Admin','User','updatePasswd'),
            'update_nickname'   => Router::create('Admin','User','updateNickname'),
            'logout'            => Router::create('Admin','User','logout'),

        ]);

        $this->assign('menu_list',[
            [
                'class' => '',
                'url'   => Router::create('Admin','Config','index1'),
                'title' => '设置',
            ],
            [
                'class' => '',
                'url'   => Router::create('Admin','User','index1'),
                'title' => '用户',
            ]
        ]);


        $this->assign('style','blue_color');

        $this->assign('copyright',[
            'fl'    => '感谢使用<a href="http://www.onethink.cn" target="_blank">OneThink</a>管理平台',
            'fr'    => 'V0.1',
        ]);
    }

}