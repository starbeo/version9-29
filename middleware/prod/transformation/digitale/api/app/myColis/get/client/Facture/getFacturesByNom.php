<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// include database and object files

include_once '../../../../../config/database.php';
include_once '../Methods/FactureMethods.php';
// get database connection
$database = new Database();
$db = $database->getConnection();
// prepare Colis object
$FactureMethods = new FactureMethods($db);
// set ID property of ColisMethods 
$FactureMethods->getFacturesByNom(
isset($_GET['client_id']) ? $_GET['client_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die(),
isset($_GET['nomfacture']) ? $_GET['nomfacture'] : die()
);
$facture_arr = array(

    "facture_id" =>  $FactureMethods->facture_id,
    "nom" =>  $FactureMethods->nom,
    "statut" => $FactureMethods->statut_facture($FactureMethods->status),
    "type" => $FactureMethods->type_facture($FactureMethods->type),
    "total_brut" =>  $FactureMethods->total_crbt,
    "total_frais" =>  $FactureMethods->total_frais,
    "total_refuse" =>  $FactureMethods->total_refuse,
    "total_net" =>  $FactureMethods->total_net,
    "commentaire" =>  $FactureMethods->commentaire,
    "date_creation" =>  $FactureMethods->date_created

);
// make it json format
print_r(json_encode($facture_arr));
?>
