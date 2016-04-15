<?php
/**
 * Created by PhpStorm.
 * User: Zhonghuang
 * Date: 2016/4/14
 * Time: 14:21
 */
namespace Application\Admin\Model;
use System\Library\Model;
use PDO;

class IndexModel extends Model{

    /**
     * @return array
     */
    public function listMenus(){
        $kdao = $this->getDao();
        return $kdao->query('select * from kbylin_system_menu_group;');
    }


    public function sortMenuGroup(array &$groups,&$sort=[]){
        foreach($groups as &$item){


        }
        return $sort;
    }

}