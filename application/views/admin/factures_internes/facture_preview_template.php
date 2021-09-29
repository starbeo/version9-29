<?= form_hidden('_at_invoice_id', $invoice->id); ?>
<div class="col-md-12 no-padding animated fadeIn">
    <div class="panel_s">
        <div class="panel-body padding-17">
            <ul class="nav nav-tabs no-margin" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab_invoice" aria-controls="tab_invoice" role="tab" data-toggle="tab">
                        <?= _l('facture_interne'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab_payments" aria-controls="tab_payments" role="tab" data-toggle="tab">
                        <?= _l('facture_interne_payments_received'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <?php if (has_permission('payments', '', 'create')) { ?>
                        <a href="#" onclick="record_payment_facture_interne(<?= $invoice->id; ?>);
                                    return false;" class=" btn btn-info pull-left"><?= _l('facture_interne_record_payment'); ?></a>
                       <?php } ?>
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <?php if (has_permission('factures_internes', '', 'edit')) { ?>
                            <a href="<?= admin_url('factures_internes/facture/' . $invoice->id); ?>" data-toggle="tooltip" title="<?= _l('edit_invoice_tooltip'); ?>" class="btn btn-default pull-left mright5" data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>
                        <?php } ?>
                        <a href="<?= admin_url('factures_internes/pdf/' . $invoice->id); ?>" class="btn btn-danger pull-left mright5" data-toggle="tooltip" title="Imprimer PDF Facture Interne" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                        <a href="<?= admin_url('factures_internes/pdf/' . $invoice->id . '/detailles'); ?>" class="btn btn-danger pull-left mright5" data-toggle="tooltip" title="Imprimer PDF Virements Clients" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                        <a href="<?= admin_url('factures_internes/excel/' . $invoice->id); ?>" class="btn btn-success pull-left mright5" data-toggle="tooltip" title="Imprimer Excel Virements Clients" data-placement="bottom"><i class="fa fa-file-excel-o"></i></a>
                        <a href="<?= admin_url('factures_internes/zip_factures_facture_interne/' . $invoice->id); ?>" class="btn btn-info pull-left mright5" data-toggle="tooltip" title="Télécharger le dossier complet de cette Facture Interne" data-placement="bottom"><i class="fa fa-download"></i></a>
                        <?php if (has_permission('factures_internes', '', 'delete')) { ?>
                            <a href="<?= admin_url('factures_internes/delete/' . $invoice->id); ?>" data-toggle="tooltip" title="<?= _l('delete_invoice_tooltip'); ?>" class="btn btn-danger pull-left mright5" data-placement="bottom"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr />
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10" id="tab_payments">
                    <?php include_once(APPPATH . 'views/admin/factures_internes/facture_interne_payments_table.php'); ?>
                </div>
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_invoice">
                    <div id="invoice-preview">
                        <div class="col-md-12 no-padding">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="bold no-margin">
                                        <a href="<?= admin_url('factures_internes/facture/' . $invoice->id); ?>"><?= $invoice->nom; ?></a>
                                    </h4>
                                    <p class="ptop5">
                                        <span><span class="text-muted"><?= _l('invoice_data_date'); ?></span> <?= $invoice->date_created; ?></span>
                                    </p>
                                    <p>
                                        <span><span class="text-muted"><?= _l('facture_interne_number_of_invoices') . ' : '; ?></span> <b><?= count($invoice->items); ?></b></span>
                                    </p>
                                </div>
                                <div class="col-sm-6 text-right">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive" style="max-height: 688px;">
                                        <table class="table items no-margin">
                                            <thead>
                                                <tr>
                                                    <th><?= _l('name'); ?></th>
                                                    <th><?= _l('client'); ?></th>
                                                    <th><?= _l('invoice_type'); ?></th>
                                                    <th><?= _l('status'); ?></th>
                                                    <th><?= _l('total_net'); ?></th>
                                                    <th><?= _l('facture_interne_date_created'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (isset($invoice)) {
                                                    foreach ($invoice->items as $item) {
                                                        $_item = '';
                                                        $_item .= '<tr>';
                                                        $_item .= '<td><a href="' . admin_url('factures/facture/' . $item['factureid']) . '" target="_blank">' . $item['nom'] . '</a></td>';
                                                        $_item .= '<td><a href="' . admin_url('expediteurs/expediteur/' . $item['client_id']) . '" target="_blank">' . $item['client'] . '</a></td>';
                                                        $_item .= '<td>' . format_facture_type($item['type']) . '</td>';
                                                        $_item .= '<td>' . format_facture_status($item['status']) . '</td>';
                                                        $_item .= '<td>' . $item['total_net'] . ' Dhs</td>';
                                                        $_item .= '<td>' . date(get_current_date_format(), strtotime($item['date_created'])) . '</td>';
                                                        $_item .= '</tr>';
                                                        echo $_item;
                                                    }
                                                }

                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-7 col-md-offset-5">
                                    <table class="table text-right">
                                        <tbody>
                                            <tr>
                                                <td><span class="bold"><?= _l('facture_interne_total_crbt') . ' : '; ?></span></td>
                                                <td><?= $invoice->total . ' Dhs'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?= _l('facture_interne_total_fresh') . ' : '; ?></span></td>
                                                <td><?= $invoice->total_frais . ' Dhs'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?= _l('total_refuse') . ' : '; ?></span></td>
                                                <td><?= $invoice->total_refuse . ' Dhs'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?= _l('total_parrainage') . ' : '; ?></span></td>
                                                <td><?= $invoice->total_parrainage . ' Dhs'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?= _l('facture_interne_total_discount') . ' : '; ?></span></td>
                                                <td><?= $invoice->total_remise . ' Dhs'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?= _l('facture_interne_total_net') . ' : '; ?></span></td>
                                                <td><?= $invoice->total_net . ' Dhs'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?= _l('facture_interne_total_received') . ' : '; ?></span></td>
                                                <td><?= $invoice->total_received . ' Dhs'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?= _l('facture_interne_rest') . ' : '; ?></span></td>
                                                <td><?= $invoice->rest . ' Dhs'; ?></td>
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
</div>
