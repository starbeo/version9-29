<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?php
                    $classPointRelaiActive = '';
                    if (get_permission_module('points_relais') == 0) {
                        $classPointRelaiActive = 'display-none';
                    }

                    ?>
                    <div class="col-md-12 padding-0 <?= $classPointRelaiActive ?>">
                        <?= render_select('f-type-livraison', $types_livraison, array('id', array('name')), 'type_livraison', '', array(), array(), 'mbot10'); ?>
                    </div>
                    <div class="col-md-12 padding-0 <?= $classPointRelaiActive ?>">
                        <?= render_select('f-point-relai', $points_relais, array('id', array('nom')), 'point_relai', '', array(), array('id' => 'bloc-f-point-relai'), 'display-none mbot10'); ?>
                    </div>
                    <?= render_select('f-ville', $cities, array('id', array('name')), 'city', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-start', 'date_created_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-end', 'date_created_end', '', array(), array(), 'mbot10'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="colis-en-attente"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="colis-en-attente"><?= _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="javascript:void(0)" class="btn btn-info pull-left mright5 mbot5" data-toggle="modal" data-target="#colis-en-attente"><?= _l('new_colis_en_attente'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-statistique"><?= _l('colis_en_attente_summary'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a>
                        <div id="statistique" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12 mbot5">
                                <h3 class="text-success no-margin"><?= _l('colis_en_attente_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center">
                                <h3 class="bold"><?= total_rows('tblcolisenattente', 'tblcolisenattente.colis_id IS NULL AND id_expediteur = ' . get_expediteur_user_id()); ?></h3>
                                <a href="javascript:void(0)"><span class="text-muted bold"><?= _l('total'); ?></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?= render_input_hidden('prefix', '', get_prefix_client()); ?>
                        <?php
                        $columns = array(
                            _l('code_barre'),
                            _l('num_commande')
                        );
                        // Check if option show point relai is actived
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('type_livraison'));
                        }
                        array_push($columns, _l('fullname'), _l('phone_number'), _l('crbt'), _l('city'), _l('date_created'), _l('status'), _l('options'));
                        render_datatable($columns, 'colis-en-attente');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="colis-en-attente" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('edit_colis'); ?></span>
                    <span class="add-title"><?= _l('new_colis'); ?></span>
                </h4>
            </div>
            <?= form_open(client_url('colis_en_attente/coli'), array('id' => 'colis-en-attente-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <?= render_input_hidden('show_point_relai', '', get_permission_module('points_relais')); ?>
                    <div id="bloc-input-barcode" class="col-md-12 display-none">
                        <?= render_input('code_barre', 'code_barre'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_input('num_commande', 'num_commande'); ?>
                                <?= render_select('type_livraison', $types_livraison, array('id', array('name')), 'type_livraison', 'a_domicile', array(), array('id' => 'bloc-select-type-livraison-colis')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= render_input('crbt', 'colis_list_price', '', 'number'); ?>
                                <?= render_select('point_relai_id', $points_relais, array('id', array('nom')), 'point_relai', '', array(), array('id' => 'bloc-select-point-relai-colis')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= render_select('ville', $cities, array('id', array('name')), 'city', '', array(), array('id' => 'bloc-select-ville-colis')); ?>
                                <?= render_input('nom_complet', 'fullname'); ?>
                            </div>
                            <div class="col-md-6">
                                <?= render_select('quartier', array(), array(), 'quartier', '', array(), array('id' => 'bloc-select-quartier-colis')); ?>
                                <?= render_input('telephone', 'phone_number', '', 'text', array('data-format' => '0ddddddddd', 'placeholder' => '0600000000 // 0700000000'), array(), '', 'bfh-phone'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?= render_textarea('adresse', 'address', '', array('rows' => 2)); ?>
                    </div>
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
                    <div class="col-md-12">
                        <?= render_textarea('commentaire', 'comment', '', array('rows' => 2)); ?>
                    </div>
                    <?= form_hidden('id'); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit" type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/colis-en-attente/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>