<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('demandes', '', 'create')) { ?>
                            <a href="<?= admin_url('demandes/demande'); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_request'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right" onclick="toggle_small_view('.table-demandes', '#demande');
                                return false;" data-toggle="tooltip" title="<?= _l('toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>
                        <a href="#" class="btn btn-default pull-right btn-statistique-requests mright5"><?= _l('requests_summary'); ?></a>
                        <a href="#" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <?= form_open(admin_url('demandes/export_by_filter'), array('id' => 'form-export-colis')); ?>

                            <div class="col-md-2">
                                <?= render_select('f-type', $types, array('id', array('name')), 'type'); ?>
                            </div>
                            <div class="col-md-4">
                                <?= render_select('f-objet', $objets, array('id', array('name')), 'objet'); ?>
                            </div>
                            <div class="col-md-2">
                                <?= render_select('f-departement', $departements, array('id', array('name')), 'departement'); ?>
                            </div>
                            <div class="col-md-2">
                                <?= render_select('f-client', $expediteurs, array('id', array('nom')), 'client'); ?>
                            </div>
                            <div class="col-md-2">
                                <?= render_select('f-priority', $priorities, array('id', array('name')), 'priority'); ?>
                            </div>
                            <div class="col-md-2">
                                <?= render_select('f-status', $statuses, array('id', array('name')), 'status'); ?>
                            </div>
                            <div class="col-md-2">
                                <?= render_date_input('f-date-created', 'date_created'); ?>

                            </div>
                            <div class="col-md-2">
                                <?= render_date_input('f-date-end', 'date_end'); ?>

                            </div>
                            <?php if (get_option('show_btn_export_excel_colis') == 1 && has_permission('colis', '', 'export')) { ?>
                                <button id="export-colis" type="submit" class="btn btn-success width100p mbot5">Exporter Excel Demandes</i></button>
                            <?php } ?>
                            <div class="col-md-2">
                                <?= form_close(); ?>

                            </div>
                            <div class="col-md-12 text-right">
                                <button id="filtre-submit" class="btn btn-primary"><?= _l('filter'); ?></button>
                                <button id="filtre-reset" class="btn btn-default"><?= _l('empty'); ?></i></button>
                            </div>
                        </div>

                        <div id="statistique-requests" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-success no-margin"><?= _l('requests_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tbldemandes'); ?></h3>
                                <a href="#"><span class="text-muted bold"><?= _l('total'); ?></span></a>
                            </div>
                            <?php foreach ($priorities as $priority) { ?>
                                <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                    <h3 class="bold"><?= total_rows('tbldemandes', array('priorite' => $priority['id'])); ?></h3>
                                    <a href="#"><span class="text-<?= $priority['color_text']; ?> bold"><?= $priority['name']; ?></span></a>
                                </div>
                            <?php } ?>
                            <?php foreach ($statuses as $status) { ?>
                                <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                    <h3 class="bold"><?= total_rows('tbldemandes', array('status' => $status['id'])); ?></h3>
                                    <a href="#"><span class="text-<?= $status['color_text']; ?> bold"><?= $status['name']; ?></span></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel_s animated fadeIn">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <!-- if demandeid found in url -->
                        <?= form_hidden('demandeid', $demandeid); ?>
                        <?= form_open(admin_url('demandes/change_status'), array('id' => 'change-status-demande-form')); ?>
                        <?php
                        $table_data = array(
                            '<div class="checkbox checkbox-primary"><input type="checkbox" id="checkbox-all-demandes" /><label></label></div>',
                            _l('name'),
                            _l('type'),
                            _l('client'),
                            _l('departement'),
                            _l('object'),
                            _l('priority'),
                            _l('status'),
                            _l('rating'),
                            _l('date_created'),
                            '<div class="checkbox checkbox-primary" style="display: none">messages</div>',

                            _l('options')

                        );

                        render_datatable($table_data, 'demandes');

                        ?>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="demande"></div>
            </div>
        </div>
    </div>
</div>
<script>var hidden_columns = [3, 8, 9, 10];</script>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/demandes/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>




