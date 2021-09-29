<?php 
// include database and object files
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
        include '../../../../../config/dbConnect.php';
        
        //Getting values
        if((isset($_POST['Codebarre']) and !empty($_POST['Codebarre'])) 
           and (isset($_POST['id_user']) and !empty($_POST['id_user']))
           and (isset($_POST['tocken']) and !empty($_POST['tocken']))
           and ( $_POST['tocken']=='$2a$08$Az.b2.8Vkpw9XwtlpYRSseU')
           ){
		
		$Codebarre = strtoupper($_POST['Codebarre']);
		$id_user = $_POST['id_user'];
		$id_type="10";
        $idemplacement="9";
        $datetime = date("Y-m-d H:i:s");
        $utc = new DateTime($datetime, new DateTimeZone('UTC'));
        $utc->setTimezone(new DateTimeZone('Africa/Casablanca'));
        $datecreation=$utc->format('Y-m-d H:i:s');
		$message="Nouveau Statut Ajoute A Partir D&apos;un Smart phone [Code d envoi:";
		$message.=$Codebarre;
		$message.=",ID:";
		$message.=$id_user;
		
		// vérifié interdit changement statut 
		$result = mysqli_query($con, "SELECT  * FROM `tblcolis`  where code_barre='$Codebarre' and status_reel in (2,3,13,9) ");
        $num_rows = mysqli_num_rows($result);
        
        if ($num_rows==0)

{
    
     $sql="INSERT INTO `tblstatus`(code_barre,type,date_created,emplacement_id,date_reporte,id_utilisateur,	id_entreprise) 
       VALUES ('$Codebarre','". (int)$id_type . "',Now(),'". (int)$idemplacement . "','0000-00-00','". (int)$id_user . "',0)";
		//Executing query to database
		if(mysqli_query($con,$sql)){
			//update colis 
				//changer statut colis en temps réeel 
				$sql="UPDATE `tblcolis` SET status_id=1, status_reel='". (int)$id_type . "' ,date_livraison=null WHERE status_reel not in (2,3,13,9) and code_barre='$Codebarre'";
				mysqli_query($con,$sql);
				//ajouter journal activité 
				$sql="INSERT INTO tblactivitylog (description, date,staffid,id_entreprise) VALUES ('$message','$datecreation','". (int)$id_user . "',0)";
				mysqli_query($con,$sql);
            echo '{';
                echo '"success": "Statut Ajouté avec succès"';
            echo '}';
		}
		else{
            echo '{';
                echo '"success": "Impossible d ajouter Statut"';
            echo '}';
        }
    
}

else 

{
     echo '{';
            echo '"success": "Intérdit de changement statut  "';
        echo '}';
}
		

    }
    else{
        echo '{';
            echo '"success": "Les Champs Sont Obligatoire ."';
        echo '}';
        
    }
		//Closing the database 
		mysqli_close($con);
 ?>