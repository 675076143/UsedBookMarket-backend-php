<?php
require '../../headers.php';
require '../../public_function.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//Create SQL connection
$link = initMySqlConnector();
$categoryID = isset($_GET["categoryID"])?$_GET["categoryID"]:null;
$bookID = isset($_GET["bookID"])?$_GET["bookID"]:null;
if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    $where = "";
    //Query statement
    if($categoryID){//If there is a classification ID, all books under the classification will be obtained
        $where = " where categoryID=$categoryID";
    }elseif($bookID){//If there is a bookid, it means to get individual book details
        $where =" where bookID=$bookID";
    }else{//Otherwise, get all data
        $where = "";
    }
    $sql = "SELECT * from book $where";
    if($res = fetchAll($link,$sql)){
        //If there is only one data, the array will not be returned, and the object will be returned directly
        if(count($res) === 1){
            $res = $res[0];
        }
        $result = array("code"=>'200',"message"=>"success","data"=>$res);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"failed","data"=>null);
        exit(json_encode($result));
    }
}
