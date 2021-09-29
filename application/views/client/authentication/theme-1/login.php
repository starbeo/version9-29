<?php $this->load->view('client/authentication/' . get_option('theme_login_client') . '/includes/head.php'); ?>
<div id="wrapper">
    <div id="content">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <div class="banniere mbot5" style="width:100%;">
                    <img src="<?= site_url('uploads/company/' . get_option('companyalias') . '/login-client/banniere-login.gif'); ?>" width='100%' height='100%'> 
                </div>
                <div class="col-md-3 no-padding">
                    <h1 class="text-center">
                        <?= _l('expediteurs_login_heading_no_register'); ?>
                    </h1>
                    <?= form_open('authentication/client', array('class' => 'bgwhite p15 login-form')); ?>
                    <?= validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                    <?php if ($this->session->flashdata('message-danger')) { ?>
                        <div class="alert alert-danger">
                            <?= $this->session->flashdata('message-danger'); ?>
                        </div>
                    <?php } ?>
                    <?php if ($this->session->flashdata('message-success')) { ?>
                        <div class="alert alert-success">
                            <?= $this->session->flashdata('message-success'); ?>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="email" id="email" placeholder="<?= _l('clients_login_email'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" id="password" placeholder="<?= _l('clients_login_password'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="remember"> <?= _l('clients_login_remember'); ?>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block"><?= _l('clients_login_login_string'); ?></button>
                    </div>
                    <?= form_close(); ?>
                    <div class="mtop10">
                        <img src="<?= site_url('uploads/company/' . get_option('companyalias') . '/login-client/icon_colis.gif'); ?>" width='100%' height='200px'>
                    </div>
                </div>
                <div class="col-md-9" style="padding-right: 0px;">
                    <img src="<?= site_url('uploads/company/' . get_option('companyalias') . '/login-client/ban_body.jpg'); ?>" width='100%' height='250px'>
                    <img src="<?= site_url('uploads/company/' . get_option('companyalias') . '/login-client/contenu_body.gif'); ?>" width='100%' height=''>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="navbar-fixed-bottom">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
<center>Copyright © <?= date('Y'); ?> MyColis. All Rights Reserved.</center>
            </div>
        </div>
    </div>
</footer>
</body>
<?php $this->load->view('client/authentication/' . get_option('theme_login_client') . '/includes/scripts.php'); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/authentication/' . get_option('theme_login_client') . '/manage.js'); ?>"></script>
</html>


