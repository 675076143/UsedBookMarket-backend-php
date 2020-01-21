<?php
require "../../headers.php";
//设置时区
date_default_timezone_set('PRC');
//获取文件名
$filename = $_FILES['file']['name'];
//获取文件临时路径
$temp_name = $_FILES['file']['tmp_name'];
//获取大小
$size = $_FILES['file']['size'];
//获取文件上传码，0代表文件上传成功
$error = $_FILES['file']['error'];
//判断文件大小是否超过设置的最大上传限制
if ($size > 2*1024*1024){
    $result = array("code"=>'400',"message"=>"File size exceeds 2m","data"=>null);
    exit(json_encode($result));
}
//The phpinfo function returns information about the file path as an array
////[dirname]: directory path [basename]: file name [Extension]: file suffix [filename]: file name without suffix
$arr = pathinfo($filename);
//获取文件的后缀名
$ext_suffix = $arr['extension'];
//设置允许上传文件的后缀
$allow_suffix = array('jpg','gif','jpeg','png');
//判断上传的文件是否在允许的范围内（后缀）==>白名单判断
if(!in_array($ext_suffix, $allow_suffix)){
    $result = array("code"=>'400',"message"=>"The uploaded file type can only bejpg,gif,jpeg,png","data"=>$result);
    exit(json_encode($result));
}
//检测存放上传文件的路径是否存在，如果不存在则新建目录
if (!file_exists('uploads')){
    mkdir('uploads');
}
//为上传的文件新起一个名字，保证更加安全
$new_filename = date('YmdHis',time()).rand(100,1000).'.'.$ext_suffix;
//将文件从临时路径移动到磁盘
if (move_uploaded_file($temp_name, '../../upload/'.$new_filename)){
    $result = array("code"=>'200',"message"=>"文件上传成功","data"=>array("url"=>"/upload/".$new_filename,"fileName"=>$new_filename));
    exit(json_encode($result));
}else{
    $result = array("code"=>'400',"message"=>"文件上传失败,错误码：$error","data"=>null);
    exit(json_encode($result));
}

////File upload error code and meaning:
//$_FILES[‘file’][‘error’]has the following types
//
//1、UPLOAD_ERR_OK：the value is 0, no error occurs, and the file is uploaded successfully.
//
//2、UPLOAD_ERR_INI_SIZE：the value is 1. The uploaded file exceeds the limit of the upload? Max? Filesize option in php.ini.
//
//3、UPLOAD_ERR_FORM_SIZE：the value is 2. The size of the uploaded file exceeds the value specified by the max? File? Size option in the HTML form.
//
//4、UPLOAD_ERR_PARTIAL：its value is 3, and only part of the file is uploaded.
//
//5、UPLOAD_ERR_NO_FILE：its value is 4, and no files are uploaded.
//
//6、UPLOAD_ERR_NO_TMP_DIR：its value is 6. No temporary folder was found. PHP 4.3.10 and PHP 5.0.3 were introduced.
//
//7、UPLOAD_ERR_CANT_WRITE：its value is 7, file write failed. PHP 5.1.0 was introduced.

