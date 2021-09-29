<?php
 /*
 author: Belal Khan 
 website: https://www.simplifiedcoding.net
 
 My Database is androiddb 
 you need to change the database name rest the things are default if you are using wamp or xampp server
 You may need to change the host user name or password if you have changed the defaults in your server
 */
 
 //Defining Constants
 define('HOST','161.35.115.15');
 define('USER','mycolisprod');
 define('PASS','O26T70egWQ2ULxuE');
 define('DB','myColisprod');

 //Connecting to Database
 $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
 ?>
