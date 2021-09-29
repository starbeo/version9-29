<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// include database and object files
include_once '../../../../../config/database.php';
include_once '../Methods/NotificationMethods.php';
// get database connection
$database = new Database();
$db = $database->getConnection();
// prepare Colis object
$NotificationMethods = new NotificationMethods($db);
// set ID property of NotificationMethods 
$NotificationMethods->getNotifPopup(
isset($_GET['client_id']) ? $_GET['client_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die()
);
$notification_arr = array(
    "notification_id" =>  $NotificationMethods->notification_id,
    "isread" => $NotificationMethods->isread,
    "date" =>  $NotificationMethods->date,
    "description" => $NotificationMethods->description
);
// make it json format
print_r(json_encode($notification_arr));
?>
