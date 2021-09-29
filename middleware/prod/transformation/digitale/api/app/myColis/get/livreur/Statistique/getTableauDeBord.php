<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
// include database and object files
include_once '../../../../../config/database.php';
include_once '../Methods/StatistiqueMethods.php';
// get database connection
$database = new Database();
$db = $database->getConnection();
// prepare Colis object
$StatistiqueMethods = new StatistiqueMethods($db);

if(isset($_GET['livreur_id']) and isset($_GET['tocken'])){
// set ID property of ColisMethods 
$livreur_id=isset($_GET['livreur_id']) ? $_GET['livreur_id'] : die();
$tocken=isset($_GET['tocken']) ? $_GET['tocken'] : die();
$statistique_arr=array();
$statistique_arr["records"]=array();
/* */
$estatistique_item=array(
    "Title" => "Colis",
    "Value" => $StatistiqueMethods->getnombrecolisTotal($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Livrés",
    "Value" => $StatistiqueMethods->getnombrecolisLivrer($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Retournés",
    "Value" => $StatistiqueMethods->getnombrecolisRetourner($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Expédiés",
    "Value" => $StatistiqueMethods->getnombrecolisExpedier($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Annulés",
    "Value" => $StatistiqueMethods->getnombrecolisAnnuler($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Refusés",
    "Value" => $StatistiqueMethods->getnombrecolisRefuser($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Pas Réponse",
    "Value" => $StatistiqueMethods->getnombrecolisPasResponse($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Reportés",
    "Value" =>$StatistiqueMethods->getnombrecolisReporte($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Injoignables",
    "Value" => $StatistiqueMethods->getnombrecolisInjoignable($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Ramassés ",
    "Value" => $StatistiqueMethods->getnombrecolisRamasser($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Retourner a L'agence",
    "Value" => $StatistiqueMethods->getnombrecolisRetournerAlagence($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Numéro Erroner",
    "Value" => $StatistiqueMethods->getnombrecolisNumeroErroner($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Bon Livraison",
    "Value" => $StatistiqueMethods->getnombreBonlivraison($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Bon Livraison Sortie",
    "Value" => $StatistiqueMethods->getnombreBonlivraisonSortie($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Bon Livraison Retourner",
    "Value" => $StatistiqueMethods->getnombreBonlivraisonRetourner($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Etat Colis Livrer",
    "Value" => $StatistiqueMethods->getnombreEtatColisLivrer($livreur_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);




echo json_encode($statistique_arr);

}
else
{
    $statistique_array = array(
        "message" => "Tocken incorrecte ou Livreur n'existe pas"
    );
    print_r(json_encode($statistique_array));
}

?>
