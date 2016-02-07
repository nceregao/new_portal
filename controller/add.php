<?php


$error = '';

$document = [
    'documentID' => '',
    'userID' => '',
    'documentName' => '',
    'documentAnnotation' => '',
    'documentContent' => '',
    'documentThumbs' => '',
    'documentTitle' => '',

    'documentCategory' => '',
    'newCategory' => '',
];
$tags_init = [];

preg_match('#^/document/edit/(\d+)#', $uri, $match);

if ( !empty($match) && is_numeric($match[1]) ) {
    $document['documentID'] = $match[1];
    $document_init = $model['documents']['getDocument']($document['documentID']);
    $tags_init = $model['documents']['getDocumentTags']($document['documentID']);
}

foreach ($document as $k => $v) {
    if ( isset($_POST[$k]) ) {
        $document[$k] = $_POST[$k];
    } elseif ( isset($document_init[$k]) ) {
        $document[$k] = $document_init[$k];
    }
}

$categories = $model['documents']['getCategories']();


if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']) ) {
    if ( in_array($_POST['submit'], ['saveExit', 'save']) ) {
        if ( empty($document['documentName']) ) {
            $error = 'Warning! Pleas fill all field';
        } else {
            $document['documentID'] = $model['documents']['saveDocument']($document['documentID'],
                is_logged_in() ? $data['user']['id'] : 0,
                $document['documentName'],
                $document['documentAnnotation'],
                $document['documentContent'],
                $document['documentThumbs'],
                $document['documentTitle']
            );
            if ( $document['documentCategory'] == 'other' && !empty($document['newCategory']) && !in_array($document['newCategory'], $categories) ) {
            	$document['documentCategory'] = $document['newCategory'];
            }
            $categoryID = array_search($document['documentCategory'], $categories);
            $model['documents']['setCategory']($document['documentID'], $document['documentCategory'], $categoryID);

            $tagsID = [];
            $tags = [];
            if ( !empty($_POST['documentTags']) ) {
                $tags = explode(',', $_POST['documentTags']);
                $tags = array_filter($tags, function(&$t) {
                    return !empty($t = trim($t));
                });
                if ( !empty($tags) ) {
                    $all_tags = $model['documents']['getTags']();
                    $existing_tags = array_diff($tags, $all_tags);
                    if ( !empty($existing_tags) ) {
                        $model['documents']['setTags']($existing_tags);
                    }
                }
                $tagsID = $model['documents']['getDocumentTagsID']($tags);
            }
            $model['documents']['delExitingDocumentTags']($document['documentID'], $tagsID);
            if ( !empty($tags) ) {
                $existing_id = array_diff($tagsID, array_keys($tags_init));
                $model['documents']['setDocumentTags']($document['documentID'], $existing_id);
            }

            if ($_POST['submit'] == 'saveExit') {
            	header('Location: /documents');
            } else {
            	header('Location: /document/edit/' . $document['documentID']);
            }
            exit();
        }
    }
}

$document['documentTags'] = implode(", ", isset($tags) ? $tags : $tags_init);
$data['error'] = $error;
$data['document'] = $document;
$data['categories'] = $categories;