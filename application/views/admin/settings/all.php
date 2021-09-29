<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?= form_hidden('tab_hash', $tab_hash); ?>
		<?= form_open_multipart($this->uri->uri_string(),array('id'=>'settings-form')); ?>
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#general_settings_tab" aria-controls="general_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('general'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#migration_settings_tab" aria-controls="migration_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('construction'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#authentication_settings_tab" aria-controls="authentication_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('authentication'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#email_settings_tab" aria-controls="email_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('email'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#sms_settings_tab" aria-controls="sms_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('sms'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#colis_settings_tab" aria-controls="colis_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('colis'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#status_settings_tab" aria-controls="status_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('status'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#delivery_notes_settings_tab" aria-controls="delivery_notes_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('delivery_notes'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#factures_settings_tab" aria-controls="factures_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('invoices'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#themes_settings_tab" aria-controls="themes_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('templates_pdf'); ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#contrats_settings_tab" aria-controls="contrats_settings_tab" role="tab" data-toggle="tab">
                                <?= _l('contract'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="general_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/general.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="migration_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/migration.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="authentication_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/authentication.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="email_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/email.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="sms_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/sms.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="colis_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/colis.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="status_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/status.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="delivery_notes_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/bons_livraison.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="factures_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/factures.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="themes_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/themes.php'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="contrats_settings_tab">
                                <?php include_once(APPPATH . 'views/admin/settings/includes/contrats.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="col-md-12 text-left">
				<button type="submit" class="btn btn-primary"><?= _l('settings_save'); ?></button>
			</div>
		</div>
		<?= form_close(); ?>
	</div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/settings/all.js?v=' . version_sources()); ?>"></script>
</body>
</html>
