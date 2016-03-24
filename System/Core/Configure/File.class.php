<?php
/**
 * Created by PhpStorm.
 * User: Lin
 * Date: 2016/1/21
 * Time: 20:02
 */
namespace System\Core\Configure;
use System\Core\BylinException;
use System\Core\Cache;

/**
 * Class File 配置管理的文件驱动（操作的配置军事PHP类型）
 *
 * 无论是自定义的文件类型还是系统设定的php配置文件 - 都已php后缀结尾
 *
 * @package System\Core\Configure
 */
class File implements ConfigureInterface {

    protected $convention = [
        //-- 系统全局配置 --//
        'GLOBAL_CONF_PATH'  => CONFIG_PATH, // 全局配置目录
        'GLOBAL_CONF_LIST'    => [
            'router',//路由配置
            'custom',
            'dao',
            'log',
            'view',
        ],

        //-- 自定义配置 --//
        'CUSTOM_CONF_PATH'  => RUNTIME_PATH.'Config/', // 用户自定义配置目录

        //-- 配置缓存相关 --//
        'CACHE_DEFAULT_ID'  => 0,
        'CACHE_ON'          => true,
        'CACHE_PATH'        => RUNTIME_PATH.'configure.cache', // 陪住缓存文件路径
        'CACHE_EXPIRE'      => 0,
        'CACHE_COMPRESS'    => false, //是否进行数据压缩
    ];

    /**
     * 配置缓存
     * @var array
     */
    protected $cache = [];

    /**
     * File constructor.
     * @param array $conf
     * @throws BylinException 无法建立缓存时抛出
     */
    public function __construct(array $conf){
        $this->convention = array_merge($this->convention,$conf);
        $this->convention['CACHE_ON'] and $this->checkCache();
    }

    /**
     * 读取单个的配置
     * @param string $item 配置文件名称
     * @param bool $iscustom 是自定义的配置还是全局的，默认为true(自定义配置)
     * @return array|null 返回配置数组，不存在指定配置时候返回null
     */
    public function read($item, $iscustom=true){
        if(!isset($this->cache[$item])) {
            $path = $this->item2path($item,$iscustom,true);
            if(null === $path) return null;//文件不存在，返回null

            if($iscustom){
                $config = @unserialize(file_get_contents($path));//无法反序列化的内容会抛出错误E_NOTICE，使用@进行忽略，但是不要忽略返回值
                $this->cache[$item] = false === $config ? null : $config;
            }else{
                $this->cache[$item] = include $path;
            }
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
        return file_put_contents($path,serialize($config)) !== false; //闭包函数无法写入
    }

    /**
     * 将配置内容存储到缓存中
     * @param array $config 配置数组
     * @param int $expire 以秒计算的缓存时间,缓存时间为0表示永不过期
     * @return bool 缓存是否成功
     */
    public function store(array $config,$expire=null){
        null === $expire and $expire = $this->convention['CACHE_EXPIRE'];
        //设置缓存数据格式
        $data     = serialize($config);
        //数据压缩
        if ($this->convention['CACHE_COMPRESS'] && function_exists('gzcompress'))  $data = gzcompress($data, 3);
        //加入缓存时间,数字补长到12位
        $data   = sprintf('%012d', $expire) . $data;//设置数据
        //执行缓存
        $result = file_put_contents($this->convention['CACHE_PATH'], $data);
        return  $result != false ; // != false 保证返回的是布尔值
    }

    /**
     * 加载缓存的配置信息并返回
     * @param array|null $confcache 外部的配置缓存（有失不使用该驱动类自身的配置缓存的情况下），
     *                              非null时将参数一的配置作为实际的缓存配置
     * @return bool 表示是否加载成功
     * @throws BylinException
     */
    public function load(array $confcache=null){
        $cachepath = $this->convention['CACHE_PATH'];//缓存文件
        if (!is_file($cachepath))  return null;//缓存文件不存在

        $content = file_get_contents($cachepath);
        if(false === $content) throw new BylinException("获取文件[{$cachepath}]的内容失败！"); //文件存在的情况下还读取不到内容，抛异常

        $expire = intval(substr($content,0,12));//0开始读取12位

        if (0 !== $expire and time() > filemtime($cachepath) + $expire) {
            //缓存过期删除缓存文件
            unlink($cachepath);
            return null;//缓存过期
        }
        $content = substr($content, 12);
        if ($this->convention['CACHE_COMPRESS'] and  function_exists('gzcompress')) {
            //解压数据
            $content = gzuncompress($content);
        }
        $this->cache = @unserialize($content);
        return $this->cache === false? false : true;
    }

    /**
     * 检查缓存配置
     * @return void
     * @throws BylinException
     */
    protected function checkCache(){
        $cache = $this->load();
        if(null === $cache){
            foreach($this->convention['GLOBAL_CONF_LIST'] as $name){
                $val = $this->read($name,false);
                if(null === $val) throw new BylinException("读取不到全局的配置项[$name]！");
                $this->cache[$name] = $val;
            }
            if($this->store($this->cache,$this->convention['CACHE_EXPIRE']))
                throw new BylinException("缓存配置的过程出现错误！");
        }else{
            $this->cache = $cache;
        }
    }

    /**
     * 将配置项转换成配置文件路径
     * @param string $item 配置项
     * @param bool $iscustom 是否是自定义的配置，为true时表示读取的是自定义写入的配置
     * @param mixed $check 检查文件是否存在
     * @return null|string 返回配置文件路径，参数二位true并且文件不存在时返回null
     */
    protected function item2path($item,$iscustom=true,$check=true){
        $path = $iscustom?$this->convention['CUSTOM_CONF_PATH']:$this->convention['GLOBAL_CONF_PATH'];
        $path = "{$path}{$item}.php";
        if($check and !is_file($path)) return null;
        return ($check and !is_file($path))?null:$path;
    }

}