<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= admin_url('marketing/marketing'); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_marketing'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('name'),
                            _l('type'),
                            _l('notification_by'),
                            _l('sent'),
                            _l('staff_who_executed'),
                            _l('sending_date'),
                            _l('staff'),
                            _l('date_created'),
                            _l('options'),
                            ), 'marketing');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="historiques" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 97%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('historiques'); ?></span>
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <div class="row">
                    <div class="col-md-12">
                        <?= form_hidden('historique-count', 0); ?>
                        <?= form_hidden('f-marketing-id'); ?>
                        <?php
                        render_datatable(array(
                            _l('type'),
                            _l('sent'),
                            _l('client'),
                            _l('date_created')
                            ), 'historiques-marketing');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/marketing/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
