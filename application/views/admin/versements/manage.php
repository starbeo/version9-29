<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#versement_modal"><?= _l('new_versement'); ?></a>
                        <a href="#" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <div class="col-md-4">
                                <?= render_select('f-livreurs', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man'); ?>
                            </div>
                            <div class="col-md-4">
                                <?= render_select('f-utilisateur', $staff, array('staffid', array('firstname', 'lastname')), 'staff'); ?>
                            </div>
                            <div class="col-md-4">
                                <?= render_date_input('f-date-created', 'date_created'); ?>
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
                        $columns = array(
                            _l('id'),
                            _l('name')
                            );
                        // Check if option show point relai is actived
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('type_livraison'));
                        }
                        array_push($columns, _l('etat_colis_livrer'), _l('total'), _l('reference_transaction'));
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('delivery_man_and_point_relai'));
                        } else {
                            array_push($columns, _l('delivery_man'));
                        }
                        array_push($columns, _l('date_created'), _l('last_update_date'), _l('staff'), _l('options'));
                        render_datatable($columns, 'versements');
                        
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="versement_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('versement_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('versement_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/versements/versement', array('id' => 'form-versement')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input_hidden('show_point_relai', '', get_permission_module('points_relais')); ?>
                        <?= render_select('type_livraison', $types_livraison, array('id', array('name')), 'type_livraison', 'a_domicile', array(), array('id' => 'bloc-select-type-livraison-versement')); ?>
                        <?= render_select('livreur_id', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-select-livreur-versement')); ?>
                        <?= render_select('user_point_relais', $point_relais_users, array('staffid', array('firstname', 'lastname')), 'agent_point_relai', '', array(), array('id' => 'bloc-select-point-relai-versement')); ?>
                        <?= render_select('etat_colis_livre_id', array(), array(), 'etat_colis_livrer'); ?>
                        <div id="bloc_rest" class="form-group display-none">
                            <p class="bold">
                                <?= _l('rest'); ?> : 
                                <span id="rest" class="label label-danger"></span>
                            </p>
                        </div>
                        <?= render_input('total', 'amount_paid', '', 'number', array('min' => 0)); ?>
                        <?= render_input('reference_transaction', 'reference_transaction', ''); ?>
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
<script src="<?= site_url('assets/js/admin/versements/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
