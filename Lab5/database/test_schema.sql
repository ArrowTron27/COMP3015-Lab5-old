DROP DATABASE IF EXISTS posts_web_app_test;
CREATE DATABASE IF NOT EXISTS posts_web_app_test;

USE posts_web_app_test;

CREATE TABLE IF NOT EXISTS posts (
   id INT auto_increment NOT NULL PRIMARY KEY,
   title VARCHAR(100) NOT NULL,
   body VARCHAR(512) NOT NULL,
   created_at DATETIME NOT NULL,
   updated_at DATETIME
);
