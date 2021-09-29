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
        if((isset($_POST['notification_id']) and !empty($_POST['notification_id'])) 
           ){
            // get database connection
            $database = new Database();
            $db = $database->getConnection();
            // instantiate Client object
            $Colis = new Colis($db);
            $notification_id = $_POST['notification_id'];
            // create the product
            if($Colis->update_notification_Client($notification_id)){
               echo '{';
                    echo '"message": "Notification modifié avec succès"';
                echo '}';
            }
            
           else{
                echo '{';
                    echo '"message": "une erreur est survenue lors de modification la notif ."';
                echo '}';
            }
    }
              else{
                echo '{';
                    echo '"message": "Les Champs Sont Obligatoire ."';
                echo '}';
            }
?>