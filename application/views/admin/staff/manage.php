<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <?php if (has_permission('staff', '', 'create')) { ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <a href="<?= admin_url('staff/member/false/' . $type); ?>" class="btn btn-info pull-left display-block"><?= _l('new_staff'); ?></a>
                        </div>
                    </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        $table_data = array(
                            _l('id'),
                            _l('staff_dt_name'),
                            _l('role'),
                            _l('staff_dt_email'),
                            _l('staff_dt_phone_number'),
                            _l('staff_dt_active'),
                            _l('options')
                        );

                        render_datatable($table_data, 'staff');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/staff/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
