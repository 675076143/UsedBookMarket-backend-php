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

$addressID = isset($json_data["addressID"])?mysqli_real_escape_string($link,$json_data["addressID"]):null;
$name = isset($json_data["name"])?mysqli_real_escape_string($link,$json_data["name"]):null;
$tel = isset($json_data["tel"])?mysqli_real_escape_string($link,$json_data["tel"]):null;
$area = isset($json_data["area"])?mysqli_real_escape_string($link,$json_data["area"]):null;
$address = isset($json_data["address"])?mysqli_real_escape_string($link,$json_data["address"]):null;
$isDefault = $json_data["isDefault"]?1:0;

if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){// get user's addresses
    $isDefault = isset($_GET["isDefault"])?$_GET["isDefault"]:null;
    $and="";
    if($isDefault){
        $and="and isDefault=1";
    }
    $sql = "select * from address where userID='$userID' $and";
    $res = fetchAll($link,$sql);
    $result = array("code"=>'200',"message"=>"success","data"=>$res);
    exit(json_encode($result));
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){// add address
    $userID = $json_data["userID"];
    if($isDefault===1){
        $sql = "UPDATE address SET `isDefault`=0 WHERE userID=$userID";
        query($link,$sql);
    }
    $sql = "INSERT INTO address 
            (`name`,`tel`,`area`,`address`,`isDefault`,`userID`) 
            VALUES
            ('$name','$tel','$area','$address','$isDefault',$userID)";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"add address successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='PUT'){// modify address
    $userID = $json_data["userID"];
    if($isDefault===1){
        $sql = "UPDATE address SET `isDefault`=0 WHERE userID=$userID";
        query($link,$sql);
    }
    $sql = "UPDATE address
            SET 
            `name` = '$name',
            `tel` = '$tel',
            `area` = '$area',
            `address` = '$address',
            `isDefault` = '$isDefault'
            WHERE `addressID` = '$addressID'";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"modify successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='DELETE'){// delete address
    $sql = "DELETE FROM address WHERE addressID='$addressID'";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"delete successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}
