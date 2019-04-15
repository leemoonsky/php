<?php
namespace Core;
class MyPDO{
    private $type;          //数据库类型
    private $host;          //主机
    private $port;          //端口号
    private $dbname;        //数据库名
    private $charset;       //字符集
    private $username;      //用户名
    private $pwd;           //密码
    private $pdo;           //PDO对象    
    private static $instance;       //保存单例
    private function __construct($param) {    //阻止在类的外部实例化
        $this->initParam($param);       //初始化参数
        $this->initPDO();
        $this->initException();
    }
    private function __clone() {
    }
    public static function getInstance($param=array()){
        if(!self::$instance instanceof self)
            self::$instance=new self($param);
        return self::$instance;
    }
    //初始化参数
    private function initParam($param){
        $this->type=$param['type']??'mysql';
        $this->host=$param['host']??'127.0.0.1';
        $this->port=$param['port']??'3306';
        $this->dbname=$param['dbname']??'php2';
        $this->charset=$param['charset']??'utf8';
        $this->username=$param['username']??'root';
        $this->pwd=$param['pwd']??'aa';
    }
    //实例化PDO
    private function initPDO(){
        try{
            $dsn="{$this->type}:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo=new \PDO($dsn, $this->username, $this->pwd);
        } catch (\PDOException $ex) {
            $this->showException($ex);
        }        
    }
    //初始化异常处理
    private function initException(){
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
    }
    //显示异常
    private function showException($ex,$sql=''){
        if($sql!='')
            echo 'SQL语句执行失败<br>错误的SQL语句是：'.$sql,'<br>';
        echo '错误编号：',$ex->getCode(),'<br>';
        echo '错误行号：',$ex->getLine(),'<br>';
        echo '错误文件：',$ex->getFile(),'<br>';
        echo '错误信息：',$ex->getMessage(),'<br>';
        exit;
    }
    //执行数据操作语句

    public function exec($sql){
        try{
            return $this->pdo->exec($sql);
        } catch (\Exception $ex) {
            $this->showException($ex, $sql);            
        }
    }
    //获取自动增长的编号
    public function getLastInsertId(){
        return $this->pdo->lastInsertId();
    }
    //封装获取匹配类型的方法
    private function getFetchType($type){
        switch ($type){
            case 'num':
                return \PDO::FETCH_NUM;
            case 'both':
                return \PDO::FETCH_BOTH;
            default :
                return \PDO::FETCH_ASSOC;
        }
    }
    //获取所有记录
    public function fetchAll($sql,$type='assoc'){
        try{
            $type= $this->getFetchType($type);
            $stmt= $this->pdo->query($sql);
            return $stmt->fetchAll($type);
        } catch (\PDOException $ex) {
            $this->showException($ex, $sql);
        }      
    }
    //获取一条记录
    public function fetchRow($sql,$type='assoc'){
       try{
           $type= $this->getFetchType($type);
           $stmt=$this->pdo->query($sql);
           return $stmt->fetch($type);
       } catch (\PDOException $ex) {
           $this->showException($ex,$sql);
       }
    }
    //获取一行一列
    public function fetchColumn($sql){
        try{
            $stmt= $this->pdo->query($sql);
            return $stmt->fetchColumn();
        } catch (\PDOException $ex) {
            $this->showException($ex,$sql);
        }
    }
}



