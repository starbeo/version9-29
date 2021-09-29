<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../../../../config/core.php';
include_once '../../../../../shared/utilities.php';
include_once '../../../../../config/database.php';
include_once '../Methods/ReclamationsMethods.php';
// utilities
$utilities = new Utilities();
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$ReclamationsMethods = new ReclamationsMethods($db);
// query products
$stmt = $ReclamationsMethods->GetAllReclamationTraiteByExpediteurPaging($from_record_num, $records_per_page,
isset($_GET['client_id']) ? $_GET['client_id'] : die(),
isset($_GET['tocken']) ? $_GET['tocken'] : die());
$tocken=$_GET['tocken'] ;
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $reclamation_arr=array();
    $reclamation_arr["records"]=array();
    $reclamation_arr["paging"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $reclamation_item=array(
            "id" =>  $id,
            "message" =>  $message,
            "reponse" =>  $reponse,
            "date_created" =>  $date_created,
            "date_traitement" =>  $date_traitement,
            "etat" =>$ReclamationsMethods->etat($etat),
        );
        array_push($reclamation_arr["records"], $reclamation_item);
    }
    // include paging
    $expediteur=$_GET['client_id'];
    $total_rows=$ReclamationsMethods->nombrereclamationtraite(isset($_GET['client_id']) ? $_GET['client_id'] : die());
    $page_url="{$home_url_client}Reclamation/getReclamationTraite.php?client_id=".(int)$expediteur."&tocken=$tocken&";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $reclamation_arr["paging"]=$paging;
    echo json_encode($reclamation_arr);
}
    else{
        echo json_encode(
            array("message" => "Tocken incorrecte  ou les réclamations n'existe pas")
        );
    }
?>