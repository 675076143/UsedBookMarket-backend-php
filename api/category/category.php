<?php
require '../../headers.php';
require '../../public_function.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//Create SQL connection
$link = initMySqlConnector();
//Query statement
$sql = "SELECT * from category";
if($res = fetchAll($link,$sql)){
    $result = array("code"=>'200',"message"=>"success","data"=>$res);
    exit(json_encode($result));
}else{
    $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
    exit(json_encode($result));
}