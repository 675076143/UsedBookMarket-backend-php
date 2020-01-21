<?php
/*
 * Initialize database connection
 * */
function initMySqlConnector(){
    $link = mysqli_connect('localhost','Sheng','123456');
    if(!$link){
        die('Failed to connect to database! ' .mysqli_connect_error());
    }
    mysqli_query($link,'set names utf8');
    mysqli_query($link,'use `usedbookmarket`');
    return $link;
}
/*
 * Query database records
 */
function query($link,$sql){
    if($result = mysqli_query($link,$sql)){
        return $result;
    }else{
        echo 'Failure SQL:',$sql,'<br/>';
        echo 'error code"',mysqli_errno($link),'<br/>';
        echo 'error message:',mysqli_error($link),'<br/>';
    }
}
/*
 * Traversing multiple result data sets
 */
function fetchAll($link,$sql){
    if($result = query($link,$sql)){
        //Traversal result set
        $rows = array();
        while ($row=mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        //Release result set
        mysqli_free_result($result);
        return $rows;
    }else{
        return false;
    }
}
/*
 * Traversing a single result data set
 */
function fetchRow($link,$sql){
    if($result = query($link,$sql)){
        $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
        return $row;
    }else{
        return false;
    }
}
/*
 * Filter illegal SQL characters
 */
function safeHandle($link,$data){
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($link,$data);
}
