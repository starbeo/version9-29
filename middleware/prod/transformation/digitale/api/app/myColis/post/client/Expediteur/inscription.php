<?php 
// include database and object files
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
include_once '../../../../../config/database.php';
include_once '../../../../../entities/expediteur.php';
		//Getting values
        if((isset($_POST['personne_a_contacte']) and !empty($_POST['personne_a_contacte'])) 
           and (isset($_POST['email']) and !empty($_POST['email'])) 
           and (isset($_POST['telephone']) and !empty($_POST['telephone'])) 
           and (isset($_POST['adresse']) and !empty($_POST['adresse'])) 
           and (isset($_POST['ville_id']) and !empty($_POST['ville_id'])) 
           and (isset($_POST['affiliation_code'])) 
           and (isset($_POST['tocken']) and !empty($_POST['tocken']))
           and ( $_POST['tocken']=='$2a$08$Az.b2.8Vkpw9XwtlpYRSseU')
           ){


            // get database connection
            $database = new Database();
            $db = $database->getConnection();
            // instantiate Client object
            $Expediteur = new Expediteur($db);
            $personne_a_contacte = $_POST['personne_a_contacte'];
            $email = $_POST['email'];
            $telephone = $_POST['telephone'];
            $adresse = $_POST['adresse'];
            $affiliation_code = $_POST['affiliation_code'];
            
            if(empty($_POST['ville_id']))
            {
                $ville_id = '';
            }
            else
            {
                $ville_id = $_POST['ville_id'];
            }
            if ($Expediteur->getlastid()=="")
            {
                $Expediteur->id = "1";
            }
            else
            {
                $Expediteur->id = $Expediteur->getlastid();
            }
            
            // set product property values
            
            $Expediteur->personne_a_contacte = $personne_a_contacte;
            $Expediteur->email = $email;
            $Expediteur->telephone = $telephone;
            $Expediteur->adresse = $adresse;
            $Expediteur->ville_id = $ville_id;
            $Expediteur->affiliation_code = $affiliation_code;
            // create the product
            //ajouter 14/01/2019
            
            $customer_id=$Expediteur->check_client_existe($email);
            if($customer_id==null)
            {
                
            if($Expediteur->createclientenattente()){
               echo '{';
                    echo '"message": "Client Ajouté  avec succès"';
                echo '}';
                //echo 'Client was created.';
            }
            
            // if unable to create the product, tell the user
            else{
                echo '{';
                    echo '"message": "une erreur est survenue lors d ajoute Client ."';
                echo '}';
                //echo 'une erreur est survenue lors d ajoute un Client .';
            }
            }
            else
            {
                 echo '{';
                    echo '"message": "Client déjà existe"';
                echo '}';
            }
            
    }
              else{
                echo '{';
                    echo '"message": "Les Champs Sont Obligatoire ."';
                echo '}';
                //echo 'Les Champs Sont Obligatoire';
            }
?>