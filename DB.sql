 CREATE TABLE IF NOT EXISTS documents(
    documentID INT PRIMARY KEY AUTO_INCREMENT,
    documentName VARCHAR(255),
    documentAnnotation TEXT,
    documentContent LONGTEXT,
    documentThumbs TEXT,
    documentTitle TEXT,
    documentTags TEXT,
    documentCategory TEXT
);

CREATE TABLE IF NOT EXISTS categories( id INT PRIMARY KEY AUTO_INCREMENT, value TEXT );

CREATE TABLE IF NOT EXISTS documents_category( id INT PRIMARY KEY AUTO_INCREMENT, id_document INT, id_category INT );

CREATE TABLE IF NOT EXISTS tags( id INT PRIMARY KEY AUTO_INCREMENT, value TEXT );

CREATE TABLE IF NOT EXISTS documents_tags( id_document INT, id_tag INT );

CREATE TABLE IF NOT EXISTS users( id INT PRIMARY KEY AUTO_INCREMENT, name CHAR(50) UNIQUE NOT NULL, password CHAR(50) );