<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= $title; ?>
                    </div>
                    <div class="col-md-12 no-padding animated fadeIn">
                        <div class="panel_s">
                            <?= form_open($this->uri->uri_string()); ?>
                            <div class="panel-body">
                                <h4 class="bold">
                                    <?= _l('payment_edit_for_invoice'); ?>
                                    <?php if (has_permission('factures_internes', '', 'view') || has_permission('factures_internes', '', 'view_own')) { ?>
                                        <a href="<?= admin_url('factures_internes/index/' . $payment->factureinterneid); ?>"><?= $invoice->nom; ?></a>
                                        <?php
                                    } else {
                                        echo $invoice->nom;
                                    }

                                    ?>
                                </h4>
                                <?= render_input('amount', 'payment_edit_amount_received', $payment->amount, 'number', array('disabled' => 'disabled')); ?>
                                <?= render_date_input('date', 'payment_edit_date', date(get_current_date_format(), strtotime($payment->date))); ?>
                                <?= render_select('paymentmode', $payment_modes, array('id', 'name'), 'payment_mode', $payment->paymentmode); ?>
                                <?= render_input('transactionid', 'payment_transaction_id', $payment->transactionid); ?>
                                <?= render_textarea('note', 'payment_edit_lave_note', $payment->note, array('rows' => 7)); ?>
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= _l('payment_view_heading'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="text-right">
                            <a href="<?= admin_url('payments/pdf/' . $payment->paymentid); ?>" class="btn btn-default" data-toggle="tooltip" title="View PDF" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                            <?php if (has_permission('payments', '', 'delete')) { ?>
                                <a href="<?= admin_url('payments/delete/' . $payment->paymentid); ?>" class="btn btn-danger"><i class="fa fa-remove"></i></a>
                            <?php } ?>
                        </div>
                        <hr />
                        <div class="col-md-12 text-center">
                            <h3 class="text-uppercase"><?= _l('payment_receipt'); ?></h3>
                        </div>
                        <div class="col-md-12 mtop30">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><?= _l('payment_date'); ?> <span class="pull-right bold"><?= date(get_current_date_format(), strtotime($payment->date)); ?></span></p>
                                    <hr />
                                    <p><?= _l('payment_view_mode'); ?> <span class="pull-right bold"><?= $payment->name; ?></span></p>
                                    <?php if (!empty($payment->transactionid)) { ?>
                                        <hr />
                                        <p><?= _l('payment_transaction_id'); ?>: <span class="pull-right bold"><?= $payment->transactionid; ?></span></p>
                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-6">
                                    <div class="payment-preview-wrapper">
                                        <?= _l('payment_total_amount'); ?><br />
                                        <?= format_money($payment->amount, $invoice->symbol); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mtop30">
                            <h4><?= _l('payment_for_string'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-borderd table-hover">
                                    <thead>
                                        <tr>
                                            <th><?= _l('payment_table_invoice_number'); ?></th>
                                            <th><?= _l('payment_table_invoice_date'); ?></th>
                                            <th><?= _l('payment_table_invoice_amount_total'); ?></th>
                                            <th><?= _l('payment_table_payment_amount_total'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= $invoice->nom; ?></td>
                                            <td><?= date(get_current_date_format(), $invoice->date_created); ?></td>
                                            <td><?= format_money($invoice->total); ?></td>
                                            <td><?= format_money($payment->amount); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/payments/payment.js?v=' . version_sources()); ?>"></script>
</body>
</html>
