<?php


if ( $_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['documentsLimit']) && is_numeric($_POST['documentsLimit']) ) {
    $documentsLimit = $_POST['documentsLimit'];
    setcookie('documentLimit', $documentsLimit, time() + 3600, '/');
    header('Location: /documents');
} else {
    $documentsLimit = isset($_COOKIE['documentLimit']) ? $_COOKIE['documentLimit'] : 5;
}

$uri = urldecode($uri);

$page = preg_match('#/page=(\d+)#', $uri, $matches) && (int)$matches[1] > 0 ? (int)$matches[1] : 1;
$offset = ($page - 1) * $documentsLimit;


$tag = preg_match('#/tag=(\w+)#', $uri, $matches) ? $matches[1] : '';
$category = preg_match('#/category=(\w+)#', $uri, $matches) ? $matches[1] : '';

$selfPath .= $category ? "/category=$category/" : '';
$selfPath .= $tag ? "/tag=$tag/" : '';

$data['documents'] = $model['documents']['getDocuments']($documentsLimit, $offset, $tag, $category);
$data['countDocuments'] = $model['documents']['getCountDocuments']($documentsLimit, $offset, $tag, $category);
$data['documentsLimit'] = $documentsLimit;

$data['tag'] = $tag;
$data['tags'] = $model['documents']['getTags']();
$data['category'] = $category;
$data['categories'] = $model['documents']['getCategories']();

$data['page'] = $page;
$data['max_page'] = ceil($data['countDocuments']/$documentsLimit);
