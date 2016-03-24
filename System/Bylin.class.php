<?php
/**
 * User: linzh
 * Date: 2016/3/14
 * Time: 20:51
 */
use System\Core\Response;
use System\Core\LiteBuilder;
use System\Core\Storage;
use System\Utils\Network;
use System\Core\Router;
use System\Core\Dispatcher;

const AJAX_JSON = 0;
const AJAX_XML = 1;

date_default_timezone_set('Asia/Shanghai'); //避免使用date函数时的警告
defined('DEBUG_MODE_ON') or define('DEBUG_MODE_ON', true); //是否开启DUBUG模式
defined('PAGE_TRACE_ON') or define('PAGE_TRACE_ON', true); //是否开启TRACE界面

/**
 * Class Bylin
 */
class Bylin {

    /**
     * 应用名称
     * @var string
     */
    protected $appname = 'Bylin';

    /**
     * 应用配置
     * @var array
     */
    protected $_convention = [

        //-- 目录配置，相对于server.php的位置 --/
        'SYSTEM_DIR'    => 'System/',
        'APP_DIR'       => 'Application/',
        'CONFIG_DIR'    => 'Config/',
        'RUNTIME_DIR'   => 'Runtime/',
        'PUBLIC_DIR'    => 'Public/',
        'UPLOAD_DIR'   => 'Upload/',

        'CLASS_LOADER'      => null, //用户自定义错误处理函数
        'ERROR_HANDLER'     => null, //用户自定义错误处理函数
        'EXCEPTION_HANDLER' => null, //用户自定义异常处理函数

        //函数包列表
        'FUNC_PACK_LIST'     => [],
    ];
    /**
     * 类库的映射
     * 完整类名称 => 类文件的完整路径
     * @var array
     */
    protected $_classes = [];

    /**
     * 标记是否加载lite文件
     * @var bool
     */
    protected $_liteon = false;

    /**
     * 标记类示例是否经理郭初始化
     * @var bool
     */
    private $_inited = false;

    /**
     * Bylin constructor.
     * @param null $appname
     * @param array|null $config
     */
    public function __construct($appname=null,array $config=null){
        self::recordStatus('construct_begin');
        version_compare(PHP_VERSION,'5.4.0','<') and die('Require PHP >= 5.4 !');
        null !== $appname and $this->appname = $appname;
        $this->init($config);
    }

    /**
     * 初始化应用程序
     * 注意：这个过程中不可以调用其它类，真正的类加载过程是在start应用开始
     * @param array $config
     */
    public function init(array $config=null){
        self::recordStatus('init_begin');
        //初始化控制（要求只能初始化一次）
        $this->_inited and die('实例已完成过初始化!');
        null !== $config and $this->_convention = array_merge($this->_convention,$config);//合并用户自定义配置和系统封默认配置

        //目录常量
        define('BASE_PATH',str_replace('\\','/',dirname(__DIR__)).'/');
        define('SYSTEM_PATH',BASE_PATH.$this->_convention['SYSTEM_DIR']);
        define('APP_PATH',BASE_PATH.$this->_convention['APP_DIR']);
        define('CONFIG_PATH',BASE_PATH.$this->_convention['CONFIG_DIR']);
        define('RUNTIME_PATH',BASE_PATH.$this->_convention['RUNTIME_DIR']);
        define('PUBLIC_PATH',BASE_PATH.$this->_convention['PUBLIC_DIR']);
        define('UPLOAD_PATH',BASE_PATH.$this->_convention['UPLOAD_DIR']);

        //布尔常量
        define('IS_WIN',false !== stripos(PHP_OS, 'WIN')); //运行环境
        define('IS_CLI', PHP_SAPI === 'cli');
        define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ));
        define('IS_POST',strtoupper($_SERVER['REQUEST_METHOD']) === 'POST');
        //其他常量
        define('APP_NAME',$this->appname);
        define('BASE_URI',dirname($_SERVER['SCRIPT_NAME']).'/');
        define('PUBLIC_URI',BASE_URI.'Public/');

        //错误显示
        error_reporting(DEBUG_MODE_ON?-1:E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);//PHP5.3一下需要用这段 “error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);”
        ini_set('display_errors',DEBUG_MODE_ON?1:0);

        self::recordStatus('init_behavior_begin');

        //行为注册(类加载、错误、异常、脚本结束)
        false === spl_autoload_register(isset($this->_convention['CLASS_LOADER'])?
            $this->_convention['CLASS_LOADER']:[$this,'_autoLoad']) and die('spl autoload register failed!');
        set_error_handler(isset($this->_convention['ERROR_HANDLER'])?$this->_convention['EXCEPTION_HANDLER']:[$this,'_handleError']) ;
        set_exception_handler(isset($this->_convention['EXCEPTION_HANDLER'])?$this->_convention['EXCEPTION_HANDLER']:[$this,'_handleException']);
        register_shutdown_function([$this,'_onShutDown']);

        self::recordStatus('init_funcpack_load_begin');
        //加载函数包(注意函数名冲突)
        include SYSTEM_PATH.'Common/functions.php'; // 加载系统函数包
        if($this->_convention['FUNC_PACK_LIST']){
            foreach($this->_convention['FUNC_PACK_LIST'] as $packname){
                $filename = BASE_PATH."Func/{$packname}.php";
                if(is_file($filename)) include $filename;//使用include代替include_once提高效率
            }
        }

        self::recordStatus('init_end');
        $this->_inited = true;
    }

    /**
     * 检查目录是否存在以及进行一些默认的一些设置
     * @param bool $on 是否检查，默认是
     * @return $this
     */
    public function inspect($on=true){
        if($on){
            //TODO:检查目录和文件的完整性
            echo "系统正在检查目录文件设置";
        }
        return $this;
    }

    /**
     * 检查是否加载lite文件
     * @param bool $on 是否启用lite文件
     * @return $this
     */
    public function liten($on=true){
        if($on){
            $this->_liteon = true;
            define('LITE_FILE_NAME',RUNTIME_PATH.APP_NAME.'.lite.php');//运行时核心文件
            //考虑到云服务器上lite文件直接使用is_file判断和包含，需要手动上传
            self::recordStatus('load_lite_begin');
            if(is_file(LITE_FILE_NAME))  include LITE_FILE_NAME;//代替include文件，拥有更好的适应性？
            self::recordStatus('load_lite_end');
        }
        return $this;
    }

    /**
     * 开启应用
     */
    public function start(){
        //检查初始化情况(即直接new)
        $this->_inited or $this->init();
        self::recordStatus('app_begin');

        $uri = Network::getUri(true);
        $hostname = Network::getHostname();
        $result = Router::parse($uri,$hostname);

        Dispatcher::execute($result[0],$result[1],$result[2],$result[3]);

//        dumpout($uri,$hostname,$result,$return);
    }

    public function test(){
        $this->_inited or $this->init();
        require SYSTEM_PATH.'Test/index.php';
        exit();
    }

    /**
     * 脚本结束自动调用
     */
    public function _onShutDown(){
        self::recordStatus("_xor_exec_shutdown");
        if(DEBUG_MODE_ON and PAGE_TRACE_ON) self::showTrace(6);//页面跟踪信息显示
        if($this->_liteon and !is_file(LITE_FILE_NAME)){ //开启加载 并且Lite文件不存在时  ==> 重新生成
            self::recordStatus('create_lite_begin');
            Storage::write(LITE_FILE_NAME,LiteBuilder::compileInBatch($this->_classes));
            self::recordStatus('create_lite_begin');
        }
        Response::flushOutput();
    }

    /**
     * 系统默认的类加载方法
     * 根目录下可以不设置命名空间
     * @param string $clsnm 类名称（包含命名空间）
     * @return void
     */
    public function _autoLoad($clsnm){
        if(isset($this->_classes[$clsnm])) {
            include_once $this->_classes[$clsnm];
        }else{
            $pos = strpos($clsnm,'\\');
            if(false === $pos){
                $file = BASE_PATH . "{$clsnm}.class.php";
                if(is_file($file)) include_once $file;
            }else{
                $file       =   BASE_PATH.str_replace('\\', '/', $clsnm).'.class.php';
                if(is_file($file) ) {
                    //window下对is_file对文件名称不区分大小写，故这里需要作检测
                    if (!(IS_WIN and false === strpos(str_replace('/', '\\', realpath($file)), "{$clsnm}.class.php") )){
                        include_once $this->_classes[$clsnm] = $file;
                    }
                }
            }
        }
    }
    /**
     * 系统默认的错误处理函数
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return void
     */
    public function _handleError($errno,$errstr,$errfile,$errline){
        //错误信息
        if(!is_string($errstr)) $errstr = serialize($errstr);
        ob_start();
        debug_print_backtrace();
        $vars = [
            'message'   => "{$errno} {$errstr}",
            'position'  => "File:{$errfile}   Line:{$errline}",
            'trace'     => ob_get_clean(),  //回溯信息
        ];
        //TODO:写入日志
        if(DEBUG_MODE_ON){
            self::loadTemplate('error',$vars);
        }else{
            self::loadTemplate('user_error');
        }
        //异常处理完成后仍然会继续执行，需要强制退出
        exit;
    }

    /**
     * 处理异常的发生
     * 开放模式下允许将Exception打印打浏览器中
     * 部署模式下不建议这么做，因为回退栈中可能保存敏感信息
     * @param Exception $e
     * @return void
     */
    public function _handleException(Exception $e){
        Response::cleanOutput();
//        $trace = $e->getTrace();
        $traceString = $e->getTraceAsString();
        //错误信息
        $vars = [
            'message'   => get_class($e).' : '.$e->getMessage(),
            'position'  => 'File:'.$e->getFile().'   Line:'.$e->getLine(),
            'trace'     => $traceString,//回溯信息，可能会暴露数据库等敏感信息
        ];
        //TODO:写入日志
        if(DEBUG_MODE_ON){
            self::loadTemplate('exception',$vars);
        }else{
            self::loadTemplate('user_error');
        }
        //异常处理完成后仍然会继续执行，需要强制退出
        exit;
    }

//--------------------------------------- 静态方法区 ------------------------------------------------------------//
    /**
     * 加载模板
     * @param string $tplname 模板文件路径
     * @param mixed $vars 释放到模板中的变量
     * @param bool $clean 是否清空之前的输出，默认为true
     */
    public static function loadTemplate($tplname,$vars=null,$clean=true){
        $clean and Response::cleanOutput();
        if(is_array($vars)) extract($vars, EXTR_OVERWRITE);
        $path = SYSTEM_PATH."Tpl/{$tplname}.php";
        is_file($path) or $path = SYSTEM_PATH."Tpl/systemerror.php";
        include $path;
    }

    /**
     * 变量跟踪信息
     * @var array
     */
    protected static $_traces = [];
    /**
     * 状态跟踪信息
     * @var array
     */
    protected static $_status = [];

    /**
     * 获取运行时刻内存使用情况
     * @param null|string $tag 状态标签
     * @return void
     */
    public static function recordStatus($tag){
        DEBUG_MODE_ON and self::$_status[$tag] = [
            microtime(true),
            memory_get_usage(),
        ];
    }

    /**
     * 跟踪trace信息
     * @param ...
     * @return void
     */
    public static function trace(){
        $values = func_get_args();
        $trace = debug_backtrace();
        if(isset($trace[0])){
            //显示调用trace方法的行号
            $path = "{$trace[0]['class']}{$trace[0]['type']}{$trace[0]['function']}[Line:{$trace[0]['line']}]";
        }else{//特殊情况，使用特殊值
            $path = uniqid('[ANY]');
        }
        self::$_traces[$path] = $values;
    }

    protected static $_infos = null;

    /**
     * 显示trace页面
     * @param int $accuracy
     */
    protected static function showTrace($accuracy=6){
        //吞吐率  1秒/单次执行时间
        if(count(self::$_status) > 1){
            $last  = end(self::$_status);
            $first = reset(self::$_status);            //注意先end后reset
            $stat = [
                1000*round($last[0] - $first[0], $accuracy),
                number_format(($last[1] - $first[1]), $accuracy)
            ];
        }else{
            $stat = [0,0];
        }
        $reqs = empty($stat[0])?'Unknown':1000*number_format(1/$stat[0],8).' req/s';

        //包含的文件数组
        $files  =  get_included_files();
        $info   =   [];
        foreach ($files as $key=>$file){
            $info[] = $file.' ( '.number_format(filesize($file)/1024,2).' KB )';
        }

        //运行时间与内存开销
        $fkey = null;
        $cmprst = [
            'Total' => "{$stat[0]}ms",//一共花费的时间
        ];
        foreach(self::$_status as $key=>$val){
            if(null === $fkey){
                $fkey = $key;
                continue;
            }
            $cmprst["[$fkey --> $key]    "] =
                number_format(1000 * floatval(self::$_status[$key][0] - self::$_status[$fkey][0]),6).'ms&nbsp;&nbsp;'.
                number_format((floatval(self::$_status[$key][1] - self::$_status[$fkey][1])/1024),2).' KB';
            $fkey = $key;
        }
        $vars = [
            'trace' => [
                'General'       => [
                    'Request'   => date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).' '.$_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'],
                    'Time'      => "{$stat[0]}ms",
                    'QPS'       => $reqs,//吞吐率
                    'SessionID' => session_id(),
                    'Cookie'    => var_export($_COOKIE,true),
                    'Obcache-Size'  => number_format((ob_get_length()/1024),2).' KB (Lack TRACE!)',//不包括trace
                ],
                'Trace'       => self::$_traces,
                'Files'         => array_merge(['Total'=>count($info)],$info),
                'Status'        => $cmprst,
                'GET'           => $_GET,
                'POST'          => $_POST,
                'SERVER'        => $_SERVER,
                'FILES'         => $_FILES,
                'ENV'           => $_ENV,
                'SESSION'       => isset($_SESSION)?$_SESSION:['SESSION state disabled'],//session_start()之后$_SESSION数组才会被创建
                'IP'            => [
                    '$_SERVER["HTTP_X_FORWARDED_FOR"]'  =>  isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'NULL',
                    '$_SERVER["HTTP_CLIENT_IP"]'  =>  isset($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:'NULL',
                    '$_SERVER["REMOTE_ADDR"]'  =>  $_SERVER['REMOTE_ADDR'],
                    'getenv("HTTP_X_FORWARDED_FOR")'  =>  getenv('HTTP_X_FORWARDED_FOR'),
                    'getenv("HTTP_CLIENT_IP")'  =>  getenv('HTTP_CLIENT_IP'),
                    'getenv("REMOTE_ADDR")'  =>  getenv('REMOTE_ADDR'),
                ],
            ],
        ];
        self::loadTemplate('trace',$vars,false);//参数三表示不清空之前的缓存区
    }


}