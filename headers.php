<?php
//Allow other domain names to access
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Credentials", "true");
header('Access-Control-Allow-Methods:GET,POST,OPTIONS,PUT,DELETE');
header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('content-type:application/json;charset=utf-8');