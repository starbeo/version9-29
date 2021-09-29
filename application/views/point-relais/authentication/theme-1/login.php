<?php $this->load->view('point-relais/authentication/' . get_option('theme_login_point_relais') . '/includes/head.php'); ?>
<body id="wallId" class="login_admin" style="background-image: url('<?= site_url(); ?>assets/images/background/fonds-ecran/big/<?= get_option('background_authentication_admin'); ?>'); background-size: 100%;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 authentication-form-wrapper animated fadeIn">
                <div class="company-logo"></div>
                <div class="mtop40 authentication-form">
                    <i id="btn-configuration" class="fa fa-cog fa-lg btn-configuration" onclick="modifyWallpaper();"></i>
                    <div class="div-logo">
                        <?= '<img src="' . base_url() . 'uploads/company/' . get_option('companyalias') . '/logo-login-admin.png" alt="' . get_option('companyname') . '" style="padding-top:8px;">'; ?>
                    </div>
                    <?= form_open($this->uri->uri_string()); ?>
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
                    <div class="text-center mb10">
                        <?php if (!empty(get_option('main_domain'))) { ?>
                            <?= icon_btn(get_option('main_domain'), 'globe', 'btn-globe mright5', array('title' => _l('our_website'), 'target' => '_blank'), true); ?>
                        <?php } ?>
                        <?php if (!empty(get_option('url_page_facebook'))) { ?>
                            <?= icon_btn(get_option('url_page_facebook'), 'facebook', 'btn-facebook mright5', array('title' => _l('our_facebook_page'), 'target' => '_blank'), true); ?>
                        <?php } ?>
                        <?php if (!empty(get_option('url_page_instagram'))) { ?>
                            <?= icon_btn(get_option('url_page_instagram'), 'instagram', 'btn-instagram mright5', array('title' => _l('our_instagram_page'), 'target' => '_blank'), true); ?>
                        <?php } ?>
                        <?php if (!empty(get_option('url_page_linkedin'))) { ?>
                            <?= icon_btn(get_option('url_page_linkedin'), 'linkedin', 'btn-linkedin mright5', array('title' => _l('our_linkedin_page'), 'target' => '_blank'), true); ?>
                        <?php } ?>
                    </div>
                    <h3 class="text-center"><?= _l('point_relais_area'); ?></h3>
                    <?= render_input('email', 'email', set_value('email'), 'email'); ?>
                    <?= render_input_group('password', 'password', '', 'password', array(), array(), '', '', '<a href="javascript:void(0)" onclick="showHidePassword()"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>'); ?>
                    <div class="form-group">
                        <input type="checkbox" name="remember"> <?= _l('remember_me'); ?>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block"><?= _l('login'); ?></button>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div id="configuration" class="hide-configuration">
                    <span id="textWallId" class="textWall">Fonds d'écran</span>
                    <div id="containerWallId" class="containerWall">
                        <img id="imageWall1" alt="image" onclick="changeWallpaper('login_bg_1.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_1.jpg'); ?>">
                        <img id="imageWall2" alt="image" onclick="changeWallpaper('login_bg_2.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_2.jpg'); ?>">
                        <img id="imageWall3" alt="image" onclick="changeWallpaper('login_bg_3.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_3.jpg'); ?>">
                        <img id="imageWall4" alt="image" onclick="changeWallpaper('login_bg_4.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_4.jpg'); ?>">
                        <img id="imageWall5" alt="image" onclick="changeWallpaper('login_bg_5.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_5.jpg'); ?>">
                        <img id="imageWall6" alt="image" onclick="changeWallpaper('login_bg_6.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_6.jpg'); ?>">
                        <img id="imageWall7" alt="image" onclick="changeWallpaper('login_bg_7.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_7.jpg'); ?>">
                        <img id="imageWall8" alt="image" onclick="changeWallpaper('login_bg_8.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_8.jpg'); ?>">
                        <img id="imageWall9" alt="image" onclick="changeWallpaper('login_bg_9.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_9.jpg'); ?>">
                        <img id="imageWall10" alt="image" onclick="changeWallpaper('login_bg_10.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_10.jpg'); ?>">
                        <img id="imageWall11" alt="image" onclick="changeWallpaper('login_bg_11.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_11.jpg'); ?>">   
                        <img id="imageWall12" alt="image" onclick="changeWallpaper('login_bg_12.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_12.jpg'); ?>">
                        <img id="imageWall13" alt="image" onclick="changeWallpaper('login_bg_13.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_13.jpg'); ?>">
                        <img id="imageWall14" alt="image" onclick="changeWallpaper('login_bg_14.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_14.jpg'); ?>">
                        <img id="imageWall15" alt="image" onclick="changeWallpaper('login_bg_15.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_15.jpg'); ?>">
                        <img id="imageWall16" alt="image" onclick="changeWallpaper('login_bg_16.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_16.jpg'); ?>">
                        <img id="imageWall17" alt="image" onclick="changeWallpaper('login_bg_17.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_17.jpg'); ?>">
                        <img id="imageWall18" alt="image" onclick="changeWallpaper('login_bg_18.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_18.jpg'); ?>">
                        <img id="imageWall19" alt="image" onclick="changeWallpaper('login_bg_19.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_19.jpg'); ?>">
                        <img id="imageWall20" alt="image" onclick="changeWallpaper('login_bg_20.jpg')" src="<?= base_url('assets/images/background/fonds-ecran/small/login_bg_20.jpg'); ?>">
                    </div>
                </div>    
            </div>
        </div>
    </div>
</body>
<?php $this->load->view('point-relais/authentication/' . get_option('theme_login_point_relais') . '/includes/scripts.php'); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/authentication/' . get_option('theme_login_point_relais') . '/manage.js'); ?>"></script>
</html>
