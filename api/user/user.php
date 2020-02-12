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
    $where = "";
    //Query statement
    if($userID){//If there is a classification ID, all books under the classification will be obtained
        $where = " where userID=$userID";
    }
    $sql = "SELECT * from user $where";
    if($res = fetchAll($link,$sql)){
        //If there is only one data, the array will not be returned, and the object will be returned directly
        if(count($res) === 1 && $userID){
            $res = $res[0];
        }
        $result = array("code"=>'200',"message"=>"success","data"=>$res);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"failed","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){// add book

}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='PUT'){// modify user
    $userID = $json_data["userID"];
    $email = $json_data["email"];
    $phone = $json_data["phone"];
    $balance = $json_data["balance"];
    $avatar = $json_data["avatar"];
    $sql = "UPDATE `user`
            SET 
            `email` = '$email',
            `phone` = '$phone',
            `balance` = '$balance',
            `avatar` = '$avatar'
            WHERE `userID` = '$userID'";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"Modify profile successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }

}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='DELETE'){// delete book

}
