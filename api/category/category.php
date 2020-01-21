<?php
require '../../headers.php';
require '../../public_function.php';
//获取表单提交数据
//解析POST中的JSON数据
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//创建SQL连接
header('content-type:application/json;charset=utf-8');
$link = initMySqlConnector();

//查询语句
$sql = "SELECT * from category";
if($res = fetchAll($link,$sql)){
    $result = array("code"=>'200',"message"=>"success","data"=>$res);
    exit(json_encode($result));
}else{
    $result = array("code"=>'400',"message"=>"未知错误，请联系系统管理员","data"=>null);
    exit(json_encode($result));
}