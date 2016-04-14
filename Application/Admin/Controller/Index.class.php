<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/25
 * Time: 16:52
 */
namespace Application\Admin\Controller;
use Application\Admin\Model\IndexModel;

class Index{

    public function index(){
        $indexModel = new IndexModel();
        $menus = $indexModel->listMenus();

        dumpout($menus);
//        $this->display();
    }

}