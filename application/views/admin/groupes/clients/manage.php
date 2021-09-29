<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left mright5" data-toggle="modal" data-target="#groupe_modal"><?= _l('new_groupe'); ?></a>
                        <a href="#" class="btn btn-success pull-left" data-toggle="modal" data-target="#group_to_customer_modal"><?= _l('group_assignment_to_customers'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('name'),
                            _l('staff'),
                            _l('date_created'),
                            _l('options'),
                            ), 'groupes');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="groupe_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('groupe_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('groupe_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/groupes/groupe'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('name', 'name'); ?>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="group_to_customer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?= _l('group_assignment_to_customers'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/groupes/affectation', array('id' => 'form-affectation-group-to-customer')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_select('groupe', $groupes, array('id', array('name')), 'groupe'); ?>
                        <?= render_select('clients[]', $clients, array('id', array('nom')), 'client', '', array('multiple' => true)); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit-form-affectation-group-to-customer" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/groupes/clients/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
