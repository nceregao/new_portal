<?php

$error = '';
$name = $password = NULL;

function is_password_correct($pass){
	if($pass && preg_match('#.{2}#', $pass)) return True;
	else return False;
}
function is_name_correct($name){
	if($name && preg_match('#.{2}#', $name)) return True;
	else return False;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	$name = isset($_POST['name']) ? $model['escape']($_POST['name']) : NULL;
	$password = isset($_POST['password']) ? $model['escape']($_POST['password']) : NULL;

	if ( is_name_correct($name) && !$model['user']['is_exists']($name) ) {
		if ( is_password_correct($password) ) {
			$password = password_hash($password, PASSWORD_DEFAULT);

			if ( $model['user']['add']($name, $password) ) {
				$user = $model['user']['get']($name);
				login_user($user);
			} else {
				$error = $model['get_last_error']();
			}
		} else {
			$error = 'Invalid password.';
		}
	} else {
		$error = 'User is already registered.';
	}

	if( $is_ajax ) {
	    echo !empty($error) ? $error : 'OK';
	    exit();
	} elseif ( empty($error) ) {
	    go_home();
	} else {
		add_error($error);
	}
}


$data['name'] = $name;
$data['password'] = $password;