<?php

$docID = NULL;

if ( $is_ajax && isset($_GET['id']) ) {
	$docID = (int)$_GET['id'];
} else {
	preg_match('#^/document/remove/(\d+)#', $uri, $match);
    $docID = $match[1];
}


if ( is_numeric($docID) ) {
	$model['documents']['removeDocument']($docID);

	if ( $is_ajax ) {
		die('OK');
	}
} elseif ( $is_ajax ) {
	die('ERROR');
}

header('Location: /documents');
