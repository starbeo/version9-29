<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// include database and object files

include_once '../../../../../config/database.php';
include_once '../Methods/BonLivraisonMethods.php';
// get database connection
$database = new Database();
$db = $database->getConnection();
// prepare Colis object
$BonLivraisonMethods = new BonLivraisonMethods($db);
// set ID property of ColisMethods 
$BonLivraisonMethods->getBonLivraisonByNom(
isset($_GET['client_id']) ? $_GET['client_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die(),
isset($_GET['nombonlivraison']) ? $_GET['nombonlivraison'] : die()
);
$facture_arr = array(

    "bonlivraison_id" =>  $BonLivraisonMethods->id,
    "nom" =>  $BonLivraisonMethods->nom,
    "date_creation" => $BonLivraisonMethods->date_created,

);
// make it json format
print_r(json_encode($facture_arr));
?>
