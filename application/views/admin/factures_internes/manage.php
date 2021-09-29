<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12" id="small-table">
                <?php if (has_permission('factures_internes', '', 'create')) { ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <a href="<?= admin_url('factures_internes/facture'); ?>" class="btn btn-info mright5 pull-left display-block">
                                <?= _l('new_facture_interne'); ?></a>
                            <a href="#" class="btn btn-default pull-right" onclick="toggle_small_view('.table-factures-internes', '#facture_interne');
                                        return false;" data-toggle="tooltip" title="<?= _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-expand"></i></a>
                        </div>
                    </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <!-- if facture interne id found in url -->
                        <?= form_hidden('facture_interne_id', isset($facture_interne_id) ? $facture_interne_id : ''); ?>
                        <?php
                        render_datatable(array(
                            _l('id'),
                            _l('name'),
                            _l('facture_interne_total_received'),
                            _l('facture_interne_total_net'),
                            _l('facture_interne_rest'),
                            _l('facture_interne_number_of_factures'),
                            _l('facture_interne_date_created'),
                            _l('facture_interne_staff'),
                            _l('options')
                            ), 'factures-internes');

                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="facture_interne"></div>
                <?= loader_waiting_ajax('30%', '45%'); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var hidden_columns = [5, 6, 7, 8];
</script>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/factures-internes/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
