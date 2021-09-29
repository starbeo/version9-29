<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../../../../config/core.php';
include_once '../../../../../shared/utilities.php';
include_once '../../../../../config/database.php';
include_once '../Methods/NotificationMethods.php';
// utilities
$utilities = new Utilities();
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$NotificationMethods = new NotificationMethods($db);
// query products
$stmt = $NotificationMethods->GetNotification(isset($_GET['client_id']) ? $_GET['client_id'] : die(),isset($_GET['tocken']) ? $_GET['tocken'] : die());
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $notification_arr=array();
    $notification_arr["records"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $notification_item=array(
            "id" =>  $id,
            "isread" =>  $isread,
            "date" =>  $date,
            "description" =>  $description,
        );
        array_push($notification_arr["records"], $notification_item);
    }
    // include paging
    echo json_encode($notification_arr);
}
    else{
        echo json_encode(
            array("message" => "Tocken incorrecte  ou les Notifications n'existe pas ")
        );
    }
?>