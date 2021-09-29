<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                           <!-- <a href="#" class="btn btn-danger pull-left mright5 mbot5" data-toggle="modal" data-target="#delete-colis-en-attente-by-date"><?= _l('delete_colis_en_attente_by_date'); ?></a>
-->			   
 <div class="btn-group pull-right" data-toggle="tooltip" title="Filtre colis en attente">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#" onclick="dt_custom_view('all', '.table-colis-en-attente');
                                                return false;">
                                               <?= _l('task_list_all'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="dt_custom_view('converted', '.table-colis-en-attente');
                                                return false;">
                                               <?= _l('colis_en_attente_converted_on_colis'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="dt_custom_view('not_converted', '.table-colis-en-attente');
                                                return false;">
                                               <?= _l('colis_en_attente_not_converted_on_colis'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?= form_hidden('shipping_cost_by_ville', get_option('shipping_cost_by_ville')); ?>
                        <?= form_hidden('custom_view', isset($custom_view) ? $custom_view : ''); ?>
                        <div class="clearfix"></div>
                        <?php
                        $columns = array(
                            _l('id'),
                            _l('code_barre'),
                            _l('num_commande'),
                            _l('client')
                        );
                        // Check if option show point relai is actived
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('type_livraison'));
                        }
                        array_push($columns, _l('phone_number'), _l('date_created'), _l('status'), _l('city'), _l('crbt'), _l('options'));
                        render_datatable($columns, 'colis-en-attente');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="colis" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('colis/coli'), array('id' => 'colis-en-attente-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span><?= _l('new_colis'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input_hidden('show_point_relai', '', get_permission_module('points_relais')); ?>
                        <?= form_hidden('en_attente'); ?>
                        <?= form_hidden('colis_en_attente_id'); ?>
                        <div class="row">
                            <div id="bloc-input-barcode" class="col-md-6 display-none">
                                <?= render_input('code_barre', 'code_barre'); ?>
                            </div>
                            <div class="col-md-6">
                                <?= render_input('num_commande', 'num_commande'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox checkbox-primary mtop0">
                                    <input type="checkbox" name="ouverture">
                                    <label for="ouverture"><?= _l('colis_opening'); ?></label>
                                </div>
                                <div class="checkbox checkbox-primary mtop0">
                                    <input type="checkbox" name="option_frais">
                                    <label for="option_frais"><?= _l('option_frais'); ?></label>
                                </div>
                                <div class="checkbox checkbox-primary mtop0">
                                    <input type="checkbox" name="option_frais_assurance">
                                    <label for="option_frais_assurance"><?= _l('option_frais_assurance'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= render_select('id_expediteur', $expediteurs, array('id', array('nom')), 'als_expediteur'); ?>
                            </div>
                            <div class="col-md-6">
                                <?= render_select('type_livraison', $types_livraison, array('id', array('name')), 'type_livraison', 'a_domicile', array(), array('id' => 'bloc-select-type-livraison-colis')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_select('ville', $cities, array('id', array('name')), 'city', '', array(), array('id' => 'bloc-select-ville-colis')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= render_select('quartier', $quartiers, array('id', array('name')), 'quartier', '', array(), array('id' => 'bloc-select-quartier-colis')); ?>
                            </div>
                        </div>
                        <?= render_select('livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-select-livreur-colis')); ?>
                        <?= render_select('point_relai_id', $points_relais, array('id', array('nom')), 'point_relai', '', array(), array('id' => 'bloc-select-point-relai-colis')); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_input('nom_complet', 'colis_list_name'); ?>
                                <?= render_input('crbt', 'colis_list_price'); ?>
                            </div>
                            <div class="col-md-6">
                                <?= render_input('telephone', 'colis_list_phone_number', '', 'text', array('placeholder' => '0600000000 // 0700000000 ')); ?>
                                <?= render_input('frais', 'colis_list_fresh'); ?>
                            </div>
                        </div>
                        <?= render_textarea('adresse', 'address'); ?>
                        <?= render_textarea('commentaire', 'comment'); ?>
                        <?= form_hidden('importer'); ?>
                        <?= form_hidden('id'); ?>
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
<!--
<div class="modal fade" id="delete-colis-en-attente-by-date" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('colis_en_attente/delete_colis_en_attente_by_date'), array('id' => 'delete-colis-en-attente-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('delete_colis_en_attente_by_date'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_date_input('start', 'start_date'); ?>
                        <?= render_date_input('end', 'end_date'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" data-url="<?= admin_url('colis_en_attente/export_colis_en_attente_by_date'); ?>" id="btn-export-excel" class="btn btn-success pull-left"><?= _l('export_excel'); ?></a>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-danger"><?= _l('delete'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>-->
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/colis-en-attente/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
