<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <?php if (has_permission('shipper', '', 'create')) { ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <a href="<?= admin_url('expediteurs/expediteur'); ?>" class="btn btn-info mright5 pull-left display-block">
                                <?= _l('new_expediteur'); ?></a>
                        </div>
                    </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('id'),
                            _l('name'),
                            _l('contact'),
                            _l('email'),
                            _l('phone_number'),
                            _l('city'),
                            _l('code_parrainage'),
                            _l('affiliation_code'),
                            _l('total_frais_parrainage'),
                            _l('date_created'),
                            _l('active'),
                            _l('options')
                            ), 'expediteurs');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/expediteurs/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
