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
$bookName = isset($_GET["bookName"])?$_GET["bookName"]:null;
$order = isset($_GET["order"])?$_GET["order"]:null;
$sort = isset($_GET["sort"])?$_GET["sort"]:null;
if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    $where = "";
    $orderBy = "";
    //Query statement
    if($categoryID){//If there is a classification ID, all books under the classification will be obtained
        $str = implode(",",$categoryID);
        $categoryID = "and categoryID in ($str)";
    }
    if($order){
        $orderBy = "order by $order $sort";
    }
    $sql = "SELECT * FROM book WHERE UPPER(bookName) LIKE UPPER('%$bookName%') $categoryID $orderBy";
    if($res = fetchAll($link,$sql)){
        $result = array("code"=>'200',"message"=>"success","data"=>$res);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"failed","data"=>null);
        exit(json_encode($result));
    }
}
