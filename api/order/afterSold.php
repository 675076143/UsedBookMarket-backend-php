<?php
require '../../headers.php';
require '../../public_function.php';
//Get form submission data
//Parsing JSON data in post
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//Create SQL connection
$link = initMySqlConnector();
if (strtoupper($_SERVER['REQUEST_METHOD'])=='PUT'){// subOrder shipped/received
    $orderSubID = $json_data["orderSubID"];
    $orderSubState = $json_data["orderSubState"];
    $rating ="";
    $sellerRating =isset($json_data["sellerRating"])?$json_data["sellerRating"]:null;
    $sellerRatingDesc = isset($json_data["sellerRatingDesc"])?$json_data["sellerRatingDesc"]:null;
    $buyerRating = isset($json_data["buyerRating"])?$json_data["buyerRating"]:null;
    $buyerRatingDesc = isset($json_data["buyerRatingDesc"])?$json_data["buyerRatingDesc"]:null;
    if($orderSubState ===3&&!$sellerRating&&!$buyerRating){
        $sql= "select ordersub.price,book.userID from ordersub
                join book on ordersub.orderSubID=book.orderSubID
                where ordersub.orderSubID=$orderSubID";
        $res = fetchRow($link,$sql);
        $price = $res["price"];
        $sellerID = $res["userID"];
        $sql = "update user set balance=balance+$price where userID=$sellerID";
        query($link,$sql);
    }
    if($sellerRating&&$sellerRatingDesc){
        $rating = ",`sellerRating`=$sellerRating,`sellerRatingDesc`='$sellerRatingDesc'";
    }
    if($buyerRating&&$buyerRatingDesc){
        $rating = ",`buyerRating`=$buyerRating,`buyerRatingDesc`='$buyerRatingDesc'";
    }
    $sql = "UPDATE `ordersub`
            SET 
            `orderSubState` = $orderSubState
            $rating
            WHERE `orderSubID` = '$orderSubID'";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"Successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}
