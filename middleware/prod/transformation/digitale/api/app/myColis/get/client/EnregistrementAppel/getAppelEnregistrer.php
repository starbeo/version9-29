<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../../../../config/core.php';
include_once '../../../../../shared/utilities.php';
include_once '../../../../../config/database.php';
include_once '../Methods/ColisMethods.php';
// utilities
$utilities = new Utilities();
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$ColisMethods = new ColisMethods($db);
// query products
$stmt = $ColisMethods->GetEnregistrementAppelsByCodeEnvoi(isset($_GET['code_envoi']) ? $_GET['code_envoi'] : die());
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $statuts_arr=array();
    $statuts_arr["records"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $statuts_item=array(
            "id" =>  $id,
            "date_appel" =>  $date_appel,
            "code_barre" =>  $code_barre,
            "livreur" =>  $livreur,
        );
        array_push($statuts_arr["records"], $statuts_item);
    }
    // include paging
    echo json_encode($statuts_arr);
}
    else{
        echo json_encode(
            array("message" => "Aucun Enregistrement Trouvé ")
        );
    }
?>