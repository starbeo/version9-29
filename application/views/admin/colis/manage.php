<?php init_head(); ?>
<div id="wrapper">
    <div class="content">


        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= form_open(admin_url('colis/export_by_filter'), array('id' => 'form-export-colis')); ?>
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
                        <?php if (get_permission_module('points_relais') == 1) { ?>
                            <?= render_select('f-livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-f-livreur'), 'mbot10'); ?>
                        <?php } ?>
                        <?= render_select('f-point-relai', $points_relais, array('id', array('nom')), 'point_relai', '', array(), array('id' => 'bloc-f-point-relai'), 'display-none mbot10'); ?>
                    </div>
                    <?= render_select('f-clients', $expediteurs, array('id', array('nom')), 'client', '', array(), array(), 'mbot10'); ?>
                    <?php if (get_permission_module('points_relais') == 0) { ?>
                        <?= render_select('f-livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', '', array(), array('id' => 'bloc-f-livreur'), 'mbot10'); ?>
                    <?php } ?>    
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
                    <?php if (get_option('show_btn_export_excel_colis') == 1 && has_permission('colis', '', 'export')) { ?>
                        <button id="export-colis" type="submit" class="btn btn-success width100p mbot5"><?= _l('colis_export'); ?></i></button>
                    <?php } ?>
                    <?php if (get_option('show_btn_export_excel_colis_facturer') == 1 && has_permission('colis', '', 'export')) { ?>
                        <button id="export-colis-facturer" type="submit" class="btn btn-success width100p mbot5"><?= _l('colis_export_facture'); ?></i></button>
                    <?php } ?>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                    <?= form_close(); ?>
                </div>
            </div>

               <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('colis', '', 'create')) { ?>
                            <a href="#" class="btn btn-info pull-left mright5 mbot5" data-toggle="modal" data-target="#colis"><?= _l('new_colis'); ?></a>
                        <?php } ?>
                        <?php if(get_option('show_btn_export_excel_colis') == 1) { ?>
        				<a href="<?= admin_url('colis/export'); ?>" class="btn btn-success pull-left mright5 mbot5"><?= _l('colis_export'); ?></a>
        				<?php } ?>
        				<?php if(get_option('show_btn_export_excel_colis_facturer') == 1) { ?>
        				<a href="<?= admin_url('colis/export_colis_facture'); ?>" class="btn btn-success pull-left mright5 mbot5"><?= _l('colis_export_facture'); ?></a>
        				<?php } ?>
                        <a href="#" class="btn btn-default pull-right btn-statistique-colis"><?= _l('colis_summary'); ?></a>
                        <a href="#" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a>
                        <div id="statistique-colis" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-success no-margin"><?= _l('colis_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 4, 'etat_id' => 1, 'status_id' => 1)); ?></h3>
                                <a href="#" onclick="dt_custom_view(4, '.table-colis');
                                        return false;"><span class="text-success bold"><?= _l('status_colis_shipped'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center col-xs-6 border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 9, 'status_id' => 1)); ?></h3>
                                <a href="#" onclick="dt_custom_view(9, '.table-colis');
                                        return false;"><span class="text-danger bold"><?= _l('status_colis_refused'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 11, 'etat_id' => 1, 'status_id' => 1)); ?></h3>
                                <a href="#" onclick="dt_custom_view(11, '.table-colis');
                                        return false;"><span class="text-info bold"><?= _l('status_colis_postponed'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 10, 'etat_id' => 1, 'status_id' => 1)); ?></h3>
                                <a href="#" onclick="dt_custom_view(10, '.table-colis');
                                        return false;"><span class="text-warning bold"><?= _l('status_colis_cancelled'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 7, 'etat_id' => 1, 'status_id' => 1)); ?></h3>
                                <a href="#" onclick="dt_custom_view(7, '.table-colis');
                                        return false;"><span class="text-info bold"><?= _l('status_colis_unreachable'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center">
                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 6, 'etat_id' => 1, 'status_id' => 1)); ?></h3>
                                <a href="#" onclick="dt_custom_view(6, '.table-colis');
                                        return false;"><span class="text-primary bold"><?= _l('status_colis_no_answer'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 13, 'etat_id' => 1, 'status_id' => 1)); ?></h3>
                                <a href="#" onclick="dt_custom_view(13, '.table-colis');
                                        return false;"><span class="text-success bold"><?= _l('status_colis_in'); ?></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?= form_hidden('shipping_cost_by_ville', get_option('shipping_cost_by_ville')); ?>
                        <?= form_hidden('custom_view'); ?>
                        <?= form_hidden('bonlivraison'); ?>
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
                        array_push($columns, _l('phone_number'), _l('colis_list_date_pickup'), _l('status'), _l('colis_list_etat'), _l('date_livraison_ou_retour'), _l('city'), _l('crbt'), _l('options'));
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
        <?= form_open(admin_url('colis/coli'), array('id' => 'form-colis')); ?>
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
                        <?= render_input_hidden('show_point_relai', '', get_permission_module('points_relais')); ?>
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
                                <div class="checkbox checkbox-primary mtop0 ">
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
                            <div class="col-md-12">
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
<div class="modal fade" id="historiques" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 97%; margin-top: 60px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('historiques'); ?></span>
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#status" aria-controls="status" role="tab" data-toggle="tab">
                                    <?= _l('status'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#bons-livraison" aria-controls="bons-livraison" role="tab" data-toggle="tab">
                                    <?= _l('bon_livraison'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#appels" aria-controls="appels" role="tab" data-toggle="tab">
                                    <?= _l('appels'); ?>
                                </a>
                            </li>

                            <li role="colis info">
                                <a href="#colis-info" aria-controls="colis-info" role="tab" data-toggle="tab">
                                    <?= _l('colis'); ?>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <?= form_hidden('historique-count', 0); ?>
                            <div role="tabpanel" class="tab-pane active" id="status">
                                <?= form_hidden('f-code-barre'); ?>
                                       <?php
                                render_datatable(array(
                                    _l('id'),
                                    _l('code_barre'),
                                    _l('type'),
                                    _l('location'),
                                    _l('sms_sent'),
                                    _l('staff'),
                                    _l('date_created'),                             _l('date_reporte'),
  _l('options')
                                    ), 'historiques-status');

                                ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="bons-livraison">
                                <?= form_hidden('f-coli-id'); ?>
                                <?php
                                render_datatable(array(
                                    _l('id'),
                                    _l('name'),
                                    _l('delivery_note_type'),
                                    _l('delivery_note_number_of_delivery_notes'),
                                    _l('delivery_man'),
                                    _l('delivery_note_date_created'),
                                    _l('delivery_note_staff'),
                                    _l('options')
                                    ), 'historiques-bons-livraison');

                                ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="appels">
                                <?php
                                render_datatable(array(
                                    _l('id'),
                                    _l('delivery_man'),
                                    _l('client'),
                                    _l('colis_list_code_barre'),
                                    _l('colis_list_date_created')
                                    ), 'historiques-appels-livreur');

                                ?>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="colis-info">
                                <?php
                                render_datatable(array(
                                    _l('id'),
                                    _l('code_barre'),
                                    _l('num_commande'),
                                    _l('client'),
                                    _l('phone_number'),
                                    _l('colis_list_date_pickup'),
                                    _l('status'),
                                    _l('colis_list_etat'),

                                    _l('city'),
                                    _l('crbt')
                                ), 'historiques-coli-info');

                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statuspopup" tabindex="-1" role="dialog">
    <div class="modal-dialog">

        <?= form_open(admin_url('status/status'),'id="formstatu"'); ?>
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



<div id="demande" style="width: 50%; margin: auto; background-color: white; height: 80%;"  class="modal fade" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">


                            </div>
                        </div>
                    </div>

            </div>
</div>



        </div>
</div>

<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/colis/manage.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/status/manage-coli.js?v=' . version_sources()); ?>"></script>

<script>
    function init_demande(id) {

        $('#demande').load(admin_url + 'demandes/get_demande_data_coli_ajax/' + id);

        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            $('html, body').animate({
                scrollTop: $('#demande').offset().top + 150
            }, 600);
        }
    }
    // Init discussion
    function init_discussion(demandeId) {
        if (typeof (demandeId) === 'undefined') {
            demandeId = $('input[name="demande_id"]').val();
        }

        if ($.isNumeric(demandeId)) {
            $('#bloc-discussions-demande').html('');
            $.post(admin_url + 'demandes/discussions', {demande_id: demandeId}).success(function (response) {
                response = $.parseJSON(response);
                var discussions = '';
                $.each(response, function (i, obj) {
                    discussions += '<li class="feed-item">';
                    discussions += '<div class="date text-info"><i class="fa fa-clock-o mright5"></i>' + obj.date + '</div>';
                    discussions += '<div class="text">' + obj.profile_image + '<b>' + obj.name + '</b> : ' + obj.content + '</div>';
                    discussions += '</li>';
                });
                $('#bloc-discussions-demande').append(discussions);
                $('.table-demandes').DataTable().ajax.reload();
            });
        }
    }
</script>

</body>
</html>


