<?php
// show error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);
 
// home page url
//$home_url="http://powercoursier.ma/RestApiTrack/App/Get/";

$home_url_client="https://mycolis.app/middleware/prod/transformation/digitale/api/app/myColis/get/client/";
$home_url_livreur="https://mycolis.app/middleware/prod/transformation/digitale/api/app/myColis/get/livreur/";
 
// page given in URL parameter, default page is one
$page = isset($_GET['page']) ? $_GET['page'] : 1;
 
// set number of records per page
$records_per_page = 20;
 
// calculate for the query LIMIT clause
$from_record_num = ($records_per_page * $page) - $records_per_page;
?>
