<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left mright5 mbot5" data-toggle="modal" data-target="#departement_modal"><?= _l('new_departement'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('name'),
                            _l('color'),
                            _l('staff'),
                            _l('date_created'),
                            _l('actions')
                            ), 'departements');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="departement_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('departement_edit'); ?></span>
                    <span class="add-title"><?= _l('departement_add'); ?></span>
                </h4>
            </div>
            <?= form_open(admin_url('departements/departement'), array('id' => 'from-departement')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('name', 'name'); ?>
                        <?= render_input('color', 'color', '', 'color'); ?>
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
<script src="<?= site_url('assets/js/admin/departements/manage.js'); ?>"></script>
</body>
</html>
