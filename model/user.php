<?php

$user = [];

$user['get'] = function($name) use ($conn){
	$result = mysqli_query($conn,
				 "SELECT * from users WHERE name = '{$name}'");
	return mysqli_fetch_assoc($result);
};

$user['add'] = function($name, $password) use($conn){
	return mysqli_query($conn,
		"INSERT INTO users(name, password) VALUES('{$name}','{$password}')");
};

$user['is_exists'] = function($name) use ($conn){
	$result = mysqli_query($conn,
		"SELECT COUNT(*) FROM users WHERE name = '{$name}'");
	$arr = mysqli_fetch_row($result);
	$count = $arr[0];
	if($count) return True;
	else return False;
};



$model['user'] = $user;