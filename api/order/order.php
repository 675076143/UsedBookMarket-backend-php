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
$orderState = isset($_GET["orderState"])?$_GET["orderState"]:null;
if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    $result = array();
    $where = $orderState===null?" where `userID`=$userID":" where `userID`=$userID and `orderState`='$orderState'";
    $sql = "SELECT * from `order` $where";

    if($res = fetchAll($link,$sql)){
        foreach($res as $item){
            $orderID = $item["orderID"];
            $sql = "SELECT * FROM `ordersub` WHERE `orderID`=$orderID";
            $item["orderSub"] = fetchAll($link,$sql);
            array_push($result,$item);
        }
        $result = array("code"=>'200',"message"=>"success","data"=>$result);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'200',"message"=>"success","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){// create order
    $userID = $json_data["userID"]?$json_data["userID"]:null;
    $bookList = $json_data["bookList"]?$json_data["bookList"]:[];
    $dateTime = new DateTime();
    $orderTime = $dateTime->format('Y-m-d H:i:s');
    $totalPrice = 0;
    $sql = "INSERT INTO `order` 
            (`userID`,`orderTime`,`orderState`,`totalPrice`) 
            VALUES
            ('$userID','$orderTime',0,'$totalPrice')";
    if($res = query($link,$sql)){
        $flag = true;
        $orderID = mysqli_insert_id($link);
        foreach ($bookList as $bookID){
            $sql = "SELECT * FROM book where bookID=$bookID";
            $res = fetchRow($link,$sql);
            $bookName = $res['bookName'];
            $price = $res['price'];
            $bookDesc = $res['bookDesc'];
            $image = $res['image'];
            $totalPrice+=$price;
            $sql = "INSERT INTO `ordersub`
                    (`orderID`,`bookName`,`bookDesc`,`image`,`price`)
                    VALUE
                    ('$orderID','$bookName','$bookDesc','$image','$price')";
            if (!query($link,$sql)) $flag=false;
        }
        if($flag){
            $sql = "UPDATE `order` SET `totalPrice` = '$totalPrice'";
            if($res = query($link,$sql)){
                $result = array("code"=>'200',"message"=>"add selling book successful","data"=>array("orderID"=>$orderID));
                exit(json_encode($result));
            }else{
                $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
                exit(json_encode($result));
            }
        }else{
            $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
            exit(json_encode($result));
        }

    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='PUT'){// order payment
    $orderID = $json_data["orderID"];
    $userID = $json_data["userID"];
    $sql = "UPDATE `order`
            SET 
            `orderState` = '1'
            WHERE `orderID` = '$orderID'";
    if($res = query($link,$sql)){
        $sql = "SELECT `totalPrice` FROM `order` WHERE `orderID`=$orderID";
        $totalPrice = fetchRow($link,$sql)["totalPrice"];
        $sql = "UPDATE `user` SET `balance`=`balance`-$totalPrice WHERE `userID`=$userID";
        if($res = query($link,$sql)){
            $result = array("code"=>'200',"message"=>"Successful payment","data"=>null);
            exit(json_encode($result));
        }else{
            $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
            exit(json_encode($result));
        }
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}elseif (strtoupper($_SERVER['REQUEST_METHOD'])=='DELETE'){// Cancel order
    $orderID = $json_data["orderID"];
    $sql = "DELETE FROM `order` WHERE `orderID`='$orderID'";
    if($res = query($link,$sql)){
        $result = array("code"=>'200',"message"=>"Cancel successful","data"=>null);
        exit(json_encode($result));
    }else{
        $result = array("code"=>'400',"message"=>"Unknown error, please contact system administrator","data"=>null);
        exit(json_encode($result));
    }
}
