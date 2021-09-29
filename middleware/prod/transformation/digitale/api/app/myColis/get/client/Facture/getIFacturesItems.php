<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../../../../config/core.php';
include_once '../../../../../shared/utilities.php';
include_once '../../../../../config/database.php';
include_once '../Methods/FactureMethods.php';
// utilities
$utilities = new Utilities();
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$FactureMethods = new FactureMethods($db);
// query products
$stmt = $FactureMethods->GetAllFacturesItemsByExpediteurPaging($from_record_num, $records_per_page,
isset($_GET['client_id']) ? $_GET['client_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die(),
isset($_GET['facture_id']) ? $_GET['facture_id'] : die());
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
            "date_ramassage" => $date_ramassage,
            "date_livraison" => $date_livraison,
            "date_retour" =>  $date_retour,
            "crbt" =>  $crbt,
            "frais" =>  $frais
        );
        array_push($facture_arr["records"], $facture_item);
    }
    // include paging
    $expediteur=$_GET['client_id'];
    $facture_id=$_GET['facture_id'];
    $total_rows=$FactureMethods->nombrefacturesItems(isset($_GET['client_id']) ? $_GET['client_id'] : die(),
    isset($_GET['facture_id']) ? $_GET['facture_id'] : die());
    $page_url="{$home_url_client}Facture/getIFacturesItems.php?client_id=".(int)$expediteur."&facture_id=".(int)$facture_id."&tocken=$tocken&";
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