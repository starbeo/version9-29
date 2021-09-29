<?php
            session_start();

            if( isset( $_SESSION['email'])){
              echo "<section style='position:relative;width: 100%;z-index:999999999;background: black;color: white;text-align: center;font-size: 20px;padding:7px 0px'>  Nous vous remercions d'avoir renseigné ce formulaire, votre demande sera traitée dans les plus brefs délais.</section>";
              session_destroy();
            }

            $ch = curl_init('https://mycolis.net/middleware/prod/transformation/digitale/api/app/myColis/get/client/Colis/getTousLesVilles.php?tocken=$2a$08$Az.b2.8Vkpw9XwtlpYRSseU');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            $data=json_decode($response,true);

            
           
            ?>



<!DOCTYPE html>
<html>
    

<head>
          <title>SEM Power Coursier </title>
        <meta name="description" content="SEM Power Coursier ">
        <meta name="author" content="SEM Power Coursier ">
        <meta name="keywords" content="SEM Power Coursier">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <!-- Stylesheets -->
        <link rel="stylesheet" href="css/bootstrap.css"/><!-- bootstrap grid -->
        <link rel="stylesheet" href="css/style.css"/><!-- template styles -->
        <link rel="stylesheet" href="css/color-default.css"/><!-- template main color -->
        <link rel="stylesheet" href="css/retina.css"/><!-- retina ready styles -->
        <link rel="stylesheet" href="css/responsive.css"/><!-- responsive styles -->
        <link rel="icon" href="img/fav.png" type="image/x-icon">

        <!-- Google Web fonts -->
        <link href='https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600' rel='stylesheet' type='text/css'>

        <!-- Font icons -->
        <link rel="stylesheet" href="icon-fonts/font-awesome-4.3.0/css/font-awesome.min.css"/><!-- Fontawesome icons css -->
        
        <link rel="stylesheet" href="style-switcher/styleSwitcher.css"/><!-- styleswitcher -->
    </head>

    <body>
        
      <?php   require('menu.php') ?>

      
      <style type="text/css">
        .parallax02{
            background-image: url('new_img/header_page/contact.jpg');
            height: 380px;
            background-size: cover;
            background-position: center center;
            margin-bottom: 35px;
           }
           .parallax02 h1{text-shadow: 1px 2px 2px black}
      </style>



       
        <!-- .page-title start -->
        <div class="page-title-style01 page-title-negative-top parallax02">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <br><br>
                        <h1 >Contactez-nous</h1>
                    </div><!-- .col-md-12 end -->
                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-title-style01.page-title-negative-top end -->


     <style type="text/css">
         .new_contact h2, .new_contact h4{text-transform: uppercase;}
         .new_contact h4{margin-bottom: 10px}
         .new_contact p{font-size: 15px;line-height: 25px}

         .new_contact .blox-icon{
            background: #b9191b;padding: 8px;max-width: 55px
         }
        
          @media screen and (max-width: 900px) {
         .new_contact{text-align: center;}
            .blox-icon{margin-left: 43% !important;margin-bottom: 20px}
            hr{display: none;}
         }

     
     </style>

        <article class="new_contact">
            <div class="container">
                <div class="row">
                    <h2><strong >Vous êtes </strong> particulier ou professionnel </h2>
                    <hr style="background: #b9191b;height: 2px;width:60px;margin:5px 0px;margin-bottom: 30px">

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-2 ">
                                <div class="blox-icon" >
                                  <img src="new_img/phone.png" >
                                </div>
                            </div>
                            <div class="col-md-10">
                            <h4> Par Téléphone </h4>
                            <p> Vous pouvez joindre le <strong>Centre de Ralation Clients </strong> du lundi au vendredi de 9:00h à 13:00h & 14:00h à 18:00h <br> Samedi: 9:00h à 13:00h ( heure Maroc )</p>
                            <h4> +212 5 22 71 87 53 </h4>   
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                         <div class="row">
                            <div class="col-md-2" >
                                <div class="blox-icon">
                                  <img src="new_img/business.png" >
                                </div>
                            </div>
                            <div class="col-md-10">
                            <h4>pour nous écrire </h4>
                             <p> Un renseignement , une suggestion , une réclamation , envoyez-nous votre Message .</p>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </article>




         <style type="text/css">
             .info-contact p{font-size: 15px;font-weight: 600;}
         </style>
         <div class="page-content" style="display: none;">
            <div class="container">
                <div class="row">
                    <div class="custom-heading02">
                        <h2>Contacter Nous</h2>
                        <p>POWER COURSIER EST UNE SOCIÉTÉ DE LIVRAISON EXPRESS À CASABLANCA</p>
                    </div>
                </div><!-- .row end -->

                <div class="row info-contact">
                    <div class="col-md-3 col-sm-3">
                        <div class="service-icon-center">
                            <div class="icon-container">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                            </div>

                            <h4>Adresse</h4>
                            <p>
                                N°56, Rue Louis Ferré,Hay Ain Borja, Assoukhour Assawda CASABLANCA 
                            </p>
                        </div><!-- .service-icon-center end -->
                    </div><!-- .col-md-3 end -->

                    <div class="col-md-3 col-sm-3">
                        <div class="service-icon-center">
                            <div class="icon-container">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                            </div>

                            <h4>Téléphone</h4>

                            <p>
                                +2125 22 71 87 53 <br>
                                +2125 22 72 17 50 <br>
                                +2128 08 50 43 73 
                            </p>
                        </div><!-- .service-icon-center end -->
                    </div><!-- .col-md-3 end -->

                    <div class="col-md-3 col-sm-3">
                        <div class="service-icon-center">
                            <div class="icon-container">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </div>

                            <h4>Ouverture</h4>

                            <p>
                                <strong >Lundi-Vendredi:</strong>
                                9h à 13h & 14h à 18h
                                 <strong>Samedi:</strong>
                                9h à 13h  
                            </p>
                        </div><!-- .service-icon-center end -->
                    </div><!-- .col-md-3 end -->


                     <div class="col-md-3 col-sm-3">
                        <div class="service-icon-center">
                            <div class="icon-container">
                               <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            </div>

                            <h4>E-mail</h4>

                            <p>
                                Contact@Powercoursier.ma 
                            </p>
                        </div><!-- .service-icon-center end -->
                    </div><!-- .col-md-3 end -->

 
                   
                </div><!-- .row end -->

            </div><!-- .container end -->
        </div><!-- .page-content end -->







        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="custom-heading">
                            <h3> écrivez nous</h3>
                        </div><!-- .custom-heading.left end -->

                        <p>
                       POWER COURSIER EST UNE SOCIÉTÉ DE LIVRAISON EXPRESS À CASABLANCA
                        </p>

                        <br />

                        <!-- .contact form start -->
                        <form class="wpcf7 clearfix"   action="send_mail/send_contact.php" method="POST" >


                            <fieldset>
                                <label>
                                    <span class="required">*</span> Nom complet:
                                </label>

                                <input type="text" class="wpcf7-text" name="name" required>
                            </fieldset>

                            <fieldset>
                                <label>
                                    <span class="required">*</span> Email:
                                </label>

                                <input type="email" class="wpcf7-text" name="email"  required>
                            </fieldset>


                            
                            <fieldset>
                                <label>
                                    <span class="required">*</span> Ville
                                </label>

                                  
                                  <select class="wpcf7-form-control-wrap wpcf7-select" name="ville">
                                   <?php 
                                        foreach ($data['records'] as $ville) {
                                        echo "<option value='".$ville['name']."'> ".$ville['name']." </option>";
                                      }
                                   ?>
                                   </select>
                            </fieldset>



                            <fieldset>
                                <label>
                                    <span class="required">*</span> Objet :
                                </label>

                                <input type="text" class="wpcf7-text" name="objet" required>
                            </fieldset>

                            

                            <fieldset>
                                <label>
                                    <span class="required">*</span> Message:
                                </label>

                                <textarea rows="8" class="wpcf7-textarea" name="message" required></textarea>
                            </fieldset>

                            <input type="submit" class="wpcf7-submit" value="Envoyer" />
                        </form><!-- .wpcf7 end -->
                    </div><!-- .col-md-6 end -->

                    <div class="col-md-6">
                        <div class="custom-heading">
                            <h3>Adresse</h3>
                        </div><!-- .custom-heading end -->




                        <iframe style="width: 100%;height: 630px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=Power%20Coursier%2056%20Rue%20Louis%20Ferr%C3%A9%2C%20Casablanca%2020320&amp;t=m&amp;z=15&amp;output=embed&amp;iwloc=near" aria-label="Power Coursier 56 Rue Louis Ferré, Casablanca 20320"></iframe>




                    </div><!-- .col-md-6 end -->
                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-content end -->

        <?php   require('footer.php') ?>


        <script src="js/jquery-2.1.4.min.js"></script><!-- jQuery library -->
        <script src="js/bootstrap.min.js"></script><!-- .bootstrap script -->
        <script src="js/jquery.srcipts.min.js"></script><!-- modernizr, retina, stellar for parallax -->  
        <script src="js/jquery.dlmenu.min.js"></script><!-- for responsive menu -->
        <script src="style-switcher/styleSwitcher.js"></script><!-- styleswitcher script -->
        <script src="js/include.js"></script><!-- custom js functions -->


           
        <script>
           
                function validateForm(){
                   
                    var name = $('#contact-name').val();
                    var email = $('#contact-email').val();
                    var ville = $('#contact-ville option:selected').text();
                    var objet = $('#contact-objet').val();
                    var message = $('#contact-message').val();


                    var form_data = new Array({'name': name, 'email': email, 'ville': ville, 'email': email, 'objet': objet, 'message': message});

                    $.ajax({
                        type: 'POST',
                        url: "send_mail/send_contact.php",
                        data: ({'action': 'contact', 'form_data': form_data})
                    }).done(function (data) {

                        if(data=="true"){
                            alert(" Nous vous remercions d'avoir renseigné ce formulaire, votre demande sera traitée dans les plus brefs délais.");
                        }else{
                            alert("problème");
                        }
                    });

                     return false;

                }; 
        </script>
        <script type="text/javascript">
            $(".navbar-default .navbar-nav>li:nth-of-type(6)").addClass("current-menu-item")
        </script>
        <!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
 fbq('init', '4448583728516480'); 
fbq('track', 'PageView');
</script>
<noscript>
 <img height="1" width="1" 
src="https://www.facebook.com/tr?id=4448583728516480&ev=PageView
&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->







    </body>


</html>
