<<<<<<< HEAD
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/25
 * Time: 16:52
 */
namespace Application\Admin\Controller;
use System\Library\View;

class Index{


=======
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/25
 * Time: 16:52
 */
namespace Application\Admin\Controller;
use Application\Admin\Model\IndexModel;
use Application\Admin\Utils\TemplateTool;
use System\Library\View;
use System\Traits\Controller\Render;
use System\Utils\Response;

class Index{

    use Render;

    public function __construct(){}

    public function menus(){
        $indexModel = new IndexModel();
        $menus = $indexModel->listMenus();
        if(false === $menus){
            Response::ajaxBack(['type'=>'error']);
        }
        $this->assign('menus',TemplateTool::translate($menus));
        $this->display();
    }

    public function updateMenus(array $list){
        $list or Response::failed('You gave the empty message!');
        $indexModel = new IndexModel();
        foreach($list as $item){
            $result = $indexModel->updateMenuItem($item);
        }
        Response::success('修改成功！');
    }


>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}