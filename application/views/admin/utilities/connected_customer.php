<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        render_datatable(array(
                            _l('id'),
                            _l('connected_customer_dt_client'),
                            _l('utility_activity_log_dt_date'),
                            _l('utility_activity_log_dt_ip'),
                            ), 'connected-customer');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/utilities/connected-customer.js?v=' . version_sources()); ?>"></script>
</body>
</html>
