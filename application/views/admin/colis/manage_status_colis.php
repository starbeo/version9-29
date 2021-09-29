<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <?php if (is_admin()) { ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#status_colis_modal"><?= _l('new_status_colis'); ?></a>
                        </div>
                    </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('name'),
                            _l('color'),
                            _l('show_in_delivery_app'),
                            _l('options'),
                            ), 'status-colis');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="status_colis_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('status_colis_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('status_colis_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/colis/status'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('name', 'name'); ?>
                        <?= render_input('color', 'color', '', 'color'); ?>
                        <div class="checkbox checkbox-primary mtop0">
                            <input type="checkbox" name="show_in_delivery_app">
                            <label for="show_in_delivery_app"><?= _l('show_in_delivery_app'); ?></label>
                        </div>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/colis/manage_status_colis.js?v=' . version_sources()); ?>"></script>
</body>
</html>
