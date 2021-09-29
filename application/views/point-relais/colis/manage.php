<?php init_head_point_relais(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/point-relais/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= render_select('f-point-relai', $points_relais, array('id', array('nom')), 'point_relai', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-clients', $expediteurs, array('id', array('nom')), 'client', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-statut', $statuses, array('id', array('name')), 'status', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-etat', $etats, array('id', array('name')), 'colis_list_etat', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-ville', $cities, array('id', array('name')), 'city', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-ramassage-start', 'date_ramassage_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-ramassage-end', 'date_ramassage_end', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-livraison-start', 'date_livraison_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-livraison-end', 'date_livraison_end', '', array(), array(), 'mbot10'); ?>
                    <?= render_input_hidden('colis-facturer', 'colis-facturer'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="colis"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="colis"><?= _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left mright5 mbot5" data-toggle="modal" data-target="#colis"><?= _l('new_colis'); ?></a>
                        <a href="#" class="btn btn-default pull-right btn-statistique-colis"><?= _l('colis_summary'); ?></a>
                        <!--a href="#" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a-->
                        <div id="statistique-colis" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-success no-margin"><?= _l('colis_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', 'point_relai_id IN ' . $points_relais_staff); ?></h3>
                                <a href="#" onclick="dt_custom_view('', '.table-colis');
                                        return false;"><span class="bold"><?= _l('colis'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', 'status_reel = 100 AND point_relai_id IN ' . $points_relais_staff); ?></h3>
                                <a href="#" onclick="dt_custom_view(100, '.table-colis');
                                        return false;"><span class="bold"><?= _l('in_progress'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', 'status_reel = 101 AND point_relai_id IN ' . $points_relais_staff); ?></h3>
                                <a href="#" onclick="dt_custom_view(101, '.table-colis');
                                        return false;"><span class="bold"><?= _l('received'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', 'status_reel = 102 AND point_relai_id IN ' . $points_relais_staff); ?></h3>
                                <a href="#" onclick="dt_custom_view(102, '.table-colis');
                                        return false;"><span class="bold"><?= _l('received_by_the_delivery_man'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', 'status_reel = 2 AND point_relai_id IN ' . $points_relais_staff); ?></h3>
                                <a href="#" onclick="dt_custom_view(2, '.table-colis');
                                        return false;"><span class="bold"><?= _l('delivred'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', 'status_reel = 3 AND point_relai_id IN ' . $points_relais_staff); ?></h3>
                                <a href="#" onclick="dt_custom_view(3, '.table-colis');
                                        return false;"><span class="bold"><?= _l('returned'); ?></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?= form_hidden('shipping_cost_by_ville', get_option('shipping_cost_by_ville')); ?>
                        <?= form_hidden('custom_view'); ?>
                        <?php
                        $columns = array(
                            _l('id'),
                            _l('code_barre'),
                            _l('point_relais'),
                            _l('num_commande'),
                            _l('client'),
                            _l('phone_number'),
                            _l('city'),
                            _l('crbt'),
                            _l('colis_list_date_pickup'),
                            _l('date_livraison_ou_retour'),
                            _l('status'),
                            _l('colis_list_etat'),
                            _l('options'),
                        );
                        
                        render_datatable($columns, 'colis');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="colis" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(point_relais_url('colis/coli'), array('id' => 'form-colis')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('edit_colis'); ?></span>
                    <span class="add-title"><?= _l('new_colis'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
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
                        </div>
                        <?= render_select('id_expediteur', $expediteurs, array('id', array('nom')), 'als_expediteur'); ?>
                        <?= render_select('point_relai_id', $points_relais, array('id', array('nom')), 'point_relai'); ?>
                        <?= render_select('ville', $cities, array('id', array('name')), 'city', '', array(), array(), 'display-none'); ?>
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
                        <?= render_textarea('adresse', 'colis_list_adresse'); ?>
                        <?= render_textarea('commentaire', 'colis_list_comment'); ?>
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
<?php init_tail_point_relais(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/point-relais/colis/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
