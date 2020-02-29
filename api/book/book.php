<?php
require '../../headers.php';
require '../../public_function.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//Create SQL connection
$link = initMySqlConnector();
$schoolID = isset($_GET["schoolID"])?$_GET["schoolID"]:null;
$categoryID = isset($_GET["categoryID"])?$_GET["categoryID"]:null;
$bookID = isset($_GET["bookID"])?$_GET["bookID"]:null;
$userID = isset($_GET["userID"])?$_GET["userID"]:null;
$orderSubState =isset($_GET["orderSubState"])?$_GET["orderSubState"]:null;
$bookName = isset($json_data["bookName"])?mysqli_real_escape_string($link,$json_data["bookName"]):null;
$image = isset($json_data["image"])?$json_data["image"]:null;
$bookDesc = isset($json_data["bookDesc"])?mysqli_real_escape_string($link,$json_data["bookDesc"]):null;
$price = isset($json_data["price"])?$json_data["price"]:null;

if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    $where = "where schoolID=$schoolID and orderSubID is null";
    $sql = "select * from book $where";
    //Query statement
    if($categoryID){//If there is a classification ID, all books under the classification will be obtained
        $where .= " and categoryID=$categoryID";
        $sql = "select * from book $where";
    }
    if($bookID){//If there is a bookID, it means to get individual book details
        $where =" where bookID=$bookID";
        $sql = "select * from book $where";
    }
    if($userID){//If there is a userID, it means to get published of this user
        $where = "where book.userID=$userID";
        $sql = "select * from book $where";
    }
    if($userID&&$orderSubState&&$orderSubState=='sold'){//If there is a userID and orderSubState==0 it means to get sold of this user
        $where = "where book.userID=$userID and ordersub.orderSubState!=0";
        $sql = "SELECT book.*,
            order.userID as buyerID,
            order.name,
            order.tel,
            order.area,
            order.address,
            ordersub.orderSubState,
            ordersub.sellerRating,
            ordersub.sellerRatingDesc,
            ordersub.buyerRating,
            ordersub.buyerRatingDesc
            FROM `book` 
            JOIN `ordersub` ON book.orderSubID=ordersub.orderSubID
            JOIN `order` ON `order`.orderID=ordersub.orderID $where";
    }
    $res = fetchAll($link,$sql);
    if(count($res) === 1 && $bookID){
        $res = $res[0];
    }
    $result = array("code"=>'200',"message"=>"success","data"=>$res);
    exit(json_encode($result));
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){// add book
    $userID = $json_data["userID"];
    $categoryID = $json_data["categoryID"];
    $schoolID = $json_data["schoolID"];
    $sql = "INSERT INTO book 
            (`bookName`,`bookDesc`,`price`,`status`,`image`,`categoryID`,`userID`,`schoolID`) 
            VALUES
            ('$bookName','$bookDesc',$price,0,'$image','$categoryID',$userID,$schoolID)";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"add selling book successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='PUT'){// modify book
    $categoryID = $json_data["categoryID"];
    $bookID = $json_data["bookID"];
    $sql = "UPDATE book
            SET 
            `bookName` = '$bookName',
            `bookDesc` = '$bookDesc',
            `price` = '$price',
            `image` = '$image',
            `categoryID` = '$categoryID'
            WHERE `bookID` = '$bookID'";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"modify successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='DELETE'){// delete book
    $bookID = $json_data["bookID"];
    $sql = "DELETE FROM book WHERE bookID='$bookID'";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"delete successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}
