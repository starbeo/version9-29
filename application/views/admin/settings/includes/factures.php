<div class="row">
	<div class="col-md-6">
        <?= render_input('limit_total_colis_added_to_invoice','limit_total_colis_added_to_invoice',get_option('limit_total_colis_added_to_invoice'), 'number', array('disabled' => true)); ?>
        <hr />
        <?php 
        $checked = '';
        if(get_option('show_add_line_additionnal_in_invoice') == 1) {
            $checked = 'checked';
        }
        ?>
        <div class="checkbox">
            <input type="checkbox" name="settings[show_add_line_additionnal_in_invoice]" <?= $checked; ?>>
            <label for="show_add_line_additionnal_in_invoice"><?= _l('show_add_line_additionnal_in_invoice'); ?></label>
        </div>
        <hr />
        <?php 
        $checked = '';
        if(get_option('show_discount_in_invoice_pdf') == 1) {
            $checked = 'checked';
        }
        ?>
        <div class="checkbox">
            <input type="checkbox" name="settings[show_discount_in_invoice_pdf]" <?= $checked; ?>>
            <label for="show_discount_in_invoice_pdf"><?= _l('show_discount_in_invoice_pdf'); ?></label>
        </div>
    </div>
</div>
