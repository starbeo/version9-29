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
$stmt = $BonLivraisonMethods->GetAllBonLivraisonByExpediteurPaging($from_record_num, $records_per_page,
isset($_GET['client_id']) ? $_GET['client_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die());
$tocken=$_GET['tocken'] ;
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $bonlivraison_arr=array();
    $bonlivraison_arr["records"]=array();
    $bonlivraison_arr["paging"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $bonlivraison_item=array(
            "bonlivraison_id" =>  $id,
            "nom" =>  $nom,
            "date_creation" => $date_created
        );
        array_push($bonlivraison_arr["records"], $bonlivraison_item);
    }
    // include paging
    $expediteur=$_GET['client_id'];
    $total_rows=$BonLivraisonMethods->nombrebonlivraison(isset($_GET['client_id']) ? $_GET['client_id'] : die());
    $page_url="{$home_url_client}BonLivraison/getBonLivraisonList.php?client_id=".(int)$expediteur."&tocken=$tocken&";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $bonlivraison_arr["paging"]=$paging;
    echo json_encode($bonlivraison_arr);
}
    else{
        echo json_encode(
            array("message" => "Tocken incorrecte  ou les Factures n'existe pas ")
        );
    }
?>