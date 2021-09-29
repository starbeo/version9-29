<div class="row">
	<div class="col-md-5">
        <?php $selected = (get_option('background_authentication_admin') ? get_option('background_authentication_admin') : 'login_bg_1.jpg'); ?>
        <?= render_select('settings[background_authentication_admin]', $backgrounds_authentication, array('alias', array('name')), 'background_authentication_page_admin', $selected); ?>
        <hr />
        <div id="bloc-show-background-authentication-admin">
            <img id="img-background-authentication-admin" data-name="<?= $selected ?>" class="img img-responsive" src="<?= site_url('assets/images/background/fonds-ecran/big/' . $selected); ?>" style="max-height: 500px; margin: 0 auto;" />
        </div>
    </div>
    <?php if(get_option('background_authentication_client_active') == 1) { ?>
	<div class="col-md-5">
        <?php $selected = (get_option('background_authentication_client') ? get_option('background_authentication_client') : 'login_bg_1.jpg'); ?>
        <?= render_select('settings[background_authentication_client]', $backgrounds_authentication, array('alias', array('name')), 'background_authentication_page_client', $selected); ?>
        <hr />
        <div id="bloc-show-background-authentication-client">
            <img id="img-background-authentication-client" data-name="<?= $selected ?>" class="img img-responsive" src="<?= site_url('assets/images/background/fonds-ecran/big/' . $selected); ?>" style="max-height: 500px; margin: 0 auto;" />
        </div>
    </div>
    <?php } ?>
</div>
