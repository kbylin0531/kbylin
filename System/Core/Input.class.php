<?php
/**
 * User: linzh
 * Date: 2016/3/18
 * Time: 21:07
 */
namespace System\Core;

use System\Utils\SEK;

class Input {

    const TYPE_ARRAY = 'a';
    const TYPE_INT   = 'i';
    const TYPE_FLOAT = 'f';
    const TYPE_BOOL  = 'b';
    const TYPE_STRING= 's';

    /**
     * 判断一个变量是否设置
     * @param string $name
     * @param array $data
     * @return bool
     */
    public static function has($name, $data){
        return SEK::keysExistInArray($name,$data);
    }
    /**
     * 获取变量 支持过滤和默认值
     * @param array $input 数据源
     * @param string $name 字段名
     * @param mixed $default 默认值
     * @param mixed $filter 过滤函数
     * @param boolean $merge 是否与默认的过虑方法合并
     * @return mixed
     */
    public static function data($input, $name = '', $default = null, $filter = null, $merge = false)
    {
        if (0 === strpos($name, '?')) {
            return self::has(substr($name, 1), $input);
        }
        if (!empty($input)) {
            $data = $input;
            $name = (string) $name;
            if ('' != $name) {
                // 解析name
                list($name, $type) = static::parseName($name);
                // 按.拆分成多维数组进行判断
                foreach (explode('.', $name) as $val) {
                    if (isset($data[$val])) {
                        $data = $data[$val];
                    } else {
                        // 无输入数据，返回默认值
                        return $default;
                    }
                }
            }

            // 解析过滤器
            $filters = static::parseFilter($filter, $merge);
            // 为方便传参把默认值附加在过滤器后面
            $filters[] = $default;
            if (is_array($data)) {
                array_walk_recursive($data, 'self::filter', $filters);
            } else {
                self::filter($data, $name ?: 0, $filters);
            }
            if (isset($type) && $data !== $default) {
                // 强制类型转换
                self::typeCast($data, $type);
            }
        } else {
            $data = $default;
        }
        return $data;
    }

    /**
     * 强类型转换
     * @param string $data
     * @param string $type
     * @return mixed
     */
    protected static function typeCast(&$data, $type)
    {
        switch (strtolower($type)) {
            // 数组
            case self::TYPE_ARRAY:
                $data = (array) $data;
                break;
            // 数字
            case self::TYPE_INT:
                $data = (int) $data;
                break;
            // 浮点
            case self::TYPE_FLOAT:
                $data = (float) $data;
                break;
            // 布尔
            case self::TYPE_BOOL:
                $data = (boolean) $data;
                break;
            // 字符串
            case self::TYPE_STRING:
            default:
                $data = (string) $data;
        }
    }
}