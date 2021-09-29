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
$stmt = $ColisMethods->GetAllColisRamasserByExpediteurPaging($from_record_num, $records_per_page,
isset($_GET['client_id']) ? $_GET['client_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die());
$tocken=$_GET['tocken'] ;
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $colis_arr=array();
    $colis_arr["records"]=array();
    $colis_arr["paging"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $colis_item=array(
            "colis_id" =>  $colis_id,
            "expediteur" =>  $client,
            "code_envoi" =>  $code_envoi,
            "numeroCommande" =>  $num_commande,
            "destinataire" => $Destinataire,
            "livreur" => $livreur,
            "crbt" => $crbt,
            "frais" => $frais,
            "ville" => $ville,
            "adresse" => $adresse,
            "telephone" => $telephone,
            "status" =>$ColisMethods->statuts($status_reel),
            "date_ramassage" => $date_ramassage,
            "date_livraison" => $date_livraison,
            "date_retour" => $date_retour,
            "commentaire" => $commentaire,
            "bonLivraison" => $NomBonLivraison,
            "typeBonLivraison" => $ColisMethods->type_bonlivraison($TypeBonLivarsion),
            "facture" => $NomFacture,
            "etatColisLivre" => $NomEtatColisLivre
        );
        array_push($colis_arr["records"], $colis_item);
    }
    // include paging
    $expediteur=$_GET['client_id'];
    $total_rows=$ColisMethods->nombrecolisRamasser(isset($_GET['client_id']) ? $_GET['client_id'] : die());
    $page_url="{$home_url_client}Colis/getColisRamasser.php?client_id=".(int)$expediteur."&tocken=$tocken&";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $colis_arr["paging"]=$paging;
    echo json_encode($colis_arr);
}
    else{
        echo json_encode(
            array("message" => "Tocken incorrecte  ou les Colis n'existe pas ")
        );
    }
?>