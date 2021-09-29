<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('etat_colis_livrer', '', 'create')) { ?>
                            <a href="<?= admin_url('etat_colis_livrer/etat'); ?>" class="btn btn-info pull-left">
                                <?= _l('new_etat_colis_livrer'); ?></a>
                        <?php } ?>
                        <?php if (has_permission('etat_colis_livrer', '', 'export')) { ?>
                            <a href="#" class="btn btn-default pull-right btn-upload-etat-colis-livrer"><?= _l('export'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div class="panel-body display-none mtop40 " id="upload-etat-colis-livrer">
                            <?= form_open(admin_url('etat_colis_livrer/export'), array('id' => 'upload-etat-colis-livrer-form')); ?>
                            <div class="col-md-3">
                                <?= render_select('delivery_men', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man'); ?>
                            </div>    
                            <div class="col-md-3">
                                <?= render_date_input('date_start', 'upload_etat_colis_livrer_date_start'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('date_end', 'upload_etat_colis_livrer_date_end'); ?>
                            </div>
                            <div class="col-md-3">
                                <button id="btn-submit-export-excel" type="submit" class="btn btn-success btn-submit-export mtop25"><?= _l('export_excel'); ?></button>
                                <button id="btn-submit-export-pdf" type="submit" class="btn btn-danger btn-submit-export mtop25"><?= _l('export_pdf'); ?></button>
                            </div>
                            <?= form_close(); ?>
                        </div>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <?php
                            $classPointRelaiActive = '';
                            if (get_permission_module('points_relais') == 0) {
                                $classPointRelaiActive = 'display-none';
                            }

                            ?>
                            <div class="col-md-3 <?= $classPointRelaiActive ?>">
                                <?= render_select('f-type-livraison', $types_livraison, array('id', array('name')), 'type_livraison'); ?>
                            </div>
                            <div class="col-md-3 <?= $classPointRelaiActive ?>">
                                <?= render_select('f-user-point-relais', $point_relais_users, array('staffid', array('firstname', 'lastname')), 'agent_point_relai', '', array(), array('id' => 'bloc-f-point-relai'), 'display-none'); ?>
                                <?php if (get_permission_module('points_relais') == 1) { ?>
                                <?= render_select('f-livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-f-livreur')); ?>
                                <?php } ?>
                            </div>
                            <?php if (get_permission_module('points_relais') == 0) { ?>
                                <div class="col-md-3">
                                    <?= render_select('f-livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-f-livreur')); ?>
                                </div>
                            <?php } ?>
                            <div class="col-md-3">
                                <?= render_select('f-utilisateur', $staff, array('staffid', array('firstname', 'lastname')), 'staff'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('f-date-created', 'etat_colis_livrer_date_created'); ?>
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
                        <?= form_open(admin_url('etat_colis_livrer/change_status'), array('id' => 'change-status-etat-colis-livrer-form')); ?>
                        <?php
                        $columns = array(
                            '<div class="checkbox checkbox-primary"><input type="checkbox" id="checkbox-all-etat" /><label></label></div>',
                            _l('name')
                        );
                        // Check if option show point relai is actived
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('type_livraison'));
                        }
                        array_push($columns, _l('total'), _l('total_versement'), _l('commision'), _l('rest'), _l('number_of_colis'),_l('number_colis_livre'),_l('number_colis_refuse'), _l('status'), _l('state'));
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('delivery_man_and_point_relai'));
                        } else {
                            array_push($columns, _l('delivery_man'));
                        }
                        array_push($columns, 'Référence CML', _l('etat_colis_livrer_staff'), _l('options'));
                        
                        render_datatable($columns, 'etat-colis-livrer');

                        ?>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="historique_versements" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 97%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('payments_history'); ?></span>
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <div class="row">
                    <div class="col-md-12">
                        <?= form_hidden('f-etat-colis-livrer'); ?>
                        <?= form_hidden('historique-versements-count', 0); ?>
                        <?php
                        render_datatable(array(
                            _l('id'),
                            _l('name'),
                            _l('etat_colis_livrer'),
                            _l('total'),
                            _l('reference_transaction'),
                            _l('delivery_man'),
                            _l('date_created'),
                            _l('last_update_date'),
                            _l('staff')
                            ), 'historiques-versements');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/etat-colis-livrer/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>


