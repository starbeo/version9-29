<?php

             session_start();

         
            if(isset($_POST['email']) && $_POST['email']!='' ){

              
              $destinataire = $_POST['email'];
              $name=$_POST['name'];
	            $expediteur = 'contact@powercoursier.ma'; 
             
              
              $objet = 'Demande Information - Power Coursier';
              $headers  = 'MIME-Version: 1.0' . "\n"; // Version MIME
              $headers .= 'Content-type: text/html; charset=ISO-8859-1'."\n"; // l'en-tete Content-type pour le format HTML
              $headers .= 'Reply-To: '.$expediteur."\n"; // Mail de reponse
              $headers .= 'From: "'.$name.'"  <contact@powercoursier.ma>'."\n"; // Expediteur
              $headers .= 'Delivered-to: '.$destinataire."\n"; // Destinataire    
              $message = '<div style="width: 100%; text-align: center; font-weight: bold"> 
                 Name: '.$_POST['name'].' <br>'.
                'E-mail : '.$_POST['email'].' <br>'.
                'ville : '.$_POST['ville'].' <br>'.
                'objet : '.$_POST['objet'].' <br>'.              
                'message: '.$_POST['message'].' <br>'.'</div>';

             
                mail('support@powercoursier.ma', $objet, $message, $headers);
                 
                 $_SESSION['email'] = true;
               }

               header('Location:'. $_SERVER["HTTP_REFERER"]);


             

 
?>