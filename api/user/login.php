<?php
require '../../headers.php';
//获取表单提交数据
//解析POST中的JSON数据
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
$userName = $json_data["username"];
$password = $json_data["password"];
//创建SQL连接
header('content-type:application/json;charset=utf-8');
require './public_function.php';
$link = initMySqlConnector();
//SQL防注入
$userName = mysqli_real_escape_string($link, $userName);
$password = mysqli_real_escape_string($link, $password);
//判断用户是否存在
$sql = "SELECT username,password,salt from `user` where `username`='$userName'";
$result = fetchRow($link, $sql);
$resultPassword = $result['password'];
$resultSalt = $result['salt'];
if ($password = md5(md5($password . $resultSalt)) !== $resultPassword) {
    $result = array("code"=>400,"message"=>"用户名或密码错误","data"=>null);
    exit(json_encode($result));
} else {
    $result = array("code"=>200,"message"=>"登录成功","data"=>null);
    exit(json_encode($result));
}
