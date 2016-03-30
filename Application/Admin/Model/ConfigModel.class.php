<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/24
 * Time: 15:11
 */
namespace Application\Admin\Model;
use System\Core\Model;

class ConfigModel extends Model{

    const TABLE_NAME = 'bl_config';


    public function getConfigGroupList($id=0){
        $this->where("name = 'CONF_GROUP_LIST'")->select();



    }

}