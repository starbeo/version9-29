<?php
            session_start();

            if( isset( $_SESSION['email'])){
             echo "<section style='position:relative;width: 100%;z-index:999999999;background: black;color: white;text-align: center;font-size: 20px;padding:7px 0px'>  Nous vous remercions d'avoir renseigné ce formulaire, votre demande sera traitée dans les plus brefs délais.</section>";
              session_destroy();
            }
            
           
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





       
        <!-- .page-title start -->
        <div class="page-title-style01 page-title-negative-top " style="background-image: url('new_img/avis.jpg');background-position:center center;margin-bottom: 35px">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1 style="line-height: 50px;text-shadow: 2px 2px 2px black">
                          Votre avis nous intéresse
                        </h1>
                    </div><!-- .col-md-12 end -->
                </div><!-- .row end -->
            </div><!-- .container end -->
        </div><!-- .page-title-style01.page-title-negative-top end -->


    





         <style type="text/css">
             label,span{font-weight: bolder;font-size: 18px}
             th,td{font-size: 17px;}

             input[type=radio] { width: 20px;height: 20px;}

         </style>
        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="custom-heading">
                            <h3 style="margin-bottom: 0px"> DONNEZ VOTRE AVIS!</h3>
                             <p>
                              Nous vous prions de bien vouloir nous faire part de vos commentaires sur votre expérience en ligne afin de nous aider à améliorer nos services à l’avenir.  </p>
                        </div><!-- .custom-heading.left end -->

                       

                        <!-- .contact form start -->
                        <form class="wpcf7 clearfix" action="send_mail/send_avis.php" method="post">
                          

                            <fieldset>
                                <label>
                                    <span class="required">*</span>اسم شركتكم
                                </label>
                                <input required type="text" class="wpcf7-text" name="nom"  placeholder="اسم شركتكم">
                            </fieldset>

                             <fieldset>
                                <label>
                                    <span class="required">*</span> البريد الإلكتروني
                                </label>
                                <input required type="email" class="wpcf7-text" name="email" placeholder="البريد الإلكتروني">
                            </fieldset>
                            
                            <table class="table">
                              <thead>
                                <tr>
                                  <th scope="col"></th>
                                  <th scope="col"> جيدة جدا </th>
                                  <th scope="col"> جيدة</th>
                                  <th scope="col">عادية</th>
                                  <th scope="col">سيئة</th>
                                  <th scope="col"> جد سيئة</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>خدمة التوصيل</td>
                                  <td><input type="radio" name="radio1" value='جيدة جدا' checked></td>
                                  <td><input type="radio" name="radio1" value='جيدة'></td>
                                  <td><input type="radio" name="radio1" value='عادية'></td>
                                  <td><input type="radio" name="radio1" value='سيئة	'></td>
                                  <td><input type="radio" name="radio1" value='جد سيئة'  ></td>
                                </tr>

                                <tr>
                                  <td>  خدمة الزبناء  </td>
                                  <td><input type="radio" name="radio2" value='جيدة جدا' checked></td>
                                  <td><input type="radio" name="radio2" value='جيدة'></td>
                                  <td><input type="radio" name="radio2" value='عادية'></td>
                                  <td><input type="radio" name="radio2" value='سيئة	'></td>
                                  <td><input type="radio" name="radio2" value='جد سيئة'  ></td>
                                </tr>

                                 <tr>
                                  <td>  سلوك ومهارات الموظفين </td>
                                  <td><input type="radio" name="radio3" value='جيدة جدا' checked></td>
                                  <td><input type="radio" name="radio3" value='جيدة'></td>
                                  <td><input type="radio" name="radio3" value='عادية'></td>
                                  <td><input type="radio" name="radio3" value='سيئة	'></td>
                                  <td><input type="radio" name="radio3" value='جد سيئة'  ></td>
                                </tr>


                                 <tr>
                                  <td>   ما رأيك في وقت التسليم؟  </td>
                                  <td><input type="radio" name="radio4" value='جيدة جدا' checked></td>
                                  <td><input type="radio" name="radio4" value='جيدة'></td>
                                  <td><input type="radio" name="radio4" value='عادية'></td>
                                  <td><input type="radio" name="radio4" value='سيئة	'></td>
                                  <td><input type="radio" name="radio4" value='جد سيئة'  ></td>
                                </tr>
                                
                              </tbody>
                            </table>

                            <br><br>


                            <fieldset>
                                <label>
                                    <span class="required">*</span>   ما هي المدن التي تودون إدراجها ؟
                                </label>
                                <textarea rows="8" class="wpcf7-textarea" name="ville" required></textarea>
                            </fieldset>




                            <fieldset >
                                    <label>
                                    <span class="required">*</span> منذ متى وأنت تستخدم خدمتنا

                                </label>
                               

                               <div class="row">
                                  <div class="col-xs-3">
                                    <label class="radio-inline"><input type="radio" name="optradio" value="أقل من 6 أشهر" checked> &nbsp أقل من 6 أشهر</label>
                                  </div>
                                  <div class="col-xs-3">
                                    <label class="radio-inline"><input type="radio" name="optradio" value="1 - 3 سنوات" >&nbsp 1 - 3 سنوات</label>
                                  </div>
                                  <div class="col-xs-3">
                                    <label class="radio-inline"><input type="radio" name="optradio" value="أكثر من 3 سنوات">&nbsp   أكثر من 3 سنوات   </label>
                                  </div>
                                  <div class="col-xs-3">
                                    <label class="radio-inline"><input type="radio" name="optradio" value="بين 6 أشهر و 3 سنوات">&nbsp 
                                      بين 6 أشهر و 3 سنوات
                                    </label>
                                  </div>
                               </div>

                                 
                            </fieldset>


                            

                             <fieldset>
                                <label>
                                    <span class="required">*</span> ما هي اقتراحاتكم ؟
                                </label>
                                <textarea rows="8" class="wpcf7-textarea" name="message" required></textarea>
                            </fieldset>






                              
                           

                            <input type="submit" class="wpcf7-submit" value="envoyer" style="float: left;" />
                        </form><!-- .wpcf7 end -->
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


           
        
        <script type="text/javascript">
            $(".navbar-default .navbar-nav>li:nth-of-type(5)").addClass("current-menu-item")
        </script>







    </body>


</html>
