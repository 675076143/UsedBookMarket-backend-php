<?php
function checkUserName($username){
    $reUserName = '/^[\da-zA-z_]{1,16}$/';
    if(!preg_match($reUserName,$username)){
        return 'User name: 1-16 alphanumeric underline';
    }return true;
}
function checkPassword($password){
    $rePassword = '/^.{1,16}$/';
    if(!preg_match($rePassword,$password)){
        return 'Password: 1-16';
    }return true;
}
function checkEmail($email){
    $reEmail = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';
    if(!preg_match($reEmail,$email)){
        return 'Mailbox format is not standard';
    }return true;
}