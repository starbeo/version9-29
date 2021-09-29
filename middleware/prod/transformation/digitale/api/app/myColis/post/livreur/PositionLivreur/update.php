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
           and (isset($_POST['latitude']) and !empty($_POST['latitude'])) 
           and (isset($_POST['longitude']) and !empty($_POST['longitude']))
           ){
            // get database connection
            $database = new Database();
            $db = $database->getConnection();
            // instantiate Client object
            $Colis = new Colis($db);
            $livreur_id = $_POST['livreur_id'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];

            // create the product
            if($Colis->positionlivreur($latitude,$longitude,$livreur_id)){
               echo '{';
                    echo '"message": "position Livreur changé avec succès"';
                echo '}';
            }
            
           else{
                echo '{';
                    echo '"message": "une erreur est survenue lors de changement position livreur  ."';
                echo '}';
            }
    }
              else{
                echo '{';
                    echo '"message": "les champs sont obligatoire ."';
                echo '}';
            }
?>