<?php 
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(isset($_POST['username']) and isset($_POST['password'])){
		$db = new DbOperations(); 
		if($db->userLogin($_POST['username'], $_POST['password'])){
			$user = $db->getUserByUsername($_POST['username']);

				echo '{';
					echo '"client_id":'.$user['id'].',';
					echo '"tocken":"'.$user['password'];
					echo '"';
				echo '}';
			
		}else{
			echo '{';
				echo '"response": "Nom  utilisateur ou mot de passe invalide"';
			echo '}';	
		}
	}else{
		$response = false;
		echo '{';
			echo '"response": "Nom utilisateur et Mot de passe sont Obligatoires"';
		echo '}';
	}
}
?>