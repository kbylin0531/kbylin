<?php
/**
 * User: Administrator
 * Date: 2015/8/25
 * Time: 9:08
 */
namespace System\Core;
use System\Core\Storage\File;
use System\Traits\Crux;

defined('BASE_PATH') or die('No Permission!');
/**
 * Class Storage 持久化存储类
 * 实际文件可能写在伺服器的文件中，也可能存放到数据库文件中，或者远程文件服务器中
 * @package System\Core
 */
class Storage {
    use Crux;

    const CONF_NAME = 'storage';
    const CONF_CONVENTION = [
        'DRIVER_DEFAULT_INDEX' => 0,
        'DRIVER_CLASS_LIST' => [
            File::class,
        ],
        'DRIVER_CONFIG_LIST' => [],
    ];

    /**
     * 获取文件内容
     * @param string $filepath 文件路径
     * @param string $file_encoding 文件内容实际编码
     * @param string $output_encode 文件内容输出编码
     * @return string|false 文件不存在时返回false
     */
    public static function read($filepath,$file_encoding='UTF-8',$output_encode='UTF-8'){
        return self::getInstance()->read($filepath,$file_encoding,$output_encode);
    }

    /**
     * 文件写入
     * @param string $filepath 文件名
     * @param string $content 文件内容
     * @param string $write_encode 文件写入编码
     * @return int 返回写入的字节数目,失败时抛出异常
     */
    public static function write($filepath,$content,$write_encode='UTF-8'){
        return self::getInstance()->write($filepath,$content,$write_encode);
    }

    /**
     * 文件追加写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  追加的文件内容
     * @param string $write_encode 文件写入编码
     * @return string 返回写入内容
     */
    public static function append($filename,$content,$write_encode='UTF-8'){
        return self::getInstance()->append($filename,$content,$write_encode);
    }

    /**
     * 文件是否存在
     * @access public
     * @param string $filename  文件名
     * @return boolean
     */
    public static function has($filename){
        return self::getInstance()->has($filename);
    }


    /**
     * 文件删除
     * @access public
     * @param string $filename  文件名
     * @return boolean
     */
    public static function unlink($filename){
        return self::getInstance()->unlink($filename);
    }

    /**
     * 返回文件内容上次的修改时间
     * 可以使用stat获取信息
     * @access public
     * @param string $filename  文件名
     * @return array|mixed
     */
    public static function filemtime($filename){
        return self::getInstance()->filemtime($filename);
    }

    /**
     * 获取文件大小
     * @param string $filename 文件路径信息
     * @return mixed
     */
    public static function filesize($filename){
        return self::getInstance()->filesize($filename);
    }

    /**
     * 删除文件夹
     * @param string $dir 文件夹目录
     * @param bool $recursion 是否递归删除
     * @return bool true成功删除，false删除失败
     */
    public static function removeFolder($dir,$recursion=false) {
        return self::getInstance()->removeFolder($dir,$recursion);
    }

    /**
     * 创建文件夹
     * 如果文件夹已经存在，则修改权限
     * @param string $fullpath 文件夹路径
     * @param int $auth 文件权限，八进制表示
     * @return bool
     */
    public static function makeFolder($fullpath,$auth = 0755){
        return self::getInstance()->makeFolder($fullpath,$auth);
    }
    /**
     * 读取文件夹内容，并返回一个数组(不包含'.'和'..')
     * array(
     *      //文件内容  => 文件内容
     *      'filename' => 'file full path',
     * );
     * @param string $dir 文件夹路径
     * @return array
     */
    public static function readFolder($dir){
        return self::getInstance()->readFolder($dir);
    }

}