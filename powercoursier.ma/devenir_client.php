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
            background-image: url('new_img/header_page/devenir_client.jpg');
            height: 380px;
            background-size: cover;
            background-position: center center;
            margin-bottom: 35px;
           }
           .parallax02 h1{text-shadow: 1px 2px 2px black}
      </style>

       
        <!-- .page-title start -->
        <div class="page-title-style01 page-title-negative-top parallax02" >
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1 style="line-height: 50px;">
                          Vous souhaitez dynamiser votre commerce ? <br>Vous êtes e-commerçant ?
                        </h1>
                    </div><!-- .col-md-12 end -->
                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-title-style01.page-title-negative-top end -->


    





         <style type="text/css">
             label,span{font-weight: bolder;}
         </style>
        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="custom-heading">
                            <h3 style="margin-bottom: 0px"> formulaire d'inscription</h3>
                             <p>
                              Remplissez le formulaire ci-dessous et un conseiller clientèle vous rappellera dans les 48h pour prendre RDV.  </p>
                        </div><!-- .custom-heading.left end -->

                       

                        <!-- .contact form start -->
                        <form class="wpcf7 clearfix"  action="send_mail/send_devenir_client.php" method="post" >
                          

                            <fieldset>
                                <label>
                                    <span class="required">*</span>Personne à contacter:
                                </label>
                                <input type="text" class="wpcf7-text" name="personne" placeholder="Personne à contacter" required>
                            </fieldset>

                            <fieldset class="row">
                                <div class="col-md-6">
                                    <label>
                                    <span class="required">*</span> Téléphone 
                                </label>
                                <input type="text" class="wpcf7-text" name="tel" placeholder="Téléphone" required>
                                </div>

                                 <div class="col-md-6">
                                  <label><span class="required">*</span> Email </label>
                                  <input type="email" class="wpcf7-text" name="email" placeholder="Email" required>
                                 </div>
                            </fieldset>


                             <fieldset class="row">
                                <div class="col-md-6">
                                    <label>
                                    <span class="required">*</span> Adresse 
                                </label>
                                <input type="text" class="wpcf7-text" name="adresse" placeholder="Adresse">
                                </div>

                                 <div class="col-md-6">
                                  <label><span class="required">*</span> Ville </label>
                                  
                                  <select class="wpcf7-form-control-wrap wpcf7-select" name="ville">
                                   <?php 
                                        foreach ($data['records'] as $ville) {
                                        echo "<option value='".$ville['ville_id'].";".$ville['name']."'> ".$ville['name']." </option>";
                                      }
                                   ?>
                                   </select>


                                 </div>
                            </fieldset>



                              



                              
                           

                            <input type="submit" class="wpcf7-submit" value="Envoyer" />
                        </form><!-- .wpcf7 end -->
                    </div><!-- .col-md-6 end -->

                   
                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-content end -->





         <style type="text/css">
             .info-contact p{font-size: 15px;font-weight: 600;}
         </style>
         <div class="page-content ">
            <div class="container">
                <div class="row">
                    <div class="custom-heading02">
                        <h2>Contactez-nous</h2>
                        <br>
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






         <style type="text/css">

        @media (min-width: 900px) {
            .info-footer{height: 280px
          }
        }
          .info-footer{
            background:#ba1818;text-align: center;padding-top: 30px;
          }
          .info-footer h2{color:white;}
          .info-footer img{max-width: 150px;margin:auto;}
      </style>
       <section class="info-footer">
        <div class="container">
           <div class="row">
               <div class="col-lg-3">
                   <img src="new_img/devenir-client/1.png">
                   <h2>Ramassage à Domicile</h2>
               </div>
               <div class="col-lg-3">
                    <img src="new_img/devenir-client/2.png">
                    <h2>Plus de 55 Villes</h2>
               </div>
               <div class="col-lg-3">
                    <img src="new_img/devenir-client/3.png">
                    <h2>Retour de fond chaque 48h</h2>
               </div>
               <div class="col-lg-3">
                    <img src="new_img/devenir-client/4.png">
                    <h2>Retour Gratuit</h2>
               </div>

           </div>
         </div>
       </section>

        <?php   require('footer.php') ?>


        <script src="js/jquery-2.1.4.min.js"></script><!-- jQuery library -->
        <script src="js/bootstrap.min.js"></script><!-- .bootstrap script -->
        <script src="js/jquery.srcipts.min.js"></script><!-- modernizr, retina, stellar for parallax -->  
        <script src="js/jquery.dlmenu.min.js"></script><!-- for responsive menu -->
        <script src="style-switcher/styleSwitcher.js"></script><!-- styleswitcher script -->
        <script src="js/include.js"></script><!-- custom js functions -->


           
        
        <script type="text/javascript">
            $(".navbar-default .navbar-nav>li:nth-of-type(5)").addClass("current-menu-item")
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
