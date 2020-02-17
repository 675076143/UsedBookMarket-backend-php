<?php
require '../../headers.php';
require '../../public_function.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//Create SQL connection
$link = initMySqlConnector();
$userID = isset($_GET["userID"])?$_GET["userID"]:null;

if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    $sql = "SELECT buyerRating,buyerRatingDesc FROM book join ordersub
            on book.orderSubID=ordersub.orderSubID
            where userID=$userID and buyerRating!=''";
    if($res = fetchAll($link,$sql)){
        $result = array("code"=>'200',"message"=>"success","data"=>$res);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"failed","data"=>null);
        exit(json_encode($result));
    }
}
