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
    $where = "where shopping_cart.userID=$userID";
    $sql = "SELECT shopping_cart.shoppingCartID,book.* from `shopping_cart` 
            inner join `book`
            on `shopping_cart`.bookID=`book`.bookID $where";
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
    $sql = "SELECT userID FROM book where bookID = '$bookID'";
    if(fetchRow($link,$sql)["userID"]===$userID){
        $result = array("code"=>'400',"message"=>"can't add your own book to the cart!","data"=>null);
        exit(json_encode($result));
    }
    $sql = "SELECT shoppingCartID FROM shopping_cart where bookID = '$bookID' and userID='$userID'";
    if(fetchRow($link,$sql)["shoppingCartID"]){
        $result = array("code"=>'400',"message"=>"this book is already in your cart!","data"=>null);
        exit(json_encode($result));
    }
    $sql = "INSERT INTO shopping_cart 
            (`userID`,`bookID`) 
            VALUES
            ('$userID','$bookID')";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"add selling book successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='DELETE'){// delete book from shopping cart
    $shoppingCartIdList = implode(",",$json_data["shoppingCartIdList"]);
    $sql = "DELETE FROM `shopping_cart` WHERE shoppingCartID in ($shoppingCartIdList)";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"delete successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}
