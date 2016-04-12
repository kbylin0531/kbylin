<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2016/1/21
 * Time: 16:41
 */
namespace System\Core;
use System\Core\Config\File;
use System\Core\Exception\ParameterInvalidException;
use System\Traits\Crux;
use System\Utils\SEK;

//use Spyc;
/**
 * Class Configure 设定管理器
 * @package System\Core
 */
class Config {

    use Crux;

    const CONF_NAME = 'configure';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,
        'DRIVER_CLASS_LIST' => [
            File::class,
        ],
        'DRIVER_CONFIG_LIST' => [
        ],
        'CONFIG_CACHE_LIST'     => [],
        'CONFIG_CACHE_EXPIRE'   => 0,
    ];

    /**
     * 配置缓存
     * @var array
     */
    protected static $confcache = [];


    /**
     * <不存在依赖关系>
     * 读取全局配置
     * 设定在 'CONFIG_PATH' 目录下的配置文件的名称
     * @param string|array $itemname 自定义配置项名称
     * @return array|mixed 配置项存在的情况下返回array，否则返回参数$replacement的值
     * @throws ParameterInvalidException
     */
    public static function readGlobal($itemname){
        $type = gettype($itemname);
        if('array' === $type){
            $result = [];
            foreach($itemname as $item){
                $temp = self::readGlobal($item);
                null !== $temp and SEK::merge($result,$temp);
            }
        }elseif('string' === $type){
            $path = CONFIG_PATH."{$itemname}.php";
            if(!is_file($path)) return null;
            $result = include $path;
        }else{
            throw new ParameterInvalidException($itemname);
        }
        return $result;
    }

    /**
     * 读取所有全局配置
     * @param string|array $list 配置列表,可以是数组，也可以是都好分隔的字符串
     * @return array 返回全部配置，配置名称为键
     * @throws ParameterInvalidException
     */
    public static function getAllGlobal($list){
        if(is_string($list)) $list = explode(',',$list);
        //无法读取驱动内部的缓存或者缓存不存在  => 重新读取配置并生成缓存
        foreach($list as $item){
            self::$confcache[$item] = self::readGlobal($item);
        }
        return self::$confcache;
    }

    /**
     * 创建配置系统全局缓存
     * @param array $cachedata 缓存的数据
     * @param int $expire
     * @return bool 创建缓存是否成功
     * @throws BylinException
     */
    public static function setGlobalCache(array $cachedata=null, $expire=null){
        if(!isset($cachedata,$expire)){
            self::checkInitialized(true);
            $config = self::getConventions();
            null === $cachedata and $cachedata = self::getAllGlobal($config['CONFIG_CACHE_LIST']);
            null === $expire  and $expire = $config['CONFIG_CACHE_EXPIRE'];
        }
        return self::getDriverInstance()->storeCache($cachedata,$expire);
    }

    /**
     * 加载配置缓存
     * @param array|null $cachedata 配置缓存数据，设置了该值可以跳过遍历读取以提高效率
     * @return array|null
     */
    public static function getGlobalCache(array $cachedata=null){
        if(null !== $cachedata){
            //全部的配置缓存，包括自身的配置
            self::$confcache = $cachedata;
        }else{
            self::checkInitialized(true);
            $instance = self::getDriverInstance();
            //加载驱动内置的缓存
            $cachedata  = $instance->loadCache();
            if(null === (self::$confcache = $cachedata)){
                self::setGlobalCache() and \Bylin::recordStatus('build cache success!');
            }
        }
        return self::$confcache;
    }

    /**
     * 写入自定义配置项
     * @param string $itemname 自定义配置项名称
     * @param array $config 配置数组
     * @param int $expire 以秒计算的缓存时间
     * @return bool 写入成功与否
     */
    public static function writeCustomConfig($itemname,array $config,$expire=null){
        return self::getDriverInstance()->write($itemname,$config,$expire);
    }

    /**
     * 读取自定义配置
     * @param string $itemname 自定义配置项名称
     * @param mixed|null $replacement 当指定的配置项不存在的时候的替代值
     * @return array|mixed 配置项存在的情况下返回array，否则返回参数$replacement的值
     */
    public static function readCustomConfig($itemname,$replacement=null){
        $result = self::getDriverInstance()->read($itemname);
        return null === $result?$replacement:$result;
    }


    /**
     * 获取配置信息
     * 示例：
     *  database.DB_CONNECT.0.type
     * 除了第一段外要注意大小写
     * @param string|null|array $items 配置项
     * @param mixed|null $replacement 当指定的配置项不存在时,仅仅在获取第二段开始的部分时有效
     * @return mixed 返回配置信息数组
     * @throws BylinException
     */
    public static function get($items=null,$replacement=null){
        //检查配置缓存
        self::checkInitialized(true);

        $configes = null;//配置分段，如果未分段则保持null的值
        //检查参数并设置分段
        if(null === $items){
            //默认参数时返回全部
            return self::$confcache;
        }elseif(is_string($items)){
            $configes = false === strpos($items,'.')?[$items]:explode('.',$items);
        }elseif(is_array($items)){
            $configes = $items;
        }

        //获取第一段的配置
        $rtn = self::$confcache[array_shift($configes)];

        //如果为true表示是经过分段的
        if($configes){
            foreach($configes as $val){
                if(isset($rtn[$val])){
                    $rtn = $rtn[$val];
                }else{
                    return $replacement;
                }
            }
        }
        return $rtn;
    }

    /**
     * 设置临时配置项
     * 下次请求时临时的配置将被清空
     * <code>
     *  UDK::dump(Configer::get());
     *  Configer::set('custom.NAME.VALUE',true);
     *  UDK::dump(Configer::get());
     * </code>
     * @param string $items 配置项名称，同get方法，可以是分段的设置
     * @param mixed $value 配置项的值
     * @return bool
     * @throws BylinException 要设置的第一项不存在时抛出异常
     */
    public static function set($items,$value){
        //检查配置缓存
        self::checkInitialized(true);

        $configes = null;//配置分段，如果未分段则保持null的值
        if(false !== strpos($items,'.')){
            $configes = explode('.',$items);
            $items = array_shift($configes);
        }
        if(!isset(self::$confcache[$items])){//不存在该配置
            if(!is_array(self::readGlobal($items))){
                return false;//不存在该配置，设置失败
            }
        }

        $confvars = &self::$confcache[$items];
        if($configes){
            foreach($configes as $item){
                if(!isset($confvars[$item])){
                    $confvars[$item] = [];
                }
                $confvars = &$confvars[$item];
            }
        }
        $confvars = $value;
        return true;
    }

}