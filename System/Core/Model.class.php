<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2016/2/2
 * Time: 9:40
 */
namespace System\Core;
use System\Core\Model\Dao;
use System\Traits\Crux;

/**
 * Class Model 模型类
 * @package System\Core
 */
class Model {

    use Crux;

    /**
     * 模型对应的数据表的实际名称
     * @var string
     */
    protected $_table = null;

    /**
     * 链式操作数据源
     * 只有非null且符合SQL语法的将被加入到SQL的组件中
     * @var array
     */
    protected $_option = [
        'distinct'  => false,
        'top'       => '',
        'fields'    => ' * ', //查询的表域情况
        'join'      => '',     //join部分，需要带上join关键字
        'where'     => '', //where部分
        'group'     => '', //分组 需要带上group by
        'having'    => '',//having子句，依赖$group存在，需要带上having部分
        'order'     => '',//排序，不需要带上order by
        'limit'     => null,
        'offset'    => null,
    ];

    /**
     * 数据表的字段
     * @var array
     */
    protected $_fields = null;

    /**
     * 数据表主键，如果是复合主键则为array形式
     * @var string|array
     */
    protected $_primary_key = 'id';

    /**
     * 当前操作的Dao对象
     * @var Dao
     */
    protected $_dao = null;

    /**
     * Model constructor.
     */
    public function __construct(){
        $classname = static::class;
        defined("{$classname}::TABLE_NAME") and $this->_table = $classname::TABLE_NAME;

        $this->reset();

    }

    /**
     * 获取模型对应的Dao
     * 期中包含了Dao对象的创建工作
     * @param array|null $config
     * @return Dao
     */
    public function getDao(array $config=null){
        isset($this->dao) and $this->_dao = Dao::getDriverInstance($config);
        return $this->_dao;
    }

    /**
     * 将链式操作设置的组件组成一段完整的SQL
     * @return $this
     */
    public function build(){}

    /**
     * 添加数据
     * <code>
     *      $fldsMap ==> array(
     *          'fieldName' => 'fieldValue',
     *          'fieldName' => array('fieldValue',boolean),//第二个元素表示是否对字段名称进行转义
     *      );
     *
     *     $data = ['a'=>'foo','b'=>'bar'];
     *     $keys = array_keys($data);
     *     $fields = '`'.implode('`, `',$keys).'`';
     *     #here is my way
     *     $placeholder = substr(str_repeat('?,',count($keys),0,-1));
     *     $pdo->prepare("INSERT INTO `baz`($fields) VALUES($placeholder)")->execute(array_values($data));
     * </code>
     *
     * 插入数据的sql可以是：
     * ①INSERT INTO 表名称 VALUES (值1, 值2,....)
     * ②INSERT INTO table_name (列1, 列2,...) VALUES (值1, 值2,....)
     *
     * @param string $tablename
     * @param array $fieldsMap
     * @return bool 返回true或者false
     * @throws BylinException
     */
    public function create($tablename,$fieldsMap){
        $fields = $placeholder = '';
        $sql = null;
        $bind  = [];
        $flag = true;//标记是否进行插入形式判断

        $dao  = $this->getDao();


        foreach($fieldsMap as $fieldName=>$fieldValue){
            $colnm = $fieldName;
            if($flag){
                if(is_numeric($fieldName)){
                    $placeholder  = rtrim(str_repeat(' ?,',count($fieldsMap)),',');
                    $sql = "INSERT INTO {$tablename} VALUES ( {$placeholder} );";
                    $bind = $fieldsMap;
                    break;
                }
                $flag = false;
            }
            if(is_array($fieldValue)){ //不设置字段名称进行插入时$fieldName无意义
                $colnm = $fieldValue[1]?$dao->escape($fieldName):$fieldName;
                $fieldValue = $fieldValue[0];
            }
            $fields .= " {$colnm} ,";
            $placeholder  .= " :{$fieldName} ,";
            $bind[":{$fieldName}"] = $fieldValue;
        }

        if(isset($sql)){
            $fields = rtrim($fields,',');
            $sql = "INSERT INTO {$tablename} ( {$fields} ) VALUES ( {$placeholder} );";
        }
        return $dao->prepare($sql)->execute($bind);
    }

    /**
     * 更新数据表
     * @param string $tablename
     * @param string|array $flds
     * @param string|array $whr
     * @return bool
     * @throws BylinException
     */
    public function update($tablename,$flds,$whr){;
        $input_params = [];
        $fields = is_string($flds)?[$flds,[]]:$this->makeSegments($flds,false);
        $where  = is_string($whr) ?[$whr,[]] :$this->makeSegments($whr, false);
        empty($fields[1]) or $input_params = $fields[1];
        empty($where[1]) or array_merge($input_params,$where[1]);
        return $this->getDao()->prepare("UPDATE {$tablename} SET {$fields[0]} WHERE {$where[0]};")->execute($input_params);
    }

    /**
     * 执行删除数据的操作
     * 如果不设置参数，则进行清空表的操作
     * @param string $tablename 数据表的名称
     * @param array $whr 字段映射数组
     * @return bool
     */
    public function delete($tablename,$whr=null){
        $bind = null;
        if(isset($whr)){
            $where  = $this->makeSegments($whr);
            $sql    = "delete from {$tablename} where {$where[0]};";
            $bind   = $where[1];
        }else{
            $sql = "delete from {$tablename};";
        }
        return $this->getDao()->prepare($sql)->execute($bind);
    }

    /**
     * 查询表的数据
     * @param string $tablename
     * @param string|array|null $fields
     * @param string|array|null $whr
     * @return array|bool
     * @throws BylinException
     */
    public function select($tablename=null,$fields=null,$whr=null){
        $this->_dao = $this->getDao();

        if(null === $tablename){
            $combined = $this->combine();

            if(false === $this->_dao->prepare($combined[0])->execute($combined[1])) return false;

            return $this->_dao->fetchAll();
        }



        $bind = null;


        //设置选取字段
        if(null === $fields){
            $fields = ' * ';
        }elseif($fields and is_array($fields)){
            //默认转义
            array_map(function($param){
                return $this->_dao->escape($param);
            },$fields);
            $fields = implode(',',$fields);
        }elseif(!is_string($fields)){
            throw new BylinException('Parameter 2 require the type of "null","array","string" ,now is invalid!');
        }

        if(null === $whr){
            $sql = "select {$fields} from {$tablename};";
        }elseif(is_array($whr)){
            $whr  = is_string($whr)? [$whr,null] :$this->makeSegments($whr);
            $sql = "select {$fields} from {$tablename} where {$whr[0]};";
            $bind = $whr[1];
        }elseif(is_string($whr)){
            $sql = "select {$fields} from {$tablename} where {$whr};";
        }else{
            throw new BylinException('Parameter 3 require the type of "null","array","string" ,now is invalid!');
        }


        if(false === $this->_dao->prepare($sql)->execute($bind) ){
            return false;
        }
        return $this->_dao->fetchAll();
    }

    /**
     * 综合字段绑定的方法
     * <code>
     *      $operator = '='
     *          $fieldName = :$fieldName
     *          :$fieldName => trim($fieldValue)
     *
     *      $operator = 'like'
     *          $fieldName = :$fieldName
     *          :$fieldName => dowithbinstr($fieldValue)
     *
     *      $operator = 'in|not_in'
     *          $fieldName in|not_in array(...explode(...,$fieldValue)...)
     * </code>
     * @param string $fieldName 字段名称
     * @param string|array $fieldValue 字段值
     * @param string $operator 操作符
     * @param bool $translate 是否对字段名称进行转义,MSSQL中使用[]
     * @return array
     * @throws BylinException
     */
    protected function makeFieldBind($fieldName,$fieldValue,$operator='=',$translate=false){
        $fieldName = trim($fieldName,' :[]');
        $bindFieldName = null;
        if(false !== strpos($fieldName,'.')){
            $arr = explode('.',$fieldName);
            $bindFieldName = ':'.array_pop($arr);
        }elseif(mb_strlen($fieldName,'utf-8') < strlen($fieldName)){//其他编码
            $bindFieldName = ':'.md5($fieldName);
        }else{
            $bindFieldName = ":{$fieldName}";
        }

        $operator = strtolower(trim($operator));
        $sql = $translate?" [{$fieldName}] ":" {$fieldName} ";
        $bind = array();

        switch($operator){
            case '=':
                $sql .= " = {$bindFieldName} ";
                $bind[$bindFieldName] = $fieldValue;
                break;
            case 'like':
                $sql .= " like {$bindFieldName} ";
                $bind[$bindFieldName] = $fieldValue;
                break;
            case 'in':
            case 'not in':
                if(is_string($fieldValue)){
                    $sql .= " {$operator} ({$fieldValue}) ";
                }elseif(is_array($fieldValue)){
                    $sql .= " {$operator} ('".implode("','",$fieldValue)."')";
                }else{
                    throw new BylinException("The parameter 1 '{$fieldValue}' is invalid!");
                }
                break;
            default:
                throw new BylinException("The parameter 2 '{$operator}' is invalid!");
        }
        return [$sql,$bind];
    }

    /**
     * 片段设置
     * <note>
     *      片段准则
     *      $map == array(
     *           //第一种情况,连接符号一定是'='//
     *          'key' => $val,
     *          'key' => array($val,$operator,true),
     *          //第二种情况，数组键，数组值//
     *          array('key','val','like|=',true),//参数4的值为true时表示对key进行[]转义
     *          //第三种情况，字符键，数组值//
     *          'assignSql' => array(':bindSQLSegment',value)//与第一种情况第二子目相区分的是参数一以':' 开头
     *      );
     * </note>
     * @param $map
     * @param string $connect 表示是否使用and作为连接符，false时为,
     * @return array
     */
    public function makeSegments($map,$connect='and'){
        //初始值与参数检测
        $bind = [];
        $sql = '';
        if(empty($map)){
            return [$sql,$bind];
        }

        //元素连接
        foreach($map as $key=>$val){
            if(is_numeric($key)){
                //第二种情况
                $rst = $this->makeFieldBind(
                    $val[0],
                    $val[1],
                    isset($val[2])?$val[2]:' = ',
                    !empty($val[3])
                );
                if(is_array($rst)){
                    $sql .= " {$rst[0]} {$connect}";
                    $bind = array_merge($bind, $rst[1]);
                }
            }elseif(is_array($val) and strpos($val[0],':') === 0){
                //第三种情况,复杂类型，由用户自定义
                $sql .= " {$key} {$connect}";
                $bind[$val[0]] = $val[1];
            }else{
                //第一种情况
                $translate = false;
                $operator = '=';
                if(is_array($val)){
                    $translate = isset($val[2])?$val[2]:false;
                    $operator = isset($val[1])?$val[1]:'=';
                    $val = $val[0];
                }
                $rst = $this->makeFieldBind($key,trim($val),$operator,$translate);//第一种情况一定是'='的情况
                if(is_array($rst)){
                    $sql .= " {$rst[0]} {$connect}";
                    $bind = array_merge($bind, $rst[1]);
                }
            }
        }
        $result = array(
            substr($sql,0,strlen($sql)-strlen($connect)),//去除最后一个and
            $bind,
        );
        return $result;
    }

    /**
     * 获取错误
     * @return string|null 有错误发生时返回string，否则返回null
     */
    public function error(){
        return $this->getDao()->getErrorInfo();
    }




//***************** 链式操作 ********************************//
    /**
     * 设置要操作的表
     * @param string $tablename 设置当前操作的数据表的名称
     * @return $this
     */
    public function table($tablename){
        $this->_option['table'] = $tablename;
        return $this;
    }

    /**
     * 设置查询或修改的字段
     * @param string|array $fields
     * @return $this
     */
    public function fields($fields){
        $this->_option['fields'] = $fields;
        return $this;
    }

    /**
     * 设置where
     * @param string|array $where
     * @return $this
     */
    public function where($where){
        $this->_option['where'] = $where;
        return $this;
    }

    /**
     * @param string|array $join
     * @return $this
     */
    public function join($join){
        $this->_option['join'] = $join;
        return $this;
    }

    /**
     * 设置group by
     * @param string|array $group
     * @return $this
     */
    public function group($group){
        $this->_option['group'] = $group;
        return $this;
    }

    /**
     * 设置order by
     * @param string|array $order
     * @return $this
     */
    public function order($order){
        $this->_option['order'] = $order;
        return $this;
    }

    /**
     * 设置top部分（部分数据库有效）
     * @param int $num
     * @return $this
     */
    public function top($num){
        $this->_option = intval($num);
        return $this;
    }

    /**
     * 各个数据库中表现一致
     * @param bool $dist 是否进行distinct
     * @return $this
     */
    public function distinct($dist=true){
        $this->_option['distinct'] = $dist;
        return $this;
    }

    /**
     * 参阅mysql中的'limit X,Y'
     * 各个数据库中的实现不一致
     * @param null|int $limit
     * @param null|int $offset
     * @return $this
     */
    public function limit($limit=null,$offset=null){
        $this->_option['offset'] = $offset;
        $this->_option['limit'] = $limit;
        return $this;
    }

    /**
     * 组合sql的各个部分并返回结果
     *
     * 返回值：
     *  [
     *      sql_string  ,
     *      input_params    => null,
     *  ]
     * @return array
     */
    public function combine(){
        $this->getDao()->compile($this->_option);
        $this->reset();
        return [];
    }



    /**
     * 重置结果集合
     * @return void
     */
    public function reset(){
        $this->_option = [
            'distinct'  => false,
            'top'       => '',
            'fields'    => ' * ', //查询的表域情况
            'join'      => '',     //join部分，需要带上join关键字
            'where'     => '', //where部分
            'group'     => '', //分组 需要带上group by
            'having'    => '',//having子句，依赖$group存在，需要带上having部分
            'order'     => '',//排序，不需要带上order by
            'limit'     => null,
            'offset'    => null,
        ];
    }


}