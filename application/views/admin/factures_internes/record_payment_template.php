<div class="col-md-12 no-padding animated fadeIn">
    <div class="panel_s">
        <?= form_open('admin/factures_internes/record_payment', array('class' => '.form-record-payment')); ?>
        <?= form_hidden('factureinterneid', $invoice->id); ?>
        <div class="panel-body">
            <h4 class="bold no-margin"><?= _l('record_payment_for_invoice'); ?> <?= $invoice->nom; ?></h4>
            <hr />
            <div class="row">
                <div class="col-md-6">
                    <?= render_input('amount', 'record_payment_amount_received'); ?>
                    <?= render_date_input('date', 'record_payment_date', date(get_current_date_format())); ?>
                    <?= render_select('paymentmode', $payment_modes, array('id', 'name'), 'payment_mode'); ?>
                    <div class="checkbox checkbox-primary mtop15 do_not_redirect inline-block">
                        <input type="checkbox" name="do_not_redirect" checked>
                        <label for="do_not_redirect"><?= _l('do_not_redirect_me_to_the_payment_processor'); ?></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="note" class="control-label"><?= _l('record_payment_leave_note'); ?></label>
                    <textarea name="note" class="form-control" rows="8" placeholder="<?= _l('invoice_record_payment_note_placeholder'); ?>" id="note"></textarea>
                </div>
            </div>
            <div class="row ptop10" id="element_cheque"></div>
            <div class="pull-right mtop15">
                <a href="#" class="btn btn-danger" onclick="init_facture_interne(<?= $invoice->id; ?>);
                        return false;"><?= _l('cancel'); ?></a>
                <button type="submit" class="btn btn-success"><?= _l('submit'); ?></button>
            </div>
            <?php if ($payments) { ?>
                <div class="mtop25 inline-block full-width">
                    <h5 class="bold"><?= _l('facture_interne_payments_received'); ?></h5>
                    <?php include_once(APPPATH . 'views/admin/factures_internes/facture_interne_payments_table.php'); ?>
                </div>
            <?php } ?>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/factures-internes/record-payment-template.js?v=' . version_sources()); ?>"></script>
