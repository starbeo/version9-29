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
$ColisMethods->readOneByCodeEnvoi(
isset($_GET['code_envoi']) ? $_GET['code_envoi'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die(),
isset($_GET['livreur_id']) ? $_GET['livreur_id'] : die()
);
$colis_arr = array(
    "colis_id" =>  $ColisMethods->colis_id,
    "expediteur" => $ColisMethods->client,
    "code_envoi" =>  $ColisMethods->code_envoi,
    "numeroCommande" => $ColisMethods->numeroCommande,
    "destinataire" =>  $ColisMethods->Destinataire,
    "livreur" => $ColisMethods->livreur,
    "crbt" => $ColisMethods->crbt,
    "frais" => $ColisMethods->frais,
    "ville" => $ColisMethods->ville,
    "adresse" => $ColisMethods->adresse,
    "telephone" => $ColisMethods->telephone,
    "status" =>$ColisMethods->statuts($ColisMethods->status_reel),
    "date_ramassage" => $ColisMethods->date_ramassage,
    "date_livraison" => $ColisMethods->date_livraison,
    "date_retour" => $ColisMethods->date_retour,
    "commentaire" => $ColisMethods->commentaire,
    "bonLivraison" => $ColisMethods->bonLivraison,
    "typeBonLivarsion" => $ColisMethods->type_bonlivraison($ColisMethods->typeBonLivarsion),
    "facture" => $ColisMethods->nomFacture,
    "etatColisLivre" => $ColisMethods->etatColisLivre,

);
// make it json format
print_r(json_encode($colis_arr));
?>
