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
$stmt = $EtatColisLivrerMethods->GetAllversementPaging($from_record_num, $records_per_page,
isset($_GET['etat_colis_livre_id']) ? $_GET['etat_colis_livre_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die());
$tocken=$_GET['tocken'] ;
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $etatcolislivrer_arr=array();
    $etatcolislivrer_arr["records"]=array();
    $etatcolislivrer_arr["paging"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $etatcolislivrer_item=array(
            "reference" =>  $name,
            "montant" =>  $total,
            "date_creation" =>  $date_created
        );
        array_push($etatcolislivrer_arr["records"], $etatcolislivrer_item);
    }
    // include paging
    $etat_colis_livre_id=$_GET['etat_colis_livre_id'];
    $total_rows=$EtatColisLivrerMethods->nombreversementEtatColisLivrer(isset($_GET['etat_colis_livre_id']) ? $_GET['etat_colis_livre_id'] : die());
    $page_url="{$home_url_livreur}EtatColisLivrer/getVersementList.php?etat_colis_livre_id=".(int)$etat_colis_livre_id."&tocken=$tocken&";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $etatcolislivrer_arr["paging"]=$paging;
    echo json_encode($etatcolislivrer_arr);
}
    else{
        echo json_encode(
            array("message" => "Tocken incorrecte  ou les Factures n'existe pas ")
        );
    }
?>