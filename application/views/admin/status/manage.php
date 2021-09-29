<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('status', '', 'create')) { ?>
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#status"><?= _l('new_status'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right btn-filtre"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <div class="col-md-4">
                                <?= render_select('f-statut', $statuses, array('id', array('name')), 'status'); ?>
                            </div>
                            <div class="col-md-4">
                                <?= render_date_input('f-date-created', 'status_list_date_created'); ?>
                            </div>
                            <div class="col-md-12 text-right">
                                <button id="filtre-submit" class="btn btn-primary"><?= _l('filter'); ?></button>
                                <button id="filtre-reset" class="btn btn-default"><?= _l('empty'); ?></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('id'),
                            _l('code_barre'),
                            _l('status'),
                            _l('location'),
                            _l('sms_sent'),
                            _l('staff'),
                            _l('date_created'),
                            _l('date_reporte'),
                            _l('options')
                            ), 'status');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="status" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('status/status')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('edit_status'); ?></span>
                    <span class="add-title"><?= _l('new_status'); ?></span>
                </h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('code_barre_verifie', 'status_list_code_barre'); ?>
                        <?= render_select('type', $types, array('id', array('name')), 'status'); ?>
                        <?= render_select('emplacement_id', $locations, array('id', array('name')), 'location'); ?>
                        <div class="form-group display-none" id="date_reporte">
                            <label class="control-label"><?= _l('date_reporte'); ?></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                                <input type="date" name="date_reporte" id="date_reporte" class="form-control" >
                            </div>
                        </div>
                        <?= render_select('motif', $motifs, array('id', array('name')), 'status_motif', '', array(), array('id' => 'motif'), 'display-none'); ?>
                        <input type="hidden" name="id">
                        <input type="hidden" name="clientid">
                        <input type="hidden" name="coli_id">
                        <input type="hidden" name="telephone">
                        <input type="hidden" name="crbt">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit" type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/status/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>


