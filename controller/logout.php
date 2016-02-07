<?php



if (is_logged_in()) {
	unset($_SESSION['user']);
}
go_home();