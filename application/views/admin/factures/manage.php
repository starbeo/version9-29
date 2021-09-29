<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('invoices', '', 'create')) { ?>
                            <a href="<?= admin_url('factures/facture/false/' . $type); ?>" class="btn btn-info mright5 pull-left display-block">
                                <?= _l('new_facture'); ?></a>
                            <div class="btn btn-info mright5 pull-left display-block" data-toggle="modal" data-target="#batch_modal" data-type="<?= $type ?>"><?= _l('batch_invoice'); ?></div>
                        <?php } ?>
                        <?php if (get_option('show_btn_export_excel_factures') == 1 && has_permission('invoices', '', 'export')) { ?>
                            <a href="<?= admin_url('factures/export'); ?>" class="btn btn-success pull-left">Exporter Excel Factures</a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right" onclick="toggle_small_view('.table-factures', '#facture');
                                return false;" data-toggle="tooltip" title="<?= _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-expand"></i></a>
                        <a href="#" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <div class="col-md-3">
                                <?= render_select('f-clients', $expediteurs, array('id', array('nom')), 'client'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_select('f-statut', $statuses, array('id', array('name')), 'status'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_select('f-utilisateur', $staff, array('staffid', array('firstname', 'lastname')), 'staff'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('f-date-created', 'status_list_date_created'); ?>
                            </div>
                            <div class="col-md-12 text-right">
                                <button id="filtre-submit" class="btn btn-primary"><?= _l('filter'); ?></button>
                                <button id="filtre-reset" class="btn btn-default"><?= _l('empty'); ?></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <!-- if invoiceid found in url -->
                        <?= form_hidden('invoiceid', isset($invoiceid) ? $invoiceid : ''); ?>
                        <?php
                        render_datatable(array(
                            _l('id'),
                            _l('name'),
                            _l('expediteur'),
                            _l('total_crbt'),
                            _l('total_frais'),
                            _l('total_net'),
                            _l('number_of_colis'),
                            _l('invoice_type'),
                            _l('status'),
                            _l('date_created'),
                            _l('staff'),
                            _l('options')
                            ), 'factures');

                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="facture"></div>
                <?= loader_waiting_ajax('35%', '45%'); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal animated fadeIn" id="commentaire_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('invoice_comment_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('invoice_comment_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/factures/commentaire', array('id' => 'comment')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_textarea('commentaire', 'comment'); ?>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal animated fadeIn" id="add_line_additionnal_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?= _l('add_additionnal_line_invoice'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/factures/add_additionnal_line', array('id' => 'add-additionnal-line-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('description_line', 'description'); ?>
                        <?= render_input('total_line', 'total', '', 'number', array('min' => 0)); ?>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

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
                        <?= render_select('type_livraison', $types_livraison, array('id', array('name')), 'type_livraison', 'a_domicile'); ?>

                    </div>
                </div>
                <?= render_select('type', $types, array('id', array('name')), 'invoice_type', '', array('disabled' => 'disabled')); ?>

                <div class="row">
                    
                    <div class="col-md-6">
                        <?= render_date_input('start_date', 'colis_date_start'); ?>
                        <?= render_select('expediteurs', array(), array(), 'als_expediteur', '', array('multiple' => true)); ?>
                    </div>
                    <div class="col-md-6">
                        <?= render_date_input('end_date', 'colis_date_end'); ?>


                    </div>
                    <div class="col-md-6">
                        <?= render_select('select_type', array(
                            array('id' => 'selectmulti', 'name' => 'ajoute tous les clients'),
                            array('id' => 'selectone', 'name' => 'par clients')
                        ), array('id', array('name')), 'Type de Sélection', 'a_domicile'); ?>


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

<script>
    var hidden_columns = [3, 4, 5, 11];
</script>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/factures/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>


