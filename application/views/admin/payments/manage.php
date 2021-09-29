<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
        </div>
        <div class="panel_s animated fadeIn">
            <div class="panel-body">
                <?= form_hidden('custom_view', $custom_view); ?>
                <?php
                render_datatable(array(
                    _l('payments_table_invoicenumber_heading'),
                    _l('payments_table_mode_heading'),
                    _l('payments_table_amount_heading'),
                    _l('payments_table_date_heading'),
                    _l('options')
                    ), 'payments');

                ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/payments/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>


