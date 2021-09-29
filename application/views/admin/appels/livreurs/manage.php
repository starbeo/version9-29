<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <div class="col-md-4">
                                <?= render_select('f-livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_men'); ?>
                            </div>
                            <div class="col-md-4">
                                <?= render_select('f-client', $clients, array('id', array('nom')), 'client'); ?>
                            </div>
                            <div class="col-md-4">
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
                            _l('delivery_men'),
                            _l('client'),
                            _l('code_barre'),
                            _l('date_created')
                            ), 'appels-livreurs');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/appels/livreurs/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
