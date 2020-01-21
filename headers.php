<?php
// 制定允许其他域名访问
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Credentials", "true");
header('Access-Control-Allow-Methods:GET,POST,OPTIONS,PUT');
header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept, Authorization');