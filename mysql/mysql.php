<?php
/**
 * @Author:      xp
 * @DateTime:    2015-04-18 17:35:08
 * @Description: mysqli类
 */

class mysqlidb
{

    // 数据库配置
    public $db_config = [
        'db_host' => 'host',
        'db_port' => '3306',
        'db_user' => 'user',
        'db_pass' => 'password',
        'db_name' => 'name',
        'db_char' => 'utf8',
        'db_prefix' => '',
    ];
    // 链接资源
    private $link;
    // 插入的id
    private $insertid;
    // 最后的sql语句
    private $lastSql;
    // 表前缀
    private $prefix;

    public function __construct($db_config = null)
    {
        if ($db_config) {
            $this->db_config = $db_config;
        }
        $this->prefix = $this->db_config['db_prefix'];
        $this->connect();
    }

    /**
     * 链接数据库
     * @access public
     * @return resource
     */
    public function connect()
    {
        $this->link = mysqli_connect(
            $this->db_config['db_host'],
            $this->db_config['db_user'],
            $this->db_config['db_pass'],
            $this->db_config['db_name'],
            $this->db_config['db_port']
        );
        if ( mysqli_connect_error ()) {
            die( 'Connect Error ('  .  mysqli_connect_errno () .  ') '
                     .  mysqli_connect_error ());
        }

        if (!$this->query("set names ".$this->db_config['db_char'])) {
            exit('编码设置失败');
        }
    }

    /**
     * 获得所有数据
     * @access public
     * @return array
     */
    public function getAll($table, $field = '*', $where = 1, $group='', $having='', $order='', $limit='')
    {
        $table = $this->jointable($table);
        $sql = 'select ';
        $where = $where == 1 ? '' : ' where '.$where;
        $group = $group == '' ? '' : ' group by '.$group ;
        $having = $having == '' ? '' : ' having '.$having;
        $order = $order == '' ? '' : ' order by '.$order ;
        $limit = $limit == '' ? '' : ' limit '.$limit;
        $sql = $sql . $field . ' from '.$table.$where.$group.$having.$order.$limit;
        return $this->query($sql);
    }

    /**
     * 获得一行数据
     * @access public
     * @param where str
     * @return array
     */
    public function getRow($table, $filed='*', $where)
    {
        $table = $this->jointable($table);
        $sql = "select ".$filed." from ".$table." where ".$where." limit 1";
        $res = $this->query($sql);
        return empty($res) ? false : $res[0] ;
    }

    /**
     * 获得ä¸个数据
     * @access public
     * @param field str
     * @param where str
     * @return mix
     */
    public function getOne($table, $field, $where)
    {
        $table = $this->jointable($table);
        $sql = "select ".$field." from ".$table." where ".$where;
        $res = $this->query($sql);
        return empty($res) ? false : $res[0] ;
    }

    /**
     * 删除数据
     * @access public
     * @param where  str
     * @return bool
     */
    public function delete($table, $where)
    {
        $table = $this->jointable($table);
        if (empty($where)) {
            exit();
        }
        $sql = 'delete from '.$table.' where '.$where;
        return $this->query($sql);
    }

    /**
     * 更新数据
     * @access public
     * @param where  str
     * @param arr  array
     * @return bool
     */
    public function update($table, $arr, $where)
    {
        $table = $this->jointable($table);
        if (empty($where)) {
            exit();
        }
        $sql = "update ".$table." set ";
        foreach ($arr as $key => $value) {
            $value = $this->parseValue($value);
            $set[]    = $key.'='.$value;
        }
        $sql .= implode(',', $set);
        $where = " where ".$where;
        $sql .= $where;
        return $this->query($sql);
    }

    /**
     * 增加数据
     * @access public
     * @param arr array
     * @return int  insertid
     */
    public function add($table, $arr)
    {
        $table = $this->jointable($table);

        $sql = '';
        $sql .= 'insert into '.$table.' ';
        foreach ($arr as $key => $value) {
            $values[]=$this->parseValue($value);
        }
        $sql .= '('. implode(',', array_keys($arr)).') values (' .implode(',', $values).')';
        $this->query($sql);

        return $this->insertid = mysqli_insert_id($this->link);
    }

    /**
     * 执行sql语句
     * @access public
     * @param  sql str
     * @return array
     */
    public function query($sql)
    {
        $this->lastSql = $sql;
        if ($res = mysqli_query($this->link, $sql)) {
            if (is_bool($res)) {
                return true;
            } elseif (is_object($res)) {
                return $this->fetch($res);
            }
        } else {
            return false;
        }
    }


    /**
     * 获得最新插入的id
     * @access public
     * @return int
     */
    public function insertId()
    {
        return $this->insertid;
    }

    /**
     * 获得最后的sql语句
     * @access public
     * @return int
     */
    public function lastSql()
    {
        return $this->lastSql;
    }

    /**
     * 根据资源得出数组
     * @access public
     * @param resource
     * @return int
     */
    public function fetch($res)
    {
        $arr = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $arr[]=$row;
        }
        return $arr;
    }

    /**
     * 拼接表
     * @access public
     * @return str
     */
    public function jointable($table)
    {
        return  $this->prefix.$table;
    }


    /**
     * 分析参数类型
     * @access public
     * @param value mix
     * @return mix
     */
    public function parseValue($value)
    {
        if (is_string($value)) {
            return '\''.$value.'\'';
        }
        return $value;
    }
}
