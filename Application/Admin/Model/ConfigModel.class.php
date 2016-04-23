<<<<<<< HEAD
<?php
/**
 * Date: 2016/3/24
 * Time: 15:11
 */
namespace Application\Admin\Model;
use System\Core\Model;

class ConfigModel extends Model{

    const TABLE_NAME = 'bl_config';


    public function getConfigList(){
        return $this->getDao()->table('bl_entity_config')->select();
    }



=======
<?php
/**
 * Date: 2016/3/24
 * Time: 15:11
 */
namespace Application\Admin\Model;
use System\Core\Model;

class ConfigModel extends Model{

    const TABLE_NAME = 'bl_config';


    public function getConfigList(){
        return $this->getDao()->table('bl_entity_config')->select();
    }



>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}