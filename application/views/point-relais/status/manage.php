<?php init_head_point_relais(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/point-relais/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?=  _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?=  _l('filter') ?></h3>
                    <?=  render_select('f-statut', $statuses, array('id', array('name')), 'status'); ?>
                    <?=  render_date_input('f-date-created', 'date_created'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="status"><?=  _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="status"><?=  _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?=  _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#status"><?=  _l('new_status'); ?></a>
                        <a href="#" class="btn btn-default pull-right btn-filter"><?=  _l('filter'); ?></a>
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
                            _l('staff'),
                            _l('date_created'),
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
        <?=  form_open(point_relais_url('status/status')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?=  _l('edit_status'); ?></span>
                    <span class="add-title"><?=  _l('new_status'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?=  render_input('code_barre_verifie', 'status_list_code_barre'); ?>
                        <?=  render_select('type', $types, array('id', array('name')), 'status'); ?>
                        <?=  render_select('emplacement_id', $locations, array('id', array('name')), 'location'); ?>
                        <div class="form-group display-none" id="date_reporte">
                            <label class="control-label"><?=  _l('date_reporte'); ?></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                                <input type="date" name="date_reporte" id="date_reporte" class="form-control" >
                            </div>
                        </div>
                        <?=  render_select('motif', $motifs, array('id', array('name')), 'status_motif', '', array(), array('id' => 'motif'), 'display-none'); ?>
                        <input type="hidden" name="id">
                        <input type="hidden" name="clientid">
                        <input type="hidden" name="coli_id">
                        <input type="hidden" name="telephone">
                        <input type="hidden" name="crbt">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=  _l('close'); ?></button>
                <button id="submit" type="submit" class="btn btn-primary"><?=  _l('submit'); ?></button>
            </div>
        </div>
        <?=  form_close(); ?>
    </div>
</div>
<?php init_tail_point_relais(); ?>
<!-- JS -->
<script src="<?=  site_url('assets/js/point-relais/status/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
