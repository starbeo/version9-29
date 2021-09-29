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
// get database connection
$database = new Database();
$db = $database->getConnection();
// prepare Colis object
$ColisMethods = new ColisMethods($db);
// set ID property of ColisMethods 
$ColisMethods->readOnecolisEnAttenteById(isset($_GET['id']) ? $_GET['id'] : die());
$colis_arr = array(
    "id" =>  $ColisMethods->colis_id,
    "code_envoi" =>  $ColisMethods->code_envoi,
    "destinataire" =>  $ColisMethods->Destinataire,
    "crbt" => $ColisMethods->crbt,
    "telephone" => $ColisMethods->telephone,
    "adresse" => $ColisMethods->adresse,
    "date_creation" => $ColisMethods->date_creation,
);
// make it json format
print_r(json_encode($colis_arr));
?>
