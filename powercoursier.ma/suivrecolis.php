


<!DOCTYPE html>
<html>


<?php


if(isset($_GET['code_colis']) && $_GET['code_colis']!="" ){

     $ch = curl_init('https://mycolis.net/middleware/prod/transformation/digitale/api/app/myColis/get/client/Status/getStatusColis.php?tocken=$2a$08$Az.b2.8Vkpw9XwtlpYRSseU&code_envoi='.$_GET['code_colis']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            $data=json_decode($response,true);
          
       if(isset($data["message"])){
          if($data["message"]=="Colis n'existe pas"){
            echo "<section style='position: absolute;width: 100%;height: 35px;z-index:999999999;background: black;color: white;text-align: center;font-size: 20px;padding-top:7px'> Colis n'existe pas</section>";
             echo "<style type='text/css'>#block_table{display: none !important;} </style>";

          }
      }


}else{
    echo "<style type='text/css'>#block_table{display: none !important;} </style>";
}

?>
     

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
        <link href='http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600' rel='stylesheet' type='text/css'>

        <!-- Font icons -->
        <link rel="stylesheet" href="icon-fonts/font-awesome-4.3.0/css/font-awesome.min.css"/><!-- Fontawesome icons css -->
        
        <link rel="stylesheet" href="style-switcher/styleSwitcher.css"/><!-- styleswitcher -->
    </head>

    <body>
        
      <?php   require('menu.php') ?>



    <style type="text/css">



      .page-title-style01  h1{line-height: 50px;text-shadow: 2px 2px 1px black}
      .page-title-negative-top{
        background-image: url('new_img/coli.jpg');background-position:0px -170px;
        margin-bottom: 35px;background-size: cover !important;height: 500px;
      }
     

    @media (min-width: 900px){
      .block_form{text-align: center;float: left;margin-left: 25%}
      .block_form input{display: inline-block !important;}
      .wpcf7-text{width: 400px;margin-right: 30px}
    }

      @media (max-width: 900px){
          .page-title-style01 h1{font-size: 20px;line-height: 30px}
          .wpcf7-text{width:100%;margin-right: 10px}
          .wpcf7-submit{width: 100%;margin-top: 15px}
          .page-title-negative-top{
            margin-bottom: 10px;padding-top:30px;padding-bottom: 0px;height:350px;background-position: 0px 0px
          }
          .parallax02{padding-bottom: 40px;margin-bottom:30px}

      }

        .parallax02{
            background-image: url('new_img/header_page/suivrecolis.jpg');
            height: 500px;
            background-size: cover;
            background-position: center center;
           }
           .parallax02 h1{text-shadow: 2px 3px 3px black;}

          
      </style>

       
        <!-- .page-title start -->
        <div class="page-title-style01 page-title-negative-top  parallax02" style="position: relative;">
            <div class="container">
                <div>
                    <div class="col-md-12">
                        <h1>Découvrez le statut de votre colis à l'instant ! <br> Pour en savoir plus sur chaque étape de votre livraison , vous n'avez qu'à entrer le numéro du colis.</h1>


                          <form class="wpcf7 clearfix block_form" action="#" method="get" >
                            
                                <input  type="text" class="wpcf7-text" name="code_colis" placeholder="Votre numéro de colis" required>
                                <input type="submit" class="wpcf7-submit" value="SUIVRE" />
                                                           
                        </form><!-- .wpcf7 end -->


                    </div><!-- .col-md-12 end -->
                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-title-style01.page-title-negative-top end -->


    


        

       <style type="text/css">
            
            td{vertical-align:middle !important;font-weight: bolder;color:black;font-size: 15px !important}
            thead th{color: white !important;font-size: 15px}

        </style>
      
         <div class="page-content" id="block_table">
            <div class="container">
                <div class="row">
                    <div class="custom-heading02">
                        <h2>Suivre votre colis</h2>
                        <br>
                    </div><!-- .custom-heading02 end -->
                </div><!-- .row end -->

                <div class="row tarif">
                    <div class="col-md-12 clearfix">

                         <table class="table table-striped" style="border: 1px solid #9E9E9E;padding: 10px;margin:auto;">
                          <thead>
                            <tr style="background:#b9191b;">
                              <th >Statut</th>
                              <th >Emplacement</th>
                              <th >Date_Création</th>
                            </tr>
                          </thead>
                          <tbody>

                          <?php
                             if(isset($data['records'])){
                              foreach ($data['records'] as  $ligne) {
                                echo "<tr>
                                       <td>".$ligne['Statut']."</td>
                                       <td class='ville'>".$ligne['Emplacement']."</td>
                                       <td>".$ligne['Date_Création']."</td>
                                      </tr>";
                              }
                          }
                          ?>
                            
                          </tbody>
                        </table>

                        
                    </div><!-- .col-md-12 end -->
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






        <?php   require('footer.php') ?>


        <script src="js/jquery-2.1.4.min.js"></script><!-- jQuery library -->
        <script src="js/bootstrap.min.js"></script><!-- .bootstrap script -->
        <script src="js/jquery.srcipts.min.js"></script><!-- modernizr, retina, stellar for parallax -->  
        <script src="js/jquery.dlmenu.min.js"></script><!-- for responsive menu -->
        <script src="style-switcher/styleSwitcher.js"></script><!-- styleswitcher script -->
        <script src="js/include.js"></script><!-- custom js functions -->


           
     
        <script type="text/javascript">
            $(".navbar-default .navbar-nav>li:nth-of-type(8)").addClass("current-menu-item")
        </script>







    </body>


</html>
