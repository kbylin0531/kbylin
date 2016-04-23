<<<<<<< HEAD
<?php
/**
 * User: linzh_000
 * Date: 2016/3/15
 * Time: 15:48
 */
namespace System\Core;

class KbylinException extends \Exception{
    public function __construct(){
        $args = func_get_args();
        $this->message = var_export($args,true);
    }
=======
<?php
/**
 * User: linzh_000
 * Date: 2016/3/15
 * Time: 15:48
 */
namespace System\Core;

class KbylinException extends \Exception{
    public function __construct(){
        $args = func_get_args();
        $this->message = var_export($args,true);
    }
>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}