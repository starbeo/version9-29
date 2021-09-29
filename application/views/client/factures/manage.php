<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= render_select('f-type', $types, array('id', array('name')), 'type', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-statut', $statuses, array('id', array('name')), 'status', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-start', 'date_created_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-end', 'date_created_end', '', array(), array(), 'mbot10'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="factures"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="factures"><?= _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="javascript:void(0)" class="btn btn-default pull-right" onclick="toggle_small_view('.table-factures', '#facture');
                                return false;" data-toggle="tooltip" title="<?= _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-expand"></i></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-statistique mright5"><?= _l('factures_summary'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a>
                        <div id="statistique" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-muted no-margin"><?= _l('factures_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <a href="<?= client_url('factures'); ?>">
                                    <h2 class="bold"><?= total_rows('tblfactures', array('type' => 2, 'id_expediteur' => get_expediteur_user_id())); ?></h2>
                                    <span class="bold text-success mtop15 inline-block"><i class="fa fa-balance-scale"></i> <?= _l('invoices_delivred'); ?></span>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <a href="<?= client_url('factures'); ?>">
                                    <h2 class="bold"><?= total_rows('tblfactures', array('type' => 3, 'id_expediteur' => get_expediteur_user_id())); ?></h2>
                                    <span class="bold text-danger mtop15 inline-block"><i class="fa fa-balance-scale"></i> <?= _l('invoices_returned'); ?></span>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <a href="<?= client_url('factures'); ?>">
                                    <h2 class="bold"><?= total_rows('tblfactures', array('status' => 1, 'id_expediteur' => get_expediteur_user_id())); ?></h2>
                                    <span class="bold text-danger mtop15 inline-block"><i class="fa fa-balance-scale"></i> <?= _l('invoices_unpaid'); ?></span>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <a href="<?= client_url('factures'); ?>">
                                    <h2 class="bold"><?= total_rows('tblfactures', array('status' => 2, 'id_expediteur' => get_expediteur_user_id())); ?></h2>
                                    <span class="bold text-success mtop15 inline-block"><i class="fa fa-balance-scale"></i> <?= _l('invoices_paid'); ?></span>
                                </a>
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
                            _l('name'),
                            _l('total_crbt'),
                            _l('total_frais'),
                            _l('total_net'),
                            _l('number_of_colis'),
                            _l('invoice_type'),
                            _l('status'),
                            _l('date_created'),
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
<script>
    var hidden_columns = [5, 6, 7];
</script>
<?php init_tail_client(); ?>
<script src="<?= site_url('assets/js/client/factures/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
