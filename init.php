<?php



session_start();
require_once 'config.php';
require 'model/base.php';


$data = [];
$data['user'] = isset($_SESSION['user']) ? $_SESSION['user'] : [];
isset($_SESSION['error']) ? : $_SESSION['error'] = [];
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';


function render_content($tpl, $data=[]){
    $template = PATH_TEMPLATE . $tpl . '.html';
	require PATH_TEMPLATE.'loyaut/web.html';
};

function add_error($err){
    $_SESSION['error'][] = $err;
}

function is_logged_in(){
    return array_key_exists('user', $_SESSION);
}

function go_home(){
    header('Location: / ');
    exit();
}

function login_user($user){
    unset($user['pass']);
    $_SESSION['user'] = $user;
    return;
}

function exit_if($state=false) {
	$is_loggen_in = isset($_SESSION['user']);
	if ($state){
		if (!$is_loggen_in) return;
	} else {
		if ($is_loggen_in) return;
	}

	go_home();
	exit;
}