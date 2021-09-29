 <div class="header-wrapper header-transparent">
            <!-- .header.header-style01 start -->
            <header id="header"  class="header-style01">
                <!-- .container start -->
                <div class="container">
                    <!-- .main-nav start -->
                    <div class="main-nav">
                        <!-- .row start -->
                        <div class="row">
                            <div class="col-md-12">
                                <nav class="navbar navbar-default nav-left" role="navigation">

                                    <!-- .navbar-header start -->
                                    <div class="navbar-header" style="padding-left: 15px">
                                        <div class="logo">
                                            <a href="index.php">
                                                <img src="new_img/logo.png"  width="130px" />
                                            </a>
                                        </div><!-- .logo end -->
                                    </div><!-- .navbar-header start -->



                                   <?php require('login.php'); ?>


                                    <!-- MAIN NAVIGATION -->
                                    <div class="collapse navbar-collapse">
                                        <ul class="nav navbar-nav" style="position: relative;left: -20px">

                                            <li><a href="index.php" >Accueil</a></li>
                                            <li><a href="about.php">A propos</a></li>
                                            <li><a href="service.php">Services</a></li>
                                            <li><a href="tarif.php">Villes & Tarifs</a></li>
                                            <li><a href="devenir_client.php">Devenir Client</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                            <li><a href="suivrecolis.php">Track Colis</a></li>
                                            <li>
                                              <a  id="lien_login" style="cursor: pointer;"  href="https://powercoursier.ma/avis.php">
                                              Votre avis</a>
                                            </li>
                                            <li>
                                              <a  id="lien_login" style="cursor: pointer;"  href="track.php">
                                              My Colis</a>
                                            </li>
                                            <li>
                                              <a  id="lien_login" style="cursor: pointer;"  href="https://mycolis.net/authentication/client" target="_blank">
                                              <img src="new_img/login.svg" class="icon_menu" width="20px">Espace client</a>
                                            </li>
                                             
                                           
                                             </ul><!-- .nav.navbar-nav end -->

                                        <!-- RESPONSIVE MENU -->
                                        <div id="dl-menu" class="dl-menuwrapper">
                                            <button class="dl-trigger">Open Menu</button>

                                            <ul class="dl-menu">
                                                <li><a href="index.php" >Accueil</a></li>
                                                <li><a href="about.php">Qui somme nous</a></li>
                                                <li><a href="service.php">Services</a></li>
                                                <li><a href="tarif.php">Villes & Tarifs</a></li>
                                                <li><a href="devenir_client.php">Devenir Client</a></li>
                                                <li><a href="contact.php">Contact</a></li>
                                                <li><a href="suivrecolis.php">Suivre votre colis</a></li>
                                                <li> <a  id="lien_login" style="cursor: pointer;"  href="https://powercoursier.ma/avis.php" target="_blank">Votre avis</a></li>
                                                <li> <a  id="lien_login" style="cursor: pointer;"  href="https://mycolis.net/authentication/client" target="_blank">Client</a></li>
                                                <li> <a  id="lien_login" style="cursor: pointer;"  href="track.php" target="_blank">My Colis</a></li>
                                                
                                            </ul><!-- .dl-menu end -->
                                        </div><!-- #dl-menu end -->



                                    



                                    </div><!-- MAIN NAVIGATION END -->
                                </nav><!-- .navbar.navbar-default end -->
                            </div><!-- .col-md-12 end -->
                        </div><!-- .row end -->
                    </div><!-- .main-nav end -->
                </div><!-- .container end -->
            </header><!-- .header.header-style01 -->
        </div><!-- .header-wrapper.header-transparent end -->














<style>
@media only screen and (max-width: 1999px) and (min-width: 992px){
.nav>li>a { padding: 15px 10px 15px 12px ;font-size: 12.2px}
}

.icon_menu{
  position: relative;top: 5px;display: inline-block !important;right: 3px
}

@media (min-width: 990px ) and (max-width:1200px ){
.logo{display: none;}
.nav>li>a { padding: 15px 7px 0px 7px ;font-size: 11px}

}


/* Add Zoom Animation */
.animate { animation: zoom 0.6s}
@keyframes zoom {
    from {transform: scale(0)} 
    to {transform: scale(1)}
}

#form-passe{display: none}
.avatar {
    width: 120px;
    height:120px;
    border-radius: 50%;
    margin: 10px auto;
}

/* The popup form - hidden by default */
.form-popup {
  position: absolute;
  bottom: 0;
  right: 0%;
  border: 3px solid #f1f1f1;
  z-index: 9;
  top:85px;
  height: 450px;
  padding:0px 0px;
  background: white;
  display: none;
}


/* Add styles to the form container */
.form-container {
  max-width: 300px;
  padding: 10px;
  background-color: white;
}

/* Full-width input fields */
.form-container input[type=text],.form-container input[type=email], .form-container input[type=password] {
    width: 100%;
    padding: 15px;
    margin: 0px 0 12px 0;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* When the inputs get focus, do something */
.form-container input[type=text]:focus,.form-container input[type=email]:focus, .form-container input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

/* Set a style for the submit/login button */
.form-container .btn {
  background-color: #b9191b;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  width: 100%;
  margin-bottom:10px;
  opacity: 1;
}



.close_form {
    right: 10px;
    top:10px;
    position: relative;
    float: right;
    color: #000;
    font-size: 35px;
    font-weight: bold;
}
.close_form:hover,.close_form:focus {
    color: #777;
    cursor: pointer;
}

@media (max-width: 900px){
    .form-popup {
    top: 150px;
    right: 0px;
    width: 100%
    }
    .form-container { max-width: 100% !important}
    
}


</style>



