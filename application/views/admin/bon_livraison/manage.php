<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('bon_livraison', '', 'create')) { ?>
                            <a href="<?= admin_url('bon_livraison/bon/false/' . $type); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_delivery_note'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right btn-filtre"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <?= form_open(admin_url('bon_livraison/export_by_filter'), array('id' => 'form-export-colis')); ?>

                            <?php
                            $classPointRelaiActive = '';
                            if (get_permission_module('points_relais') == 0) {
                                $classPointRelaiActive = 'display-none';
                            }

                            ?>
                            <div class="col-md-3 <?= $classPointRelaiActive ?>">
                                <?= render_select('f-type-livraison', $types_livraison, array('id', array('name')), 'type_livraison'); ?>
                            </div>
                            <div class="col-md-3 <?= $classPointRelaiActive ?>">
                                <?= render_select('f-point-relai', $points_relais, array('id', array('nom')), 'point_relai', '', array(), array('id' => 'bloc-f-point-relai'), 'display-none'); ?>
                                <?php if (get_permission_module('points_relais') == 1) { ?>
                                <?= render_select('f-livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-f-livreur')); ?>
                                <?php } ?>
                            </div>
                            <?php if (get_permission_module('points_relais') == 0) { ?>
                                <div class="col-md-3">
                                    <?= render_select('f-livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-f-livreur')); ?>
                                </div>
                            <?php } ?>
                            <div class="col-md-3">
                                <?= render_select('f-utilisateur', $staff, array('staffid', array('firstname', 'lastname')), 'staff'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('f-date-created', 'etat_colis_livrer_date_created'); ?>
                            </div>
                            <div class="col-md-2">
                                <?= render_date_input('f-date-end', 'date_end'); ?>

                            </div>
                            <?php if (get_option('show_btn_export_excel_colis') == 1 && has_permission('colis', '', 'export')) { ?>
                                <button id="export-colis" type="submit" class="btn btn-success width100p mbot5"><?= _l('colis_export'); ?></i></button>
                            <?php } ?>
                                <?= form_close(); ?>
                            <div class="col-md-12 text-right">
                                <button id="filtre-submit" class="btn btn-primary"><?= _l('filter'); ?></button>
                                <button id="filtre-reset" class="btn btn-default"><?= _l('empty'); ?></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="btn btn-info mright5 pull-left display-block" data-toggle="modal" data-target="#batch_modal" data-type="<?= $type ?>"><?= _l('batch_invoice'); ?></div>


                    <div class="modal animated fadeIn" id="batch_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document" style="width: 80%;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">
                                        <span><?= _l('automatic_generation'); ?></span>
                                    </h4>
                                </div>
                                <?= form_open('admin/factures/batch', array('id' => 'batch-form')); ?>
                                <div class="modal-body">
                                    <?php
                                    $classShowTypeLivraison = '';
                                    if(get_permission_module('points_relais') == 0) {
                                        $classShowTypeLivraison = 'display-none';
                                    }
                                    ?>
                                    <div id="bloc-type-livraison" class="row <?= $classShowTypeLivraison ?>">
                                        <div class="col-md-12">
                                            <?= render_select('f-type-livraison', $types_livraison, array('id', array('name')), 'type_livraison'); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= render_date_input('start_date', 'colis_date_start'); ?>
                                            <?= render_select('expediteurs', array(), array(), 'als_expediteur', '', array('multiple' => true)); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= render_date_input('end_date', 'colis_date_end'); ?>
                                            <?= render_select('type_facture', $types, array('id', array('name')), 'type_facture', '', array('')); ?>


                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5 class="bold"><?= _l('list_of_customers'); ?> :</h5>
                                            <div class="bloc-customers-selected"></div>
                                        </div>
                                    </div>
                                    <?= loader_waiting_ajax('-9%', '45%'); ?>
                                    <div id="bloc-results-batch-invoices" class="row display-none">
                                        <div class="col-md-6">
                                            <h5 class="bold"><?= _l('list_of_invoices_created'); ?> :</h5>
                                            <div class="bloc-invoices-created"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="bold"><?= _l('list_of_errors'); ?> :</h5>
                                            <div class="bloc-errors-orders"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="btn btn-default pull-left" onclick="initModalBatchInvoices()"><?= _l('reset'); ?></div>
                                    <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                                    <button id="submit-batch-invoices" group="submit" class="btn btn-primary"><?= _l('generate'); ?></button>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?= form_open(admin_url('bon_livraison/change_status'), array('id' => 'change-status-bon-livraison-form')); ?>
                        <?php
                        $columns = array(
                            '<div class="checkbox checkbox-primary"><input type="checkbox" id="checkbox-all-bons-livraison" /><label></label></div>',
                            _l('name')
                        );
                        // Check if option show point relai is actived
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('type_livraison'));
                        }
                        array_push($columns, _l('type'), _l('status'), _l('delivery_note_number_of_delivery_notes'));
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('delivery_man_and_point_relai'));
                        } else {
                            array_push($columns, _l('delivery_man'));
                        }
                        array_push($columns, _l('date_created'), _l('staff'), _l('options'));

                        render_datatable($columns, 'delivery-notes');

                        ?>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="bonlivraison" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 97%; margin-top: 60px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>


                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('bon_livraison'); ?></span>
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <div class="row">
                    <div class="col-md-12">


                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane active" id="status">
                                <?= form_hidden('f-bonlivraison-id'); ?>
                                <?php
                                render_datatable(array(
                                    '',
                                    _l('name'),
                                    _l('client'),
                                    _l('sms_sent'),
                                 '',
                                  '',
                                    '',
                                    '',
                                    'Comment'

                                ), 'bonlivraison');

                                ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="bons-livraison">
                                <?= form_hidden('f-coli-id'); ?>
                                <?php
                                render_datatable(array(
                                    _l('id'),
                                    _l('name'),
                                    _l('delivery_note_type'),
                                    _l('delivery_note_number_of_delivery_notes'),
                                    _l('delivery_man'),
                                    _l('delivery_note_date_created'),
                                    _l('delivery_note_staff'),
                                    _l('options')
                                ), 'historiques-bons-livraison');

                                ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="appels">
                                <?php
                                render_datatable(array(
                                    _l('id'),
                                    _l('delivery_man'),
                                    _l('client'),
                                    _l('colis_list_code_barre'),
                                    _l('colis_list_date_created')
                                ), 'historiques-appels-livreur');

                                ?>
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
<script src="<?= site_url('assets/js/admin/bons-livraison/manage.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/bons-livraison/manage-batch.js?v=' . version_sources()); ?>"></script>

<script src="<?= site_url('assets/js/admin/bons-livraison/general.js?v=' . version_sources()); ?>"></script>
</body>
</html>

