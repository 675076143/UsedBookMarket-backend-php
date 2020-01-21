<?php
require '../../headers.php';
require '../../public_function.php';
require './check_form.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
$userName = $json_data["userName"];
$password = $json_data["password"];
//Create SQL connection
$link = initMySqlConnector();
//SQL anti injection
$userName = mysqli_real_escape_string($link,$userName);
$password = mysqli_real_escape_string($link,$password);
//Verify input format
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
if(strtoupper($_SERVER['REQUEST_METHOD'])=='OPTIONS'){
    exit();
}

//Judge whether the user exists
$sql = "SELECT COUNT(*) as count FROM `user` WHERE username='$userName'";
$result = fetchRow($link,$sql)['count'];
if(empty($error)){
    if($result==0){
        $salt=md5(uniqid(microtime()));
        //md5*2
        $password=md5(md5($password.$salt));
        $sql = "INSERT INTO `user` (userName,password,salt,userState) VALUES ('$userName','$password','$salt',1)";
        if($res = query($link,$sql)){
            $result = array("code"=>'200',"message"=>"register successful","data"=>null);
            exit(json_encode($result));
        }else{
            $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
            exit(json_encode($result));
        }

    }else {
        $result = array("code"=>'400',"message"=>"User already exists","data"=>null);
        exit(json_encode($result));
    }
}else{
    $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
    exit(json_encode($result));
}