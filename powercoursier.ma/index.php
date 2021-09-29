<!DOCTYPE html>
<?php session_start() ?>
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
        <link rel="stylesheet" href="css/animate.css"/><!-- used for animations -->
        <link rel='stylesheet' href='owl-carousel/owl.carousel.css'/><!-- Client carousel -->
        <link rel="stylesheet" href="masterslider/style/masterslider.css" /><!-- Master slider css -->
        <link rel="stylesheet" href="masterslider/skins/default/style.css" /><!-- Master slider default skin -->
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
        <!-- .master-slider start -->


       
        <style type="text/css">
            #masterslider h2, #masterslider p {
                text-shadow: 1px 2px 2px black;
            }
            .master-slider{margin: 0px !important}
            #masterslider h2{font-size: 6em;font-weight: bold;}
              
           @media screen and (max-width: 900px) {
             #masterslider h2{font-size: 6em}
             #masterslider p{font-size: 4em;margin-top: 50px;line-height: 1.5em}
            #masterslider .btn-big{font-size: 8;padding: 6px 30px}
        }
        </style>
        <div id="masterslider" class="master-slider ms-skin-default">
            <div class="ms-slide">
                <!-- slide background -->
                <img src="masterslider/blank.gif" data-src="new_img/slider/1.jpg" style="filter: brightness(0.65);-webkit-filter: brightness(0.65);"  /> 
                <h2 class="ms-layer pi-caption01" 
                    style="left: 258px; top: 420px;" 
                    data-type="text" 
                    data-effect="top(short)" 
                    data-duration="300"
                    data-hide-effect="fade" 
                    data-delay="0" 
                    >
                    Bienvenue chez SEM
                </h2>
                <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt=""
                     style="left: 540px; top: 480px;"
                     data-type="image" 
                     data-effect="bottom(short)" 
                     data-duration="300"
                     data-hide-effect="fade" 
                     data-delay="300" 
                     />
                <p class="ms-layer pi-text"
                   style="left: 278px; top: 500px;"
                   data-type="text" 
                   data-effect="top(short)" 
                   data-duration="300"
                   data-hide-effect="fade" 
                   data-delay="600"      
                   >
                    LEADER DE LIVRAISON E-COMMERCE AU MAROC
                     <br><br><br>
                     <a href="devenir_client.php" class="btn btn-big" style="float:left;"><span>Devenir Client</span></a>
                </p>
            </div><!-- .ms-slide end -->




               <div class="ms-slide">
                <!-- slide background -->
                <img src="masterslider/blank.gif" data-src="new_img/slider/2.jpg" style="filter: brightness(0.65);-webkit-filter: brightness(0.65);" /> 
                <h2 class="ms-layer pi-caption01" 
                    style="left: 258px; top: 330px;font-size: 7em" 
                    data-type="text" 
                    data-effect="top(short)" 
                    data-duration="300"
                    data-hide-effect="fade" 
                    data-delay="0" 
                    >
                    E-commerce
                </h2>
               
                <p class="ms-layer pi-text"
                   style="left: 278px; top: 410px;"
                   data-type="text" 
                   data-effect="top(short)" 
                   data-duration="300"
                   data-hide-effect="fade" 
                   data-delay="600"      
                   >
                    Voulez-vous envoyer vos commandes en toute sécurité?
                     <br><br><br>
                     <a href="service1.php" class="btn btn-big" style="float:left;"><span>En savoir plus</span></a>
                </p>
            </div><!-- .ms-slide end -->






            <div class="ms-slide">
                <!-- slide background -->
                <img src="masterslider/blank.gif" data-src="new_img/slider/3.jpg"  style="filter: brightness(0.65);-webkit-filter: brightness(0.65);" /> 
                <h2 class="ms-layer pi-caption01" 
                    style="left: 258px; top: 420px;font-size: 7em" 
                    data-type="text" 
                    data-effect="top(short)" 
                    data-duration="300"
                    data-hide-effect="fade" 
                    data-delay="0" 
                    >
                    Stockage
                </h2>
                <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt=""
                     style="left: 540px; top: 480px;"
                     data-type="image" 
                     data-effect="bottom(short)" 
                     data-duration="300"
                     data-hide-effect="fade" 
                     data-delay="300" 
                     />
                <p class="ms-layer pi-text"
                   style="left: 278px; top: 500px;"
                   data-type="text" 
                   data-effect="top(short)" 
                   data-duration="300"
                   data-hide-effect="fade" 
                   data-delay="600"      
                   >
                    Ce service de manutention compte vous permet de livrer à vos clients par envoi
                    express !
                     <br><br><br>
                     <a href="service3.php" class="btn btn-big" style="float:left;"><span>En savoir plus</span></a>
                </p>
            </div><!-- .ms-slide end -->




             <div class="ms-slide">
                <!-- slide background -->
                <img src="masterslider/blank.gif" data-src="new_img/slider/4.jpg"   style="filter: brightness(0.65);-webkit-filter: brightness(0.65);"/> 
                <h2 class="ms-layer pi-caption01" 
                    style="left: 258px; top: 420px;font-size: 7em" 
                    data-type="text" 
                    data-effect="top(short)" 
                    data-duration="300"
                    data-hide-effect="fade" 
                    data-delay="0" 
                    >
                    course administrative
                </h2>
                <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt=""
                     style="left: 540px; top: 480px;"
                     data-type="image" 
                     data-effect="bottom(short)" 
                     data-duration="300"
                     data-hide-effect="fade" 
                     data-delay="300" 
                     />
                <p class="ms-layer pi-text"
                   style="left: 278px; top: 500px;"
                   data-type="text" 
                   data-effect="top(short)" 
                   data-duration="300"
                   data-hide-effect="fade" 
                   data-delay="600"      
                   >
                    Vous êtes professionnel ou particulier et vous avez besoin d'acheminer un document en urgence ?
                     <br><br><br>
                     <a href="service4.php" class="btn btn-big" style="float:left;"><span>En savoir plus</span></a>
                </p>
            </div><!-- .ms-slide end -->


            <div class="ms-slide">
                <!-- slide background -->
                <img src="masterslider/blank.gif" data-src="new_img/slider/5.jpg"   style="filter: brightness(0.65);-webkit-filter: brightness(0.65);"/>
                <h2 class="ms-layer pi-caption01"
                    style="left: 258px; top: 420px;font-size: 7em"
                    data-type="text"
                    data-effect="top(short)"
                    data-duration="300"
                    data-hide-effect="fade"
                    data-delay="0"
                >
                    Couverture nationale
                </h2>
                <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt=""
                     style="left: 540px; top: 480px;"
                     data-type="image"
                     data-effect="bottom(short)"
                     data-duration="300"
                     data-hide-effect="fade"
                     data-delay="300"
                />
                <p class="ms-layer pi-text"
                   style="left: 278px; top: 500px;"
                   data-type="text"
                   data-effect="top(short)"
                   data-duration="300"
                   data-hide-effect="fade"
                   data-delay="600"
                >
                    Nous livrons à plus que 200 villes au Maroc.
                    <br><br><br>
                    <!--<a href="service4.php" class="btn btn-big" style="float:left;"><span>En savoir plus</span></a>-->
                </p>
            </div><!-- .ms-slide end -->

        

           
        </div><!-- #masterslider end -->

        <div class="page-content parallax parallax01 dark" style="padding-top: 20px;padding-bottom: 5px">
            <div class="container">
                <div class="row" style="margin-bottom: 0px">
                    <div class="col-md-12">
                        <div class="call-to-action clearfix">
                            <div class="text">
                                <h2 style="line-height: 40px;padding-top: 7px;font-size:20px">Parce que vos exigences sont les nôtres, nous vous offrons la solution la plus adaptée pour toutes vos expéditions.</h2>
                            </div><!-- .text end -->

                            <a href="devenir_client.php" class="btn btn-big">
                                <span>Devenir Client</span>
                            </a>
                        </div><!-- .call-to-action end -->
                    </div><!-- .col-md-12 end -->
                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-content.parallax end -->


         
         <style type="text/css">
           .block_bienvenue p{font-size: 17px !important;line-height: 25px}
           .block_bienvenue li{font-size: 17px !important}
         </style>

          <div class="page-content parallax block_bienvenue " style="padding-top: 30px;padding-bottom: 20px">
            <div class="container">
                <div class="row" style="margin-bottom: 0px">
                    <div class="col-md-8">
                            <div class="text">
                                 <h3 style="margin-bottom:12px">Bienvenue chez SEM</h3>
                                <p>
                                    Service Express Maroc est une société qui dispose d’une large expérience dans le domaine de la
                                    livraison E-Commerce dans le Maroc.<br>
                                    Nous sommes le leader au niveau national, et on s’engage à offrir le meilleur service de ramassage, livraison des clients, et d’assurer le contre remboursement dans les délais accordés.<br>
                                   Le succès de SEM repose sur : 
                                 </p>
                                   <ul>
                                     <li>Rapidité </li>
                                     <li>Confiance</li>
                                     <li>Fiabilité</li>
                                     <li>Innovation</li>
                                   </ul>
                                   <p style="padding-top: 5px">
                                   Pour toutes ces raison vous pouvez nous confier vos expéditions avec la certitude qu'elles seront exécutées professionnellement.
                                   
                                </p> 
                                <p >
                                   SEM s'engage à offrir le meilleur service de ramassage et livraison à ses clients, et d'assurer le contre remboursement dans les délais accordés.  
                                </p> 
                            </div><!-- .text end -->                           
                    </div><!-- .col-md-12 end -->

                     <div class="col-md-4">
                            <div style="margin-top: 35px">
                              <img src="new_img/accueil-block3.jpg">                             
                            </div><!-- .text end -->                           
                    </div><!-- .col-md-12 end -->



                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-content.parallax end -->











     <style type="text/css">

        
          .info-footer{
            background:#ba1818;text-align: center;padding-top: 30px;
          }
          .info-footer .row{margin-bottom:40px !important}
          .info-footer h2{color:white;font-size: 18px;margin-bottom: 10px}
          .info-footer img{max-width: 105px;margin:auto;}
          .info-footer a{color: #242424;font-weight:bolder;text-transform:uppercase;cursor: pointer;font-size: 12px}

          @media (max-width: 900px) {
            .info-footer img{padding-top: 30px !important}
        }
      </style>
       <section class="info-footer" >
        <div class="container">
           <div class="row">
               <h2 style="color:white;">Nous Donnons toute la priorité à la qualité du service clients</h2>
               <hr style="width: 70px;border:1px solid white">
               <div class="col-lg-3">
                   <img src="new_img/about/1.png">
                   <h2>Confirmation</h2>
                   <a id="myBtn1">en savoir plus</a>
               </div>
               <div class="col-lg-3">
                    <img src="new_img/track/5.png" style="padding-left: 10px;padding-bottom: 10px">
                    <h2>Traçabilité</h2>
                    <a id="myBtn2">en savoir plus</a>
               </div>
               <div class="col-lg-3">
                    <img src="new_img/about/3.png">
                    <h2>Relance</h2>
                    <a id="myBtn3">en savoir plus</a>
               </div>
               <div class="col-lg-3">
                    <img src="new_img/about/4.png">
                    <h2>Acheminement 24/24</h2>
                    <a id="myBtn4">en savoir plus</a>
               </div>

           </div>
         </div>
       </section>
         


           


<style>

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

#text{
padding: 50px;line-height: 40px
}
@media screen and (max-width: 900px) {
             #text{font-size: 16px;padding: 20px}
        }
</style>





<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3 id="text" >text</h3>
  </div>

</div>

<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn1 = document.getElementById("myBtn1");
var btn2 = document.getElementById("myBtn2");
var btn3 = document.getElementById("myBtn3");
var btn4 = document.getElementById("myBtn4");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn1.onclick = function() {
  modal.style.display = "block";
  $("#text").html("<h2>Confirmation</h2><hr style='width:70px;border:1px solid #b9191b;margin:0px;margin-bottom: 10px !important;'> Grâce au service de Notification SMS, le destinataire de votre colis est informé par SMS directement sur son mobile de l’arrivée du colis et de son lieu de réception.");
}
btn2.onclick = function() {
  modal.style.display = "block";
   $("#text").html("<h2>Traçabilité</h2><hr style='width:70px;border:1px solid #b9191b;margin:0px;margin-bottom: 10px !important;'> Grâce au nouveau service de Traçabilité, vous avez désormais la possibilité de suivre la progression du cheminement de vos colis et contre remboursement, en toute transparence !");
}
btn3.onclick = function() {
  modal.style.display = "block";
   $("#text").html("<h2>Relance</h2><hr style='width:70px;border:1px solid #b9191b;margin:0px;margin-bottom: 10px !important;'> Grâce au service de relance, Power coursier met votre disposition un Centre de Relation Client qui relance chaque jour vos destinataires  afin de garantir un meilleur taux de livraison et une meilleure satisfaction clients");
}
btn4.onclick = function() {
  modal.style.display = "block";
   $("#text").html("<h2>ACHEMINEMENT 24/24</h2><hr style='width:70px;border:1px solid #b9191b;margin:0px;margin-bottom: 10px !important;'> Quelle que soit la nature et la destination de votre colis, nos agents veillent à vous assister dans chaque étape de votre expédition : depuis notre Guichet Rapide jusqu'au SMS de confirmation de réception, nous vous tenons informés tout au long de l’acheminement de votre expédition jusqu’à sa bonne réception par votre Destinataire.");
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }

  
}
</script>





    

         
   
        <?php   require('footer.php') ?>
        
        <script src="js/jquery-2.1.4.min.js"></script><!-- jQuery library -->
        <script src="js/bootstrap.min.js"></script><!-- .bootstrap script -->
        <script src="js/jquery.srcipts.min.js"></script><!-- modernizr, retina, stellar for parallax -->  
        <script src="owl-carousel/owl.carousel.min.js"></script><!-- Carousels script -->
        <script src="masterslider/masterslider.min.js"></script><!-- Master slider main js -->
        <script src="js/jquery.dlmenu.min.js"></script><!-- for responsive menu -->
        <script src="style-switcher/styleSwitcher.js"></script><!-- styleswitcher script -->
        <script src="js/include.js"></script><!-- custom js functions -->

        <script>
            /* <![CDATA[ */
            jQuery(document).ready(function ($) {
                'use strict';

                var width=$(window).width();
                var height_slider=900;
                if(width<900){ height_slider=1200;}
                // MASTER SLIDER START
                var slider = new MasterSlider();
                slider.setup('masterslider', {
                    width: 1920, // slider standard width
                    height: height_slider, // slider standard height
                    space: 0,
                    layout: "fullwidth",
                    speed: 50,
                    centerControls: false,
                    loop: true,
                    autoplay: true,
                    parallaxMode: "mouse"
                            // more slider options goes here...
                            // check slider options section in documentation for more options.
                });
                // adds Arrows navigation control to the slider.
                slider.control('arrows');
                MSScrollParallax.setup(slider, 50, 80, true);

                
                
            });
            /* ]]> */
        </script>

        <script type="text/javascript">
            $(".navbar-default .navbar-nav>li:nth-of-type(1)").addClass("current-menu-item")
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
