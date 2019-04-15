<?php
namespace Core;
class Framework{
    public static function run(){
        self::initConst();
        self::initConfig();
        self::initError();
        self::initRoutes();
        self::initAutoLoad();
        self::initDispatch();
    }
    //定义路径常量
    private static function initConst(){
        define('DS', DIRECTORY_SEPARATOR);  //定义目录分隔符
        define('ROOT_PATH', getcwd().DS);      //入口文件所在的目录
        define('APP_PATH', ROOT_PATH.'Application'.DS);
        define('CONFIG_PATH', APP_PATH.'Config'.DS); 
        define('CONTROLLER_PATH', APP_PATH.'Controller'.DS);
        define('MODEL_PATH', APP_PATH.'Model'.DS);
        define('VIEW_PATH', APP_PATH.'View'.DS);
        define('FREAMEWORK_PATH', ROOT_PATH.'Framework'.DS);
        define('CORE_PATH', FREAMEWORK_PATH.'Core'.DS);
        define('LIB_PATH', FREAMEWORK_PATH.'Lib'.DS);
    }    
    //引入配置文件
    private static function initConfig(){
        $GLOBALS['config']=require CONFIG_PATH.'Config.php';
    }
    //错误处理
    private static function initError(){
        ini_set('error_reporting',E_ALL);   //ini_set用来更改php.ini的配置
        if($GLOBALS['config']['app']['debug']){ //开发模式
            ini_set('display_errors','on'); //错误显示在浏览器上
            ini_set('log_errors','off');    //错误不用记录在日志中
        }else{      //运行模式
            ini_set('display_errors','off');
            ini_set('log_errors','on');
            ini_set('error_log',ROOT_PATH.'Log'.DS.date('Y-m-d').'.log');
        }
    }
    //确定路由
    private static function initRoutes(){
        $p=$_GET['p']??$GLOBALS['config']['app']['df_p'];         //平台
        $c=$_GET['c']??$GLOBALS['config']['app']['df_c'];	//控制器
        $a=$_GET['a']??$GLOBALS['config']['app']['df_a'];	//方法
        $p=ucfirst(strtolower($p));     //平台的首字母大写
        $c=ucfirst(strtolower($c));	//类的首字母大写
        $a=strtolower($a);              //全部转出小写
        define('PLATFORM_NAME', $p);    //平台名称常量
        define('CONTROLLER_NAME', $c);  //控制器名称常量
        define('ACTION_NAME', $a);      //方法名称常量
        define('__URL__', CONTROLLER_PATH.$p.DS);    //当前请求的控制器所在的目录
        define('__VIEW__', VIEW_PATH.$p.DS);        //当前视图所在的目录
        define('__VIEWC__', APP_PATH.'Viewc'.DS.$p.DS);   //设置混编目录路径
    }
    //自动加载类
    private static function initAutoLoad(){
        spl_autoload_register(function($class_name){
            //smarty类和地址映射成一个关联数组
            $map=array(
                'Smarty'    =>  CORE_PATH.'smarty'.DS.'Smarty.class.php'
            );          
            $namespace= dirname($class_name);  //获取的命名空间
            $class_name= basename($class_name); //获取类名
            if(isset($map[$class_name])){
                $path=$map[$class_name];
            }elseif(in_array($namespace, array('Core','Lib')))   //当前命名空间是Core和Lib
                $path=FREAMEWORK_PATH.$namespace.DS.$class_name.'.class.php';
            elseif($namespace=='Model') //当前命名空间是Model
                $path=MODEL_PATH.$class_name.'.class.php';
            else    //当前是控制器
                $path=__URL__.$class_name.'.class.php';
            if(isset($path) && file_exists($path))
                require $path;
        }); 
    }
    //请求分发
    private static function initDispatch(){
        $controller_name='Controller\\'.PLATFORM_NAME.'\\'.CONTROLLER_NAME.'Controller';	//拼接类名
        $action_name=ACTION_NAME.'Action';		//拼接方法名
//        var_dump($controller_name,$action_name);die;
        $obj=new $controller_name();
        $obj->$action_name();
    }
}





