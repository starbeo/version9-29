<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// include database and object files
include_once '../../../../../config/database.php';
include_once '../Methods/LivreurMethods.php';

// get database connection
$database = new Database();

$db = $database->getConnection();

// prepare Colis object
$LivreurMethods = new LivreurMethods($db);
// set ID property of ColisMethods 
$LivreurMethods->getInformationLivreur(isset($_GET['livreur_id']) ? $_GET['livreur_id'] : die(),isset($_GET['tocken']) ? $_GET['tocken'] : die());
$livreur_arr = array(
    "livreur_id" =>  $LivreurMethods->staffid,
    "prenom" =>  $LivreurMethods->firstname,
    "nom" =>  $LivreurMethods->lastname,
    "email" =>  $LivreurMethods->email,
    "telephone" =>  $LivreurMethods->telephone,
    "latitude" =>  $LivreurMethods->latitude,
    "longitude" =>  $LivreurMethods->longitude,
    "ville" =>  $LivreurMethods->ville
);

// make it json format
print_r(json_encode($livreur_arr));
?>
