<<<<<<< HEAD
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/23
 * Time: 13:59
 */
namespace System\Utils;

/**
 * Class Think 选自thinkphp的方法
 * @package System\Utils
 */
class Think {

    /**
     * 获取输入参数 支持过滤和默认值
     * 使用方法:
     * <code>
     * I('id',0); 获取id参数 自动判断get或者post
     * I('post.name','','htmlspecialchars'); 获取$_POST['name']
     * I('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法
     * @param mixed $datas 要获取的额外数据源
     * @return mixed
     */
    public static function I($name,$default='',$filter='htmlspecialchars',$datas=null) {
        if(strpos($name,'.')) { // 指定参数来源
            list($method,$name) =   explode('.',$name,2);
        }else{ // 默认为自动判断
            $method =   'param';
        }
        switch(strtolower($method)) {
            case 'get'     :   $input =& $_GET;break;
            case 'post'    :   $input =& $_POST;break;
            case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
            case 'param'   :
                switch($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input  =  $_POST;
                        break;
                    case 'PUT':
                        parse_str(file_get_contents('php://input'), $input);
                        break;
                    default:
                        $input  =  $_GET;
                }
                break;
//            case 'path'    :
//                $input  =   array();
//                if(!empty($_SERVER['PATH_INFO'])){
//                    $depr   =   C('URL_PATHINFO_DEPR');
//                    $input  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));
//                }
//                break;
            case 'request' :   $input =& $_REQUEST;   break;
            case 'session' :   $input =& $_SESSION;   break;
            case 'cookie'  :   $input =& $_COOKIE;    break;
            case 'server'  :   $input =& $_SERVER;    break;
            case 'globals' :   $input =& $GLOBALS;    break;
            case 'data'    :   $input =& $datas;      break;
            default:
                return NULL;
        }
        if(''==$name) { // 获取全部变量
            $data       =   $input;
            array_walk_recursive($data,'filter_exp');
            $filters    =   isset($filter)?$filter:'htmlspecialchars';
            if($filters) {
                if(is_string($filters)){
                    $filters    =   explode(',',$filters);
                }
                foreach($filters as $filter){
                    $data   =   array_map_recursive($filter,$data); // 参数过滤
                }
            }
        }elseif(isset($input[$name])) { // 取值操作
            $data       =   $input[$name];
            is_array($data) && array_walk_recursive($data,'filter_exp');
            $filters    =   isset($filter)?$filter:'htmlspecialchars';
            if($filters) {
                if(is_string($filters)){
                    $filters    =   explode(',',$filters);
                }elseif(is_int($filters)){
                    $filters    =   array($filters);
                }

                foreach($filters as $filter){
                    if(function_exists($filter)) {
                        $data   =   is_array($data)?array_map_recursive($filter,$data):$filter($data); // 参数过滤
                    }else{
                        $data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
                        if(false === $data) {
                            return   isset($default)?$default:NULL;
                        }
                    }
                }
            }
        }else{ // 变量默认值
            $data       =    isset($default)?$default:NULL;
        }
        return $data;
    }

=======
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/23
 * Time: 13:59
 */
namespace System\Utils;

/**
 * Class Think 选自thinkphp的方法
 * @package System\Utils
 */
class Think {

    /**
     * 获取输入参数 支持过滤和默认值
     * 使用方法:
     * <code>
     * I('id',0); 获取id参数 自动判断get或者post
     * I('post.name','','htmlspecialchars'); 获取$_POST['name']
     * I('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法
     * @param mixed $datas 要获取的额外数据源
     * @return mixed
     */
    public static function I($name,$default='',$filter='htmlspecialchars',$datas=null) {
        if(strpos($name,'.')) { // 指定参数来源
            list($method,$name) =   explode('.',$name,2);
        }else{ // 默认为自动判断
            $method =   'param';
        }
        switch(strtolower($method)) {
            case 'get'     :   $input =& $_GET;break;
            case 'post'    :   $input =& $_POST;break;
            case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
            case 'param'   :
                switch($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input  =  $_POST;
                        break;
                    case 'PUT':
                        parse_str(file_get_contents('php://input'), $input);
                        break;
                    default:
                        $input  =  $_GET;
                }
                break;
//            case 'path'    :
//                $input  =   array();
//                if(!empty($_SERVER['PATH_INFO'])){
//                    $depr   =   C('URL_PATHINFO_DEPR');
//                    $input  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));
//                }
//                break;
            case 'request' :   $input =& $_REQUEST;   break;
            case 'session' :   $input =& $_SESSION;   break;
            case 'cookie'  :   $input =& $_COOKIE;    break;
            case 'server'  :   $input =& $_SERVER;    break;
            case 'globals' :   $input =& $GLOBALS;    break;
            case 'data'    :   $input =& $datas;      break;
            default:
                return NULL;
        }
        if(''==$name) { // 获取全部变量
            $data       =   $input;
            array_walk_recursive($data,'filter_exp');
            $filters    =   isset($filter)?$filter:'htmlspecialchars';
            if($filters) {
                if(is_string($filters)){
                    $filters    =   explode(',',$filters);
                }
                foreach($filters as $filter){
                    $data   =   array_map_recursive($filter,$data); // 参数过滤
                }
            }
        }elseif(isset($input[$name])) { // 取值操作
            $data       =   $input[$name];
            is_array($data) && array_walk_recursive($data,'filter_exp');
            $filters    =   isset($filter)?$filter:'htmlspecialchars';
            if($filters) {
                if(is_string($filters)){
                    $filters    =   explode(',',$filters);
                }elseif(is_int($filters)){
                    $filters    =   array($filters);
                }

                foreach($filters as $filter){
                    if(function_exists($filter)) {
                        $data   =   is_array($data)?array_map_recursive($filter,$data):$filter($data); // 参数过滤
                    }else{
                        $data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
                        if(false === $data) {
                            return   isset($default)?$default:NULL;
                        }
                    }
                }
            }
        }else{ // 变量默认值
            $data       =    isset($default)?$default:NULL;
        }
        return $data;
    }

>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}