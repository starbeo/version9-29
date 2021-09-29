<div class="row">
    <?php if(get_option('sms_solde_active') == 1) { ?>
	<div class="col-md-3">
        <h4><?= _l('sms_solde'); ?></h4>
		<hr />
		<?= render_input('sms_solde_low_cost','sms_solde_low_cost',get_solde_sms(), 'number', array('disabled' => 'disabled')); ?>
		<?= render_input('sms_solde_premium','sms_solde_premium',get_solde_sms(true), 'number', array('disabled' => 'disabled')); ?>
	</div>
    <?php } ?>
	<div class="col-md-3">
        <h4><?= _l('settings_sms_premium'); ?></h4>
		<hr />
		<?= render_input('sms_premium_shortcode','message_sender',get_option('sms_premium_shortcode'), 'text', array('min' => '3', 'max' => '11')); ?>
	</div>
	<div class="col-md-6">
		<h4><?= _l('send_test_sms_heading'); ?></h4>
		<p class="text-muted"><?= _l('send_test_sms_subheading'); ?></p>
		<hr />
		<?= render_input('phone_number_test','phone_number'); ?>
        <?= render_textarea('message_test', 'message', '', array('rows' => 6, 'maxlength' => 450)); ?>
        <p><?= _l('note_message_sms'); ?></p>
		<div class="form-group">
			<button type="button" class="btn btn-info test_sms">Test</button>
		</div>
	</div>
</div>
