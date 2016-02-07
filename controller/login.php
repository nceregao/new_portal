<?php


$error = '';
$name = $password = NULL;

$name = isset($_POST['name']) ? $_POST['name'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$name = isset($_POST['name']) ? $_POST['name'] : NULL;
	$password = isset($_POST['password']) ? $_POST['password'] : NULL;
    $name = $model['escape']($name);
    $password = $model['escape']($password);

	if ($password && $name) {
		$user = $model['user']['get']($name);

		if(!$user){
			$error = 'User does not exists';
		} else {
			if (password_verify($password, $user['password'])){
				login_user($user);
			} else {
				$error = 'Password is incorrect';
			}
		}
    } else {
        $error = 'Invalid name or password';
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