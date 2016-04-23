<<<<<<< HEAD
<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2016/1/21
 * Time: 20:02
 */
namespace System\Core\Config;
use System\Core\KbylinException;
use System\Core\Cache;
use System\Core\Storage;

/**
 * Class File 配置管理的文件驱动（操作的配置军事PHP类型）
 *
 * 无论是自定义的文件类型还是系统设定的php配置文件 - 都已php后缀结尾
 *
 * @package System\Core\Configure
 */
class File implements ConfigInterface {

    protected $convention = [
        'CUSTOM_CONF_PATH'  => RUNTIME_PATH.'Config/', // 用户自定义配置目录
    ];

    /**
     * 文件系统是否可用
     * @return bool
     */
    public function available(){
        return is_readable(RUNTIME_PATH) and is_writable(RUNTIME_PATH);
    }

    /**
     * 配置缓存
     * @var array
     */
    protected $cache = [];

    /**
     * File constructor.
     * @param array $conf
     * @throws KbylinException 无法建立缓存时抛出
     */
    public function __construct(array $conf){
        $this->convention = array_merge($this->convention,$conf);
    }

    /**
     * 读取单个的配置
     * @param string $item 配置文件名称
     * @return array|null 返回配置数组，不存在指定配置时候返回null
     */
    public function read($item){
        if(!isset($this->cache[$item])) {
            $path = $this->item2path($item,true);
            if(null === $path) return null;//文件不存在，返回null
            $content = Storage::read($path);
            if(null === $content) return null;
            $config = @unserialize($content);//无法反序列化的内容会抛出错误E_NOTICE，使用@进行忽略，但是不要忽略返回值
            $this->cache[$item] = false === $config ? null : $config;
        }
        return $this->cache[$item];
    }

    /**
     * 写入单个持久化的配置
     * @param string $item 配置文件名称
     * @param array $config 写入的配置信息
     * @param bool $cover 是否覆盖原先的配置,默认为true（是）
     * @return bool 返回false表示写入失败
     */
    public function write($item,array $config,$cover=true){
        $path = $this->item2path($item,false);//不检查文件是否已经存在
        if($cover and is_file($path)) return false;//文件已经存在，写入失败
        return Storage::write($path,serialize($config)) !== false; //闭包函数无法写入
    }

    /**
     * 将配置项转换成配置文件路径
     * @param string $item 配置项
     * @param mixed $check 检查文件是否存在
     * @return null|string 返回配置文件路径，参数二位true并且文件不存在时返回null
     */
    protected function item2path($item,$check=true){
        $path = $this->convention['CUSTOM_CONF_PATH']."{$item}.php";
        if($check and !is_file($path)) return null;
        return ($check and !is_file($path))?null:$path;
    }
=======
<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2016/1/21
 * Time: 20:02
 */
namespace System\Core\Config;
use System\Core\KbylinException;
use System\Core\Cache;
use System\Core\Storage;

/**
 * Class File 配置管理的文件驱动（操作的配置军事PHP类型）
 *
 * 无论是自定义的文件类型还是系统设定的php配置文件 - 都已php后缀结尾
 *
 * @package System\Core\Configure
 */
class File implements ConfigInterface {

    protected $convention = [
        'CUSTOM_CONF_PATH'  => RUNTIME_PATH.'Config/', // 用户自定义配置目录
    ];

    /**
     * 文件系统是否可用
     * @return bool
     */
    public function available(){
        return is_readable(RUNTIME_PATH) and is_writable(RUNTIME_PATH);
    }

    /**
     * 配置缓存
     * @var array
     */
    protected $cache = [];

    /**
     * File constructor.
     * @param array $conf
     * @throws KbylinException 无法建立缓存时抛出
     */
    public function __construct(array $conf){
        $this->convention = array_merge($this->convention,$conf);
    }

    /**
     * 读取单个的配置
     * @param string $item 配置文件名称
     * @return array|null 返回配置数组，不存在指定配置时候返回null
     */
    public function read($item){
        if(!isset($this->cache[$item])) {
            $path = $this->item2path($item,true);
            if(null === $path) return null;//文件不存在，返回null
            $content = Storage::read($path);
            if(null === $content) return null;
            $config = @unserialize($content);//无法反序列化的内容会抛出错误E_NOTICE，使用@进行忽略，但是不要忽略返回值
            $this->cache[$item] = false === $config ? null : $config;
        }
        return $this->cache[$item];
    }

    /**
     * 写入单个持久化的配置
     * @param string $item 配置文件名称
     * @param array $config 写入的配置信息
     * @param bool $cover 是否覆盖原先的配置,默认为true（是）
     * @return bool 返回false表示写入失败
     */
    public function write($item,array $config,$cover=true){
        $path = $this->item2path($item,false);//不检查文件是否已经存在
        if($cover and is_file($path)) return false;//文件已经存在，写入失败
        return Storage::write($path,serialize($config)) !== false; //闭包函数无法写入
    }

    /**
     * 将配置项转换成配置文件路径
     * @param string $item 配置项
     * @param mixed $check 检查文件是否存在
     * @return null|string 返回配置文件路径，参数二位true并且文件不存在时返回null
     */
    protected function item2path($item,$check=true){
        $path = $this->convention['CUSTOM_CONF_PATH']."{$item}.php";
        if($check and !is_file($path)) return null;
        return ($check and !is_file($path))?null:$path;
    }
>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
}