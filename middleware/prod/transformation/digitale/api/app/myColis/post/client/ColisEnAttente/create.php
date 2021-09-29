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
        if((isset($_POST['numerocommande']) and !empty($_POST['numerocommande'])) 
           and (isset($_POST['destinataire']) and !empty($_POST['destinataire'])) 
           and (isset($_POST['crbt']) and !empty($_POST['crbt'])) 
           and (isset($_POST['telephone']) and !empty($_POST['telephone'])) 
           and (isset($_POST['adresse']) and !empty($_POST['adresse'])) 
           and (isset($_POST['ville_id']) and !empty($_POST['ville_id'])) 
           and (isset($_POST['client_id']) and !empty($_POST['client_id'])) 
           ){
            // get database connection
            $database = new Database();
            $db = $database->getConnection();
            // instantiate Client object
            $Colis = new Colis($db);
            $numerocommande = $_POST['numerocommande'];
            $destinataire = $_POST['destinataire'];
            $crbt = $_POST['crbt'];
            $telephone = $_POST['telephone'];
            $adresse = $_POST['adresse'];
            $ville_id = $_POST['ville_id'];
            $client_id = $_POST['client_id'];
            
            if(empty($_POST['commentaire']))
            {
                $commentaire = '';
            }
            else
            {
                $commentaire = $_POST['commentaire'];
            }
            
            
            // set product property values
            $Colis->id = $Colis->getlastid();
            $Colis->codeenvoi = $numerocommande;
            $Colis->destinataire = $destinataire;
            $Colis->crbt = $crbt;
            $Colis->telephone = $telephone;
            $Colis->adresse = $adresse;
            $Colis->ville_id = $ville_id;
            $Colis->client_id = $client_id;
            $Colis->commentaire = $commentaire;
            // create the product
            if($Colis->createcolisenattente()){
               echo '{';
                    echo '"message": "Colis En Attente Ajouté  avec succès"';
                echo '}';
                //echo 'Client was created.';
            }
            
            // if unable to create the product, tell the user
           else{
                echo '{';
                    echo '"message": "une erreur est survenue lors d ajoute Colis En Attente ."';
                echo '}';
                //echo 'une erreur est survenue lors d ajoute un Client .';
            }
    }
              else{
                echo '{';
                    echo '"message": "Les Champs Sont Obligatoire ."';
                echo '}';
                //echo 'Les Champs Sont Obligatoire';
            }
?>