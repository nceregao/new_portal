<?php


$documents = [];

$documents['saveDocument'] = function(&$documentID, $userID, $documentName, $documentAnnotation, $documentContent, $documentThumbs, $documentTitle) use ($conn) {
	if ( $documentID ) {
        $values = "userID=%d, documentName='%s', documentAnnotation='%s', documentContent='%s', documentThumbs='%s', documentTitle='%s'";
        $base_query = "UPDATE documents SET $values WHERE documentID=$documentID";
    } else {
        $cols = 'userID, documentName, documentAnnotation, documentContent, documentThumbs, documentTitle';
        $base_query = "INSERT INTO documents($cols) VALUES (%d, '%s', '%s', '%s', '%s', '%s')";
    }
    $query = sprintf($base_query, $userID, $documentName, $documentAnnotation, $documentContent, $documentThumbs, $documentTitle);
    mysqli_query($conn, $query);
    return $documentID ?: mysqli_insert_id($conn);
};

$documents['getDocument'] = function($id) use ($conn) {
	$query = "SELECT
			users.id AS userID,
	        users.name AS user,
	        documents.documentName,
	        documents.documentAnnotation,
	        documents.documentContent,
	        documents.documentThumbs,
	        documents.documentTitle,
	        categories.value AS documentCategory
	        FROM documents
	        	LEFT JOIN users
	        		ON documents.userID = users.id
	        	LEFT JOIN documents_category
                	ON documents.documentID = documents_category.id_document
                LEFT JOIN categories
	        		ON documents_category.id_category = categories.id
                WHERE documents.documentID = $id LIMIT 1";
    if ( $resultDoc = mysqli_query($conn, $query) ) {
        return mysqli_fetch_assoc($resultDoc);
    } else {
        die(mysqli_error($conn));
    }
};

$documents['removeDocument'] = function($id) use ($conn) {
    mysqli_query($conn, "DELETE FROM documents_tags WHERE id_document=$id");
    mysqli_query($conn, "DELETE FROM documents_category WHERE id_document=$id");
	return mysqli_query($conn, "DELETE FROM documents WHERE documentID=$id LIMIT 1");
};


$documents['getCountDocuments'] = function($documentsLimit = 5, $offset = 0, $tag, $category) use ($conn){
	$query = "SELECT COUNT(*) AS count
        FROM documents
        	LEFT JOIN users
        		ON documents.userID=users.id";
    if ( !empty($category) )
        $query .= " LEFT JOIN documents_category ON documents.documentID=documents_category.id_document
            		LEFT JOIN categories ON documents_category.id_category=categories.id";
    if ( !empty($tag) )
        $query .= " LEFT JOIN documents_tags ON documents.documentID=documents_tags.id_document
        			LEFT JOIN tags ON documents_tags.id_tag=tags.id";
    $query .= " WHERE 1";
    if ( !empty($category) )
    	$query .= " AND categories.value='$category'";
    if ( !empty($tag) )
    	$query .= " AND tags.value='$tag'";
    return mysqli_fetch_assoc(mysqli_query($conn, $query))['count'];
};

$documents['getDocuments'] = function($documentsLimit = 5, $offset = 0, $tag, $category) use ($conn){
	$documents = [];
	$query = "SELECT
        documents.documentID,
        users.name AS user,
        documents.documentName,
        documents.documentAnnotation,
        documents.documentContent,
        documents.documentThumbs,
        documents.documentTitle
        FROM documents
        	LEFT JOIN users
        		ON documents.userID=users.id";
    if ( !empty($category) )
        $query .= " LEFT JOIN documents_category ON documents.documentID=documents_category.id_document
            		LEFT JOIN categories ON documents_category.id_category=categories.id";
    if ( !empty($tag) )
        $query .= " LEFT JOIN documents_tags ON documents.documentID=documents_tags.id_document
        			LEFT JOIN tags ON documents_tags.id_tag=tags.id";
    $query .= " WHERE 1";
    if ( !empty($category) )
    	$query .= " AND categories.value='$category'";
    if ( !empty($tag) )
    	$query .= " AND tags.value='$tag'";
    $query .= " LIMIT $documentsLimit OFFSET $offset";
	$resultDocuments = mysqli_query($conn, $query);
	if ($resultDocuments) {
		foreach ($resultDocuments as $doc ) {
	        $documents[] = $doc;
	    }
	} else {
		echo mysqli_error($conn);
	}
	return $documents;
};





$documents['getTags'] = function() use ($conn){
	$tags = [];
	$resultTags = mysqli_query($conn, "SELECT * FROM tags") ;
    foreach ($resultTags as $t) {
        $tags[$t["id"]] = $t['value'];
    }
	return $tags;
};

$documents['setTags'] = function($tags) use ($conn) {
    $tags_string = "('". implode("'), ('", $tags) ."')";
    mysqli_query($conn, 'INSERT INTO tags(value) VALUES'.$tags_string);
};


$documents['getDocumentTags'] = function($id) use ($conn) {
	$query = "SELECT tags.value, tags.id
				FROM documents_tags JOIN tags
				ON documents_tags.id_tag = tags.id
				WHERE documents_tags.id_document = $id";
	$res = [];
    if ( $result = mysqli_query($conn, $query) ){
        foreach ($result as $t) {
            $res[$t['id']] = $t['value'];
        }
    }
    return $res;
};

$documents['delExitingDocumentTags'] = function($id, $exiting_tags) use ($conn) {
    $exiting_tags_string = implode("', '", $exiting_tags);
    mysqli_query($conn, "DELETE FROM documents_tags WHERE id_document = $id AND id_tag NOT IN ('$exiting_tags_string')");
};

$documents['getDocumentTagsID'] = function($tags) use ($conn) {
	$tagsID = [];
    $tags_ids_string = implode("', '", $tags);
    if ( $results_tags = mysqli_query($conn, "SELECT id FROM tags WHERE value IN ('$tags_ids_string')") ) {
        foreach ($results_tags as $t) {
            $tagsID[] = $t['id'];
        }
    }
    return $tagsID;
};

$documents['setDocumentTags'] = function($documentID, $tags) use ($conn) {
    $documents_tags = "($documentID,". implode("), ($documentID,", $tags) .")";
    mysqli_query($conn, 'INSERT INTO documents_tags(id_document, id_tag) VALUES' . $documents_tags);
};



$documents['setCategory'] = function($documentID, $cat, $catID) use ($conn) {
	if ( $res = mysqli_query($conn,
		"SELECT documents_category.id_category, categories.value
			FROM documents_category LEFT JOIN categories ON documents_category.id_category = categories.id
    		WHERE documents_category.id_document=$documentID") ) {
		$res = mysqli_fetch_assoc($res);
	}
	if ( $res && $res['id_category'] == $catID ) return false;

	if ( $cat === '' ) {
		if ( $res ) {
			mysqli_query($conn, "DELETE FROM documents_category WHERE id_document=$documentID");
		}
		return false;
	}
	if ( $catID == false ) {
		mysqli_query($conn, "INSERT INTO categories(value) VALUES ('$cat')");
		$catID = mysqli_insert_id($conn);
	}
	if ( $res ) {
		mysqli_query($conn, "UPDATE documents_category SET id_category=$catID WHERE id_document=$documentID");
	} else {
		mysqli_query($conn, "INSERT INTO documents_category(id_document, id_category) VALUES ('$documentID', '$catID')");
	}
};

$documents['getCategories'] = function() use ($conn){
	$categories = [];
	$result = mysqli_query($conn, "SELECT id, value FROM categories");
	foreach ($result as $res) {
        $categories[$res['id']] = $res['value'];
    }
	return $categories;
};




$model['documents'] = $documents;