<?php
require '../../headers.php';
require '../../public_function.php';
require './check_form.php';
//获取表单提交数据
//解析POST中的JSON数据
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
$userName = $json_data["userName"];
$password = $json_data["password"];
//创建SQL连接
header('content-type:application/json;charset=utf-8');


$link = initMySqlConnector();
//SQL防注入
$userName = mysqli_real_escape_string($link,$userName);
$password = mysqli_real_escape_string($link,$password);
//验证输入格式
$data= array(
    'username'=>$userName,
    'password'=>$password,
);
$validate=array(
    'username'=>'checkUserName',
    'password'=>'checkPassword',
);
$error=array();
foreach ($validate as $k=>$v){
    $reResult=$v($data[$k]);
    if($reResult !==true){
        $error[]=$reResult;
    }
}

//判断用户是否存在
$sql = "SELECT COUNT(*) as count FROM `user` WHERE username='$userName'";
$result = fetchRow($link,$sql)['count'];
if(empty($error)){
    if($result==0){
        //echo '可以插入';
        $salt=md5(uniqid(microtime()));
        //md5*2

        $password=md5(md5($password.$salt));
        $sql = "INSERT INTO `user` (userName,password,salt,userState) VALUES ('$userName','$password','$salt',1)";
        if($res = query($link,$sql)){
            $result = array("code"=>'200',"message"=>"注册成功","data"=>null);
            exit(json_encode($result));
        }else{
            $result = array("code"=>'400',"message"=>"未知错误，请联系系统管理员","data"=>null);
            exit(json_encode($result));
        }

    }else {
        $result = array("code"=>'400',"message"=>"用户已存在","data"=>null);
        exit(json_encode($result));
    }
}else{
    require 'register_error_html.php';
}