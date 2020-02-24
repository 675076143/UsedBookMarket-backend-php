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
$bookID = isset($_GET["bookID"])?$_GET["bookID"]:null;
if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    if($bookID){
        //Judge whether the stared book exists
        $sql = "SELECT COUNT(*) as count FROM `star` WHERE userID='$userID' AND bookID='$bookID'";
        $result = fetchRow($link,$sql)['count'];
        if($result==0){
            $result = array("code"=>'200',"message"=>"unStared","data"=>false);
            exit(json_encode($result));
        }else{
            $result = array("code"=>'200',"message"=>"stared","data"=>true);
            exit(json_encode($result));
        }
    }
    $sql = "select starID,book.* from star 
            join book on star.bookID=book.bookID
            where star.userID='$userID'";
    if($res = fetchAll($link,$sql)){
        $result = array("code"=>'200',"message"=>"success","data"=>$res);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"failed","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){// add book to shopping cart
    $userID = $json_data["userID"];
    $bookID = $json_data["bookID"];
    //Judge whether the stared book exists
    $sql = "SELECT COUNT(*) as count FROM `star` WHERE userID='$userID' AND bookID='$bookID'";
    $result = fetchRow($link,$sql)['count'];
    if($result==0){
        $sql = "INSERT INTO `star` 
                (userID,bookID) 
                VALUES ('$userID','$bookID')";
        if($res = query($link,$sql)){
            $result = array("code"=>'200',"message"=>"star successful","data"=>null);
            exit(json_encode($result));
        }else{
            $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
            exit(json_encode($result));
        }

    }else {
        $result = array("code"=>'400',"message"=>"This book has already been stared","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='DELETE'){// delete book from shopping cart
    $userID = $json_data["userID"];
    $bookID = $json_data["bookID"];
    $sql = "DELETE FROM `star` WHERE userID=$userID and bookID=$bookID";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"delete successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}
