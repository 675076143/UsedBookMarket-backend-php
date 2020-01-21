<?php
require '../../headers.php';
require '../../public_function.php';
//获取表单提交数据
//解析POST中的JSON数据
$json_raw = file_get_contents("php://input");
$json_data = json_decode($json_raw,true);
//创建SQL连接
$link = initMySqlConnector();
$categoryID = isset($_GET["categoryID"])?$_GET["categoryID"]:null;
$bookID = isset($_GET["bookID"])?$_GET["bookID"]:null;
if(strtoupper($_SERVER['REQUEST_METHOD'])=='GET'){
    $where = "";
    //查询语句
    if($categoryID){//如果有分类ID代表获取该分类下的所有书本
        $where = " where categoryID=$categoryID";
    }elseif($bookID){//如果有bookID,则代表获取单个书本详情信息
        $where =" where bookID=$bookID";
    }else{//否则获取所有数据
        $where = "";
    }
    $sql = "SELECT * from book $where";
    if($res = fetchAll($link,$sql)){
        //如果只有一条数据，就不返回数组了
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
