<?php

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$model = [];
$model['get_last_error'] = function () use ($conn){
	return mysqli_error($conn);
};

$model['escape'] = function ($str) use ($conn){
	return mysqli_real_escape_string($conn, $str);
};


require 'model/user.php';
require 'model/documents.php';


unset($conn);