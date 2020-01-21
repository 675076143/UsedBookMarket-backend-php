<?php
require "../../headers.php";
//Set time zone
date_default_timezone_set('PRC');
//Get file name
$filename = $_FILES['file']['name'];
//Get file temporary path
$temp_name = $_FILES['file']['tmp_name'];
//Get size
$size = $_FILES['file']['size'];
//Get the file upload code. 0 means the file is uploaded successfully
$error = $_FILES['file']['error'];
//Determine whether the file size exceeds the set maximum upload limit
if ($size > 2*1024*1024){
    $result = array("code"=>'400',"message"=>"File size exceeds 2m","data"=>null);
    exit(json_encode($result));
}
//The phpinfo function returns information about the file path as an array
////[dirname]: directory path [basename]: file name [Extension]: file suffix [filename]: file name without suffix
$arr = pathinfo($filename);
//Get the suffix of the file
$ext_suffix = $arr['extension'];
//Set the suffix to allow uploading files
$allow_suffix = array('jpg','gif','jpeg','png');
//Judge whether the uploaded file is within the allowed range (suffix) ==> white list
if(!in_array($ext_suffix, $allow_suffix)){
    $result = array("code"=>'400',"message"=>"The uploaded file type can only bejpg,gif,jpeg,png","data"=>$result);
    exit(json_encode($result));
}
//Check whether the path to store the uploaded file exists. If not, create a new directory
if (!file_exists('uploads')){
    mkdir('uploads');
}
//Create a new name for the uploaded file to ensure more security
$new_filename = date('YmdHis',time()).rand(100,1000).'.'.$ext_suffix;
//Move files from temporary path to disk
if (move_uploaded_file($temp_name, '../../upload/'.$new_filename)){
    $result = array("code"=>'200',"message"=>"File uploaded successfully","data"=>array("url"=>"/upload/".$new_filename,"fileName"=>$new_filename));
    exit(json_encode($result));
}else{
    $result = array("code"=>'400',"message"=>"File upload failed, error code:$error","data"=>null);
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

