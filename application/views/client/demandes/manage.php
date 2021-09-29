<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= render_select('f-priority', $priorities, array('id', array('name')), 'priority', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-status', $statuses, array('id', array('name')), 'status', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-start', 'date_created_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-end', 'date_created_end', '', array(), array(), 'mbot10'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="demandes"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="demandes"><?= _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= client_url('demandes/demande'); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_request'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right" onclick="toggle_small_view('.table-demandes', '#demande');
                                return false;" data-toggle="tooltip" title="<?= _l('toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-statistique mright5"><?= _l('requests_summary'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a>
                        <div id="statistique" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-muted no-margin"><?= _l('requests_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tbldemandes', array('client_id' => get_expediteur_user_id())); ?></h3>
                                <a href="javascript:void(0)"><span class="text-muted bold"><?= _l('total'); ?></span></a>
                            </div>
                            <?php foreach ($priorities as $priority) { ?>
                                <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                    <h3 class="bold"><?= total_rows('tbldemandes', array('priorite' => $priority['id'], 'client_id' => get_expediteur_user_id())); ?></h3>
                                    <a href="javascript:void(0)"><span class="text-<?= $priority['color_text']; ?> bold"><?= $priority['name']; ?></span></a>
                                </div>
                            <?php } ?>
                            <?php foreach ($statuses as $status) { ?>
                                <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                    <h3 class="bold"><?= total_rows('tbldemandes', array('status' => $status['id'], 'client_id' => get_expediteur_user_id())); ?></h3>
                                    <a href="javascript:void(0)"><span class="text-<?= $status['color_text']; ?> bold"><?= $status['name']; ?></span></a>
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
                        <?php
                        $table_data = array(
                            _l('name'),
                            _l('type'),
                            _l('departement'),
                            _l('object'),
                            _l('priority'),
                            _l('status'),
                            _l('rating'),
                            _l('date_created'),
                            _l('options')
                        );

                        render_datatable($table_data, 'demandes');

                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="demande"></div>
            </div>
        </div>
    </div>
</div>
<script>var hidden_columns = [2, 6, 7];</script>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/demandes/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>


