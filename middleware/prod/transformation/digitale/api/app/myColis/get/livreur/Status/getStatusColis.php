<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../../../../config/core.php';
include_once '../../../../../shared/utilities.php';
include_once '../../../../../config/database.php';
include_once '../Methods/StatutsMethods.php';
// utilities
$utilities = new Utilities();
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
// initialize object
$StatutsMethods = new StatutsMethods($db);
// query products
if((isset($_GET['tocken']) and !empty($_GET['tocken'])) and $_GET['tocken']=='$2a$08$Az.b2.8Vkpw9XwtlpYRSseU') 
{
$stmt = $StatutsMethods->GetStatusColisByCodeEnvoi(isset($_GET['code_envoi']) ? $_GET['code_envoi'] : die());
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
    // products array
    $statuts_arr=array();
    $statuts_arr["records"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        if ($date_reporte=='00-00-0000')
        {
            $date_reporte=null;
        }
        $statuts_item=array(
            "Code_envoi" =>  $code_envoi,
            "Statut" =>  $statut,
            "Emplacement" =>  $emplacement,
            "Date_reporte" =>  $date_reporte,
            "Date_Création" =>$date_created,
        );
        array_push($statuts_arr["records"], $statuts_item);
    }
    // include paging
    echo json_encode($statuts_arr);
}
    else{
        echo json_encode(
            array("message" => "Colis n'existe pas")
        );
    }
}

else{
        echo json_encode(
            array("message" => "Tocken invalide")
        );
    }
?>