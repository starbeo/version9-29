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

if(isset($_GET['client_id']) and isset($_GET['tocken'])){
// set ID property of ColisMethods 
$client_id=isset($_GET['client_id']) ? $_GET['client_id'] : die();
$tocken=isset($_GET['tocken']) ? $_GET['tocken'] : die();
$statistique_arr=array();
$statistique_arr["records"]=array();
/* */
$estatistique_item=array(
    "Title" => "Colis",
    "Value" => $StatistiqueMethods->getnombrecolisTotal($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis En Attentes",
    "Value" => $StatistiqueMethods->getnombrecolisEnAttente($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Livrés",
    "Value" => $StatistiqueMethods->getnombrecolislivre($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Retournés",
    "Value" => $StatistiqueMethods->getnombrecolisRetourne($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Expédiés",
    "Value" => $StatistiqueMethods->getnombrecolisExpedie($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Annulés",
    "Value" => $StatistiqueMethods->getnombrecolisAnnuler($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Refusés",
    "Value" => $StatistiqueMethods->getnombrecolisRefuse($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Pas Réponse",
    "Value" => $StatistiqueMethods->getnombrecolisPasResponse($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Reportés",
    "Value" =>$StatistiqueMethods->getnombrecolisReporte($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Injoignables",
    "Value" => $StatistiqueMethods->getnombrecolisInjoignable($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Ramassés",
    "Value" => $StatistiqueMethods->getnombrecolisRamasser($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Retourner à l'agence",
    "Value" => $StatistiqueMethods->getnombrecolisRetourneralagence($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Colis Numéro Erronées",
    "Value" => $StatistiqueMethods->getnombrecolisNumeroErronner($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Bon Livraison",
    "Value" => $StatistiqueMethods->getbonlivraisonclient($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Factures",
    "Value" => $StatistiqueMethods->getnombrefactureTotal($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Factures Livrés",
    "Value" => $StatistiqueMethods->getnombrefactureLivre($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Factures Retournés",
    "Value" => $StatistiqueMethods->getnombrefactureRetourne($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Réclamations",
    "Value" => $StatistiqueMethods->getnombrereclamationTotal($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Réclamations Traités",
    "Value" =>  $StatistiqueMethods->getnombrereclamationtraite($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Réclamations Non Traités",
    "Value" => $StatistiqueMethods->getnombrereclamationnontraite($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Solde Brut ",
    "Value" => $StatistiqueMethods->getsommecrbtcolislivre($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);

/* */
$estatistique_item=array(
    "Title" => "Total Frais Livraison",
    "Value" => $StatistiqueMethods->getsommefraiscolislivre($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Solde Net",
    "Value" => $StatistiqueMethods->getsommeprixnetcolislivre($client_id,$tocken)
);

array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Solde Brut Retournés",
    "Value" => $StatistiqueMethods->getsommecrbtcolisretourner($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Total Frais Retournés",
    "Value" => $StatistiqueMethods->getsommefraiscolisretourner($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Solde Retournés Net",
    "Value" => $StatistiqueMethods->getsommeprixnetcolisretourner($client_id,$tocken),
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Solde Brut EnCours",
    "Value" => $StatistiqueMethods->getsommecrbtcolisencours($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Total Frais EnCours",
    "Value" => $StatistiqueMethods->getsommefraiscolisencours($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);
/* */
$estatistique_item=array(
    "Title" => "Solde Encours Net",
    "Value" => $StatistiqueMethods->getsommeprixnetcolisencours($client_id,$tocken)
);
array_push($statistique_arr["records"], $estatistique_item);

echo json_encode($statistique_arr);

}
else
{
    $statistique_array = array(
        "message" => "Tocken incorrecte ou client n'existe pas"
    );
    print_r(json_encode($statistique_array));
}

?>
