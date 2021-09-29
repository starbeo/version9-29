<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#quartier_livreur_modal"><?= _l('new_quartier_livreur'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('id'),
                            _l('quartier'),
                            _l('delivery_men'),
                            _l('options'),
                            ), 'quartiers-livreur');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="quartier_livreur_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('quartier_livreur_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('quartier_livreur_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/quartiers_livreur/quartier_livreur'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_select('quartier_id', $quartiers, array('id', array('name')), 'quartier'); ?>
                        <?= render_select('livreur_id', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man'); ?>
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
<script src="<?= site_url('assets/js/admin/quartiers-livreur/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
