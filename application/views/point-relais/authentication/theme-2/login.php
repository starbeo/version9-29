<?php $this->load->view('point-relais/authentication/' . get_option('theme_login_point_relais') . '/includes/head.php'); ?>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image" style="background: url('<?= site_url(); ?>assets/images/background/point-relais.jpeg'); background-repeat: no-repeat; background-size: 100% 100%;"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4"><?= _l('point_relais_area'); ?></h1>
                                        <div class="div-logo">
                                            <?= '<img src="' . base_url() . 'uploads/company/' . get_option('companyalias') . '/logo-login-admin.png" alt="' . get_option('companyname') . '" style="padding-top:8px; width: 100%;">'; ?>
                                        </div>    
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
                                    <?= render_input('email', 'email', set_value('email'), 'email'); ?>
                                    <?= render_input('password', 'password', '', 'password'); ?>
                                    <div class="form-group no-margin">
                                        <input type="checkbox" id="show_password"> <?= _l('show_password'); ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" name="remember"> <?= _l('remember_me'); ?>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-user btn-block"><?= _l('login'); ?></button>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <?php if (!empty(get_option('website'))) { ?>
                                            <?= icon_btn(get_option('website'), 'globe', 'btn-primary mright5', array('title' => _l('our_website'), 'target' => '_blank'), true); ?>
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
                                    <?= form_close(); ?>
                                </div>
                            </div>
                        </div>
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
