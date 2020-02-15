<?php
require '../../headers.php';
require '../../public_function.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//Create SQL connection
$link = initMySqlConnector();
$from = isset($_GET["from"])?$_GET["from"]:null;
$to = isset($_GET["to"])?$_GET["to"]:null;
date_default_timezone_set('prc');

if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    $where = "";
    //Query statement
    if($from&&$to){// if there is from and to, means get chat records of two people
        $where = " where `from`=$from and `to`=$to or `from`=$to and `to`=$from";
    }else if($to){// if there is only to, means get all message of this user;
        $where = " where `to`=$to or `from`=$to";
    }
    $sql = "SELECT * from message $where ORDER BY `messageTime`";
    if($res = fetchAll($link,$sql)){
        $result = array();
        // group message by from(sender)
        foreach ($res as $message){
            if(!isset($result[$message['from']])&&$message['from']!==$to){
                $result[$message['from']] = array();
            }
            if($message['from']===$to){
                // If it's a message sent by yourself
                // Add this message to recipient
                array_push($result[$message['to']],$message);
            }else{
                array_push($result[$message['from']],$message);
            }

        }
        $result = array("code"=>'200',"message"=>"success","data"=>$result);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"failed","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){// leave a message
    $from = $json_data["from"];
    $to = $json_data["to"];
    $message = $json_data["message"];
    $messageTime = date('y-m-d h:i:s',time());
    $sql = "INSERT INTO message 
            (`from`,`to`,`messageTime`,`message`) 
            VALUES
            ('$from','$to','$messageTime','$message')";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"message successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}
