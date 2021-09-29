<?php
           session_start();


           $ville= explode(";", $_POST['ville']);
           $id_ville=$ville[0];
           $nom_ville=$ville[1];
            // set post fields
            $post = [
              'personne_a_contacte' => $_POST['personne'],
              'email' => $_POST['email'],
              'telephone' => $_POST['tel'],
              'adresse' => $_POST['adresse'],
              'ville_id' => $id_ville,
              'affiliation_code' => '',
              'tocken' => '$2a$08$Az.b2.8Vkpw9XwtlpYRSseU'
            ];

            $ch = curl_init('https://mycolis.net/middleware/prod/transformation/digitale/api/app/myColis/post/client/Expediteur/inscription.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            $response = curl_exec($ch);
            curl_close($ch);
            //var_dump($response);


         	       
            if(isset($_POST['email']) && $_POST['email']!='' ){

              
              $destinataire = $_POST['email'];
              $name=$_POST['personne'];
	            $expediteur = 'contact@powercoursier.ma'; 
             
              $objet = 'Inscription Nouveau  Client- '.$name;
              $objet .= ' -Power Coursier';
              $headers  = 'MIME-Version: 1.0' . "\n"; // Version MIME
              $headers .= 'Content-type: text/html; charset=ISO-8859-1'."\n"; // l'en-tete Content-type pour le format HTML
              $headers .= 'Reply-To: '.$expediteur."\n"; // Mail de reponse
              $headers .= 'From: "'.$name.'"  <contact@powercoursier.ma>'."\n"; // Expediteur
              $headers .= 'Delivered-to: '.$destinataire."\n"; // Destinataire    
              $message = '<div style="width: 100%; text-align: center; font-weight: bold"> 
                 Personne: '.$_POST['personne'].' <br>'.
                 'Tél : '.$_POST['tel'].' <br>'.
                'E-mail : '.$_POST['email'].' <br>'.
                'adresse : '.$_POST['adresse'].' <br>'.              
                'ville : '.$nom_ville.' <br>'.
                'code: '.$_POST['code'].' <br>'.'</div>';

             
                mail('support@powercoursier.ma', $objet, $message, $headers);
                 
                  $_SESSION['email'] = true;
               }
              
               header('Location:'. $_SERVER["HTTP_REFERER"]);

             

 
?>