<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#shipping_cost_modal"><?= _l('new_shipping_cost'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('name'),
                            _l('shipping_cost'),
                            _l('date_created'),
                            _l('staff'),
                            _l('options'),
                            ), 'shipping-cost');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="shipping_cost_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('shipping_cost_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('shipping_cost_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/shipping_cost/shipping_cost', array('id' => 'form-shipping-cost')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('name', 'name'); ?>
                        <?= render_input('shipping_cost', 'shipping_cost', '', 'number'); ?>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/shipping-cost/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
