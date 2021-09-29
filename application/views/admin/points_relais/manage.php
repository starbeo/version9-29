<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('points_relais', '', 'create')) { ?>
                            <a href="<?= admin_url('points_relais/point_relai'); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_point_relais'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <div class="col-md-3">
                                <?= render_select('f-ville', $cities, array('id', array('name')), 'city'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('f-date-created', 'date_created'); ?>
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
                        <?php
                        render_datatable(array(
                            _l('societe'),
                            _l('name'),
                            _l('address'),
                            _l('city'),
                            _l('active'),
                            _l('staff'),
                            _l('date_created'),
                            _l('options')
                            ), 'points-relais');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/points-relais/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
