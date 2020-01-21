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
    $result = array("code"=>'400',"message"=>"文件大小超过2M大小","data"=>null);
    exit(json_encode($result));
}
//phpinfo函数会以数组的形式返回关于文件路径的信息
//[dirname]:目录路径[basename]:文件名[extension]:文件后缀名[filename]:不包含后缀的文件名
$arr = pathinfo($filename);
//获取文件的后缀名
$ext_suffix = $arr['extension'];
//设置允许上传文件的后缀
$allow_suffix = array('jpg','gif','jpeg','png');
//判断上传的文件是否在允许的范围内（后缀）==>白名单判断
if(!in_array($ext_suffix, $allow_suffix)){
    $result = array("code"=>'400',"message"=>"上传的文件类型只能是jpg,gif,jpeg,png","data"=>$result);
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

//文件上传error码以及含义：
//$_FILES[‘file’][‘error’]有以下几种类型
//
//1、UPLOAD_ERR_OK：其值为 0，没有错误发生，文件上传成功。
//
//2、UPLOAD_ERR_INI_SIZE：其值为 1，上传的文件超过了 php.ini 中 upload_max_filesize选项限制的值。
//
//3、UPLOAD_ERR_FORM_SIZE：其值为 2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
//
//4、UPLOAD_ERR_PARTIAL：其值为 3，文件只有部分被上传。
//
//5、UPLOAD_ERR_NO_FILE：其值为 4，没有文件被上传。
//
//6、UPLOAD_ERR_NO_TMP_DIR：其值为 6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。
//
//7、UPLOAD_ERR_CANT_WRITE：其值为 7，文件写入失败。PHP 5.1.0 引进。

