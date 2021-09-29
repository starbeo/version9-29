<?php
           session_start();

            

         	       
            if(isset($_POST['email']) && $_POST['email']!='' ){

              
              $destinataire = $_POST['email'];
              $name=$_POST['nom'];
	           $expediteur = 'contact@powercoursier.ma'; 
             
              
              $objet = 'Avis Client- '.$name;
              $objet .= ' -Power Coursier';
              $headers  = 'MIME-Version: 1.0' . "\n"; // Version MIME
              $headers .= "Content-Type: text/html; charset=UTF-8\n"; // l'en-tete Content-type pour le format HTML
              $headers .= 'Reply-To: '.$expediteur."\n"; // Mail de reponse
              $headers .= 'From: "'.$destinataire."\n"; // Expediteur
              $headers .= 'Delivered-to: '.$destinataire."\n"; // Destinataire    
              $message = '<div style="width: 100%; text-align: center; font-weight: bold"> 
                 اسم شركتكم : '.$_POST['nom'].' <br>'.
                 'البريد الإلكتروني : '.$_POST['email'].' <br>'.

                 'خدمة التوصيل : '.$_POST['radio1'].' <br>'.
                 'خدمة الزبناء : '.$_POST['radio2'].' <br>'.
                 'سلوك ومهارات الموظفين : '.$_POST['radio3'].' <br>'.
                 'ما رأيك في وقت التسليم؟ : '.$_POST['radio4'].' <br>'.

                 ' منذ متى وأنت تستخدم خدمتنا : '.$_POST['optradio'].' <br>'.
                 'ما هي المدن التي تودون إدراجها ؟ : '.$_POST['ville'].' <br>'.
         
                
                ' ما هي اقتراحاتكم ؟ : '.$_POST['message'].' <br>'.'</div>';

             
                mail('contact@powercoursier.ma', $objet, $message, $headers);
                 
                  $_SESSION['email'] = true;
               }
              
               header('Location:'. $_SERVER["HTTP_REFERER"]);

             

 
?>