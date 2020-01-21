<?php
function checkUserName($username){
    $reUserName = '/^[\da-zA-z_]{1,16}$/';
    if(!preg_match($reUserName,$username)){
        return '用户名：1-16位字母数字下划线';
    }return true;
}
function checkPassword($password){
    $rePassword = '/^.{1,16}$/';
    if(!preg_match($rePassword,$password)){
        return '密码：1-16';
    }return true;
}
function checkEmail($email){
    $reEmail = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';
    if(!preg_match($reEmail,$email)){
        return '邮箱格式不规范';
    }return true;
}