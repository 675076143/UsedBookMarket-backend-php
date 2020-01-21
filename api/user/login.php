<?php
require '../../headers.php';
require '../../public_function.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
$userName = $json_data["userName"];
$password = $json_data["password"];
//Create SQL connection
$link = initMySqlConnector();
//SQL anti injection
$userName = mysqli_real_escape_string($link, $userName);
$password = mysqli_real_escape_string($link, $password);
//Judge whether the user exists
$sql = "SELECT * from `user` where `username`='$userName'";
$result = fetchRow($link, $sql);
$resultPassword = $result['password'];
$resultSalt = $result['salt'];
if(strtoupper($_SERVER['REQUEST_METHOD'])=='OPTIONS'){
    exit();
}
else if(strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){
    if ($password = md5(md5($password . $resultSalt)) !== $resultPassword) {
        $result = array("code"=>'400',"message"=>"Wrong username or password","data"=>null);
        exit(json_encode($result));
    } else {
        $result = array("code"=>'200',"message"=>"Login successfully","data"=>$result);
        exit(json_encode($result));
    }
}

