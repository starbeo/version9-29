<?= form_hidden('_at_invoice_id', $invoice->id); ?>
<div class="col-md-12 no-padding animated fadeIn">
    <div class="panel_s">
        <div class="panel-body padding-17">
            <ul class="nav nav-tabs no-margin" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab_invoice" aria-controls="tab_invoice" role="tab" data-toggle="tab">
                        <?= _l('invoice'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <!-- Affichage Status -->
                    <?= format_facture_status($invoice->status); ?>
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <?php
                        $_tooltip = _l('invoice_sent_to_email_tooltip');
                        if ($invoice->sent == 1) {
                            $_tooltip = _l('invoice_already_send_to_client_tooltip', time_ago($invoice->datesend));
                        }

                        ?>
                        <?php if (has_permission('invoices', '', 'edit')) { ?>
                            <a href="<?= admin_url('factures/facture/' . $invoice->id); ?>" data-toggle="tooltip" title="<?= _l('edit_invoice_tooltip'); ?>" class="btn btn-default pull-left mright5" data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>
                        <?php } ?>
                        <?php if (has_permission('invoices', '', 'edit') && $invoice->type == 2 && get_option('show_add_line_additionnal_in_invoice') == 1) { ?>
                            <a href="#" data-toggle="modal"  data-target="#add_line_additionnal_modal" class="btn btn-info pull-left mright5" data-placement="bottom" title="<?= _l('add_additionnal_line_invoice'); ?>" data-id="<?= $invoice->id; ?>" data-description-line="<?= $invoice->description_line; ?>" data-total-line="<?= $invoice->total_line; ?>"><i class="fa fa-plus"></i></a>
                        <?php } ?>
                        <a href="#" data-toggle="modal"  data-target="#commentaire_modal" class="btn btn-info pull-left mright5" data-placement="bottom" data-id="<?= $invoice->id; ?>" data-comment="<?= $invoice->commentaire; ?>"><i class="fa fa-comments-o"></i></a>
                        <a href="<?= admin_url('factures/pdf/' . $invoice->id); ?>" class="btn btn-danger pull-left mright5" data-toggle="tooltip" title="Voir PDF" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                        <a href="#" id="facture-send-to-client" class="btn pull-left btn-primary mright5" data-toggle="tooltip" title="<?= $_tooltip; ?>" data-placement="bottom" data-email="<?= $invoice->client->email; ?>" data-invoiceid="<?= $invoice->id; ?>"><i class="fa fa-envelope"></i></a>
                        <?php if (is_null($invoice->num_factureinterne)) { ?>
                            <?php if (has_permission('invoices', '', 'delete')) { ?>
                                <a href="<?= admin_url('factures/delete/' . $invoice->id); ?>" data-toggle="tooltip" title="<?= _l('delete_invoice_tooltip'); ?>" class="btn btn-danger pull-left mright5" data-placement="bottom"><i class="fa fa-trash"></i></a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Affichage Sent -->
                    <?php if ($invoice->sent == 1) { ?>
                        <?= format_facture_send($invoice->sent, $invoice->datesend); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr />
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_invoice">
                    <div id="invoice-preview">
                        <div class="col-md-12 no-padding">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="bold no-margin">
                                        <a href="<?= admin_url('factures/facture/' . $invoice->id); ?>"><?= $invoice->nom; ?></a>
                                    </h4>
                                    <p class="ptop10">
                                        <span><span class="text-muted"><?= _l('invoice_type') . ' : '; ?></span> 
                                            <?php
                                            $class = '';
                                            if ($invoice->type == 2) {
                                                $class = 'success';
                                            } else if ($invoice->type == 3) {
                                                $class = 'danger';
                                            }

                                            ?>
                                            <span class="label label-<?= $class; ?>"><?= $invoice->name; ?></span>
                                        </span>
                                    </p>
                                    <p>
                                        <span><span class="text-muted">Nombre Colis :</span> <b><?= count($invoice->items); ?></b></span>
                                    </p>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <h4 class="bold no-margin">
                                        <a href="<?= admin_url('expediteurs/expediteur/' . $invoice->id_expediteur); ?>" target="_blank"><?= $invoice->client->nom; ?></a>
                                    </h4>
                                    <p class="ptop10">
                                        <span><span class="text-muted"><?= _l('invoice_data_date'); ?></span> <?= $invoice->date_created; ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive" style="max-height: 688px;">
                                        <table class="table items no-margin">
                                            <thead>
                                                <tr>
                                                    <th><?= _l('colis_list_code_barre'); ?></th>
                                                    <th><?= _l('colis_list_crbt'); ?></th>
                                                    <th><?= _l('colis_list_fresh'); ?></th>
                                                    <th><?= _l('colis_list_etat'); ?></th>
                                                    <th><?= _l('colis_list_status'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (isset($invoice)) {
                                                    foreach ($invoice->items as $item) {
                                                        $_item = '';
                                                        $_item .= '<tr>';
                                                        $_item .= '<td>' . $item['code_barre'] . '</td>';
                                                        $_item .= '<td>' . $item['crbt'] . '</td>';
                                                        $_item .= '<td>' . $item['frais'] . '</td>';
                                                        $_item .= '<td>' . format_etat_colis($item['etat_id']) . '</td>';
                                                        $_item .= '<td>' . format_status_colis($item['status_reel']) . '</td>';
                                                        $_item .= '</tr>';
                                                        echo $_item;
                                                    }
                                                }

                                                ?>
                                            </tbody>
                                    </div>
                                </div>
                                <?php
                                if (isset($invoice) && $invoice->type == 2) {
                                    $discount = 0;
                                    $discount_txt = '';
                                    if ($invoice->remise_type == 'fixed_amount') {
                                        $discount = $invoice->remise;
                                    } else if ($invoice->remise_type == 'percentage') {
                                        $discount = ($invoice->total_refuse * ($invoice->remise / 100));
                                        $discount_txt = ' de ' . $invoice->remise . '%';
                                    }

                                    ?>
                                    <div class="col-md-7 col-md-offset-5">
                                        <table class="table text-right">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <span class="bold"><?= _l('total_brut'); ?></span>
                                                    </td>
                                                    <td>
                                                        <?= number_format($invoice->total_crbt, 2, ',', ' ') . ' Dhs'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="bold"><?= _l('total_frais'); ?> (-)</span>
                                                    </td>
                                                    <td>
                                                        <?= number_format($invoice->total_frais, 2, ',', ' ') . ' Dhs'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="bold"><?= _l('total_refuse'); ?> (-)</span>
                                                    </td>
                                                    <td>
                                                        <?= number_format($invoice->total_refuse, 2, ',', ' ') . ' Dhs'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="bold"><?= _l('total_parrainage'); ?> (-)</span>
                                                    </td>
                                                    <td>
                                                        <?= number_format($invoice->total_parrainage, 2, ',', ' ') . ' Dhs'; ?>
                                                    </td>
                                                </tr>
                                                <?php if (!is_null($invoice->description_line)) { ?>
                                                    <tr>
                                                        <td>
                                                            <span class="bold"><?= ucwords($invoice->description_line); ?></span>
                                                        </td>
                                                        <td>
                                                            <?= number_format($invoice->total_line, 2, ',', ' ') . ' Dhs'; ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td>
                                                        <span class="bold"><?= _l('discount_invoice') . $discount_txt; ?> (+)</span>
                                                    </td>
                                                    <td>
                                                        <?= number_format($discount, 2, ',', ' ') . ' Dhs'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="bold"><?= _l('total_net'); ?></span>
                                                    </td>
                                                    <td>
                                                        <b><?= number_format($invoice->total_net, 2, ',', ' ') . ' Dhs'; ?></b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
