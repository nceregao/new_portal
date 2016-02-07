<?php

require_once 'config.php';
require 'init.php';

$title = '';
$selfPath = '';
$uri = $_SERVER['REQUEST_URI'];


function D($var, $exit = 0)
{
    print '<div style="background-color: #ffffff; padding: 3px; z-index: 1000;"><pre style="text-align: left; font: normal 10px Courier; color: #000000;">';
    if ( is_array($var) || is_object($var) ) print_r($var);
    else var_dump($var);
    print '</pre></div>';
    if ( $exit ) exit;
}


switch (True) {
	case preg_match('#^/$#', $uri):
		$tpl = 'containers/container';
		$title = 'News_portal';
		$selfPath .= 'containers/container';
		break;
	case preg_match('#^/account/login#', $uri):
		exit_if(true);
		require 'controller/login.php';
		$tpl = 'account/login';
		$title = 'login';
		$selfPath .= 'account/login';
		break;
	case preg_match('#^/account/register#', $uri):
		exit_if(true);
		require 'controller/register.php';
		$tpl = 'account/register';
		$title = 'register';
		$selfPath .= 'account/register';
		break;
	case preg_match('#^/account/logout#', $uri):
		require 'controller/logout.php';
		break;

	case preg_match('#^/documents#', $uri):
		$selfPath .= 'documents';
		$title = 'List news';
		require 'controller/documents.php';
		$tpl = 'documents';
		break;

	case preg_match('#^/document/\d+#', $uri):
		$selfPath .= '/document/';
		$title = '';
		require 'controller/document.php';
		$tpl = 'document';
		break;
	case preg_match('#^/document/add#', $uri):
		$title = 'New document';
		$selfPath .= '/document/add';
		require 'controller/add.php';
		$tpl = 'add';
		break;
	case preg_match('#^/document/edit#', $uri):
		require 'controller/add.php';
		$tpl = 'add';
		$title = 'edit';
		$selfPath .= '/document/edit';
		break;

	case preg_match('#^/document/remove#', $uri):
		require 'controller/remove.php';
		break;

	default:
		$tpl = '404';
		$title = '404';
		$selfPath .= '404';
		break;
}

$data['title'] = $title;
$data['selfPath'] = $selfPath;

render_content($tpl, $data);
