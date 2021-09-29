<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// include database and object files
include_once '../../../../../config/database.php';
include_once '../Methods/ColisMethods.php';
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$ColisMethods = new ColisMethods($db);
// query Colis Livré
$stmt = $ColisMethods->getAllvilles();
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // Colis Livré array
    $ville_arr=array();
    $ville_arr["records"]=array();
    
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
          
        $ville_item=array(
            "ville_id" =>  $ville_id,
            "name" =>  $name,
        );
        array_push($ville_arr["records"], $ville_item);
    }
    echo json_encode($ville_arr);
}
else{
    echo json_encode(
        array("message" => "les Villes n'existe pas ")
    );
}
?>
