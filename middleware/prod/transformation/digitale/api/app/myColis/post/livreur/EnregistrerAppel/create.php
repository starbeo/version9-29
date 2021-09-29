<?php 
// include database and object files
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
include_once '../../../../../config/database.php';
include_once '../../../../../entities/colis.php';
		//Getting values
        if((isset($_POST['livreur_id']) and !empty($_POST['livreur_id'])) 
           and (isset($_POST['code_envoi']) and !empty($_POST['code_envoi'])) 
           ){
            // get database connection
            $database = new Database();
            $db = $database->getConnection();
            // instantiate Client object
            $Colis = new Colis($db);
            $livreur_id = $_POST['livreur_id'];
            $code_envoi = $_POST['code_envoi'];

            // create the product
            if($Colis->enregitrerappel($livreur_id,$code_envoi)){
               echo '{';
                    echo '"message": "Enregistrement Appel  Ajouté  avec succès"';
                echo '}';
            }
            
           else{
                echo '{';
                    echo '"message": "une erreur est survenue lors d ajoute Enregistrement Appel ."';
                echo '}';
            }
    }
              else{
                echo '{';
                    echo '"message": "Les Champs Sont Obligatoire ."';
                echo '}';
            }
?>