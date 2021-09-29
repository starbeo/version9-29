<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// include database and object files
include_once '../../../../../config/database.php';
include_once '../Methods/ExpediteurMethods.php';
// get database connection
$database = new Database();
$db = $database->getConnection();
// prepare Colis object
$ExpediteurMethods = new ExpediteurMethods($db);
// set ID property of ColisMethods 
$ExpediteurMethods->getInformationExpediteur(isset($_GET['client_id']) ? $_GET['client_id'] : die(),isset($_GET['tocken']) ? $_GET['tocken'] : die());
$cexpediteur_arr = array(
    "client_Id" =>  $ExpediteurMethods->expediteur_id,
    "nomClient" =>  $ExpediteurMethods->client,
    "contact" =>  $ExpediteurMethods->contact,
    "email" => $ExpediteurMethods->email,
    "telephone" => $ExpediteurMethods->telephone1." ".$ExpediteurMethods->telephone2,
    "adresse" => $ExpediteurMethods->adresse,
    "ville" => $ExpediteurMethods->ville,
    "rib" => $ExpediteurMethods->rib,
    "frais_livraison_interieur" => $ExpediteurMethods->frais_livraison_interieur,
    "frais_livraison_exterieur" => $ExpediteurMethods->frais_livraison_exterieur,
    "frais_retour" => $ExpediteurMethods->frais_retourne,
    "frais_refuse" => $ExpediteurMethods->frais_refuse,
    "frais_stockage" => $ExpediteurMethods->frais_stockage,
    "frais_emballage" => $ExpediteurMethods->frais_emballage,
    "frais_etiquette" => $ExpediteurMethods->frais_etiquette,
);
// make it json format
print_r(json_encode($cexpediteur_arr));
?>
