<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= admin_url('sliders/slider'); ?>" class="btn btn-info pull-left mright5 mbot5"><?= _l('new_slider'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('name'),
                            _l('slider'),
                            _l('active'),
                            _l('staff'),
                            _l('date_created'),
                            _l('actions')
                            ), 'sliders');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/sliders/manage.js'); ?>"></script>
</body>
</html>
