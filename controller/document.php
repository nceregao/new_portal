<?php


$document = [
    'documentID' => '',
    'userID' => '',
    'documentName' => '',
    'documentAnnotation' => '',
    'documentContent' => '',
    'documentThumbs' => '',
    'documentTitle' => '',

    'documentCategory' => '',
    'documentTags' => ''
];

preg_match('#^/document/(\d+)#', $uri, $match);

if ( !empty($match) && is_numeric($match[1]) ) {
    $document['documentID'] = $match[1];
    $document_init = $model['documents']['getDocument']($document['documentID']);
    $document_init['documentTags'] = implode(", ", $model['documents']['getDocumentTags']($document['documentID']));
}

foreach ($document as $k => $v) {
	if ( isset($document_init[$k]) ) {
    	$document[$k] = $document_init[$k];
    }
}


$selfPath .= $document['documentID'];
$title = $document['documentTitle'];

$data['document'] = $document;