<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../../../../config/core.php';
include_once '../../../../../shared/utilities.php';
include_once '../../../../../config/database.php';
include_once '../Methods/BonLivraisonMethods.php';
// utilities
$utilities = new Utilities();
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$BonLivraisonMethods = new BonLivraisonMethods($db);
// query products
$stmt = $BonLivraisonMethods->GetAllBonLivraisonItemsPaging($from_record_num, $records_per_page,
isset($_GET['livreur_id']) ? $_GET['livreur_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die(),
isset($_GET['bonlivrasion_id']) ? $_GET['bonlivrasion_id'] : die());
$tocken=$_GET['tocken'] ;
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $facture_arr=array();
    $facture_arr["records"]=array();
    $facture_arr["paging"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $facture_item=array(
            "colis_id" =>  $colis_id,
            "code_envoi" =>  $code_barre,
            "num_commande" => $num_commande,
            "destinataire" => $nom_complet,
            "telephone" =>  $telephone,
            "crbt" =>  $crbt,
            "status" =>$BonLivraisonMethods->statuts($status_reel)
            
        );
        array_push($facture_arr["records"], $facture_item);
    }
    // include paging
    $livreur_id=$_GET['livreur_id'];
    $bonlivrasion_id=$_GET['bonlivrasion_id'];
    $total_rows=$BonLivraisonMethods->nombrebonlivraisonItems(isset($_GET['livreur_id']) ? $_GET['livreur_id'] : die(),
    isset($_GET['bonlivrasion_id']) ? $_GET['bonlivrasion_id'] : die());
    $page_url="{$home_url_livreur}BonLivraison/getBonLivraisonItems.php?livreur_id=".(int)$livreur_id."&bonlivrasion_id=".(int)$bonlivrasion_id."&tocken=$tocken&";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $facture_arr["paging"]=$paging;
    echo json_encode($facture_arr);
}
    else{
        echo json_encode(
            array("message" => "Tocken incorrecte  ou les Factures n'existe pas ")
        );
    }
?>