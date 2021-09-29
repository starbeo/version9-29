<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../../../../config/core.php';
include_once '../../../../../shared/utilities.php';
include_once '../../../../../config/database.php';
include_once '../Methods/EtatColisLivrerMethods.php';
// utilities
$utilities = new Utilities();
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$EtatColisLivrerMethods = new EtatColisLivrerMethods($db);
// query products
$stmt = $EtatColisLivrerMethods->GetAllItemsEtatColisLivrerPaging($from_record_num, $records_per_page,
isset($_GET['etat_colis_livre_id']) ? $_GET['etat_colis_livre_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die());
$tocken=$_GET['tocken'] ;
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $etat_colis_livre_arr=array();
    $etat_colis_livre_arr["records"]=array();
    $etat_colis_livre_arr["paging"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $etat_colis_livre_item=array(
            "colis_id" =>  $colis_id,
            "code_envoi" =>  $code_barre,
            "num_commande" => $num_commande,
            "destinataire" => $nom_complet,
            "telephone" =>  $telephone,
            "crbt" =>  $crbt
        );
        array_push($etat_colis_livre_arr["records"], $etat_colis_livre_item);
    }
    // include paging
    $etat_colis_livre_id=$_GET['etat_colis_livre_id'];
    $total_rows=$EtatColisLivrerMethods->nombreetatcolislivrerItems(isset($_GET['etat_colis_livre_id']) ? $_GET['etat_colis_livre_id'] : die());
    $page_url="{$home_url_livreur}EtatColisLivrer/getEtatColisLivrertems.php?etat_colis_livre_id=".(int)$etat_colis_livre_id."&tocken=$tocken&";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $etat_colis_livre_arr["paging"]=$paging;
    echo json_encode($etat_colis_livre_arr);
}
    else{
        echo json_encode(
            array("message" => "Tocken incorrecte  ou les Factures n'existe pas ")
        );
    }
?>