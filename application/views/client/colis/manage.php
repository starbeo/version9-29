<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= form_open(client_url('colis/export'), array('id' => 'form-export-colis')); ?>
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
                    <?= render_select('f-statut', $statuses, array('id', array('name')), 'status', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-etat', $etats, array('id', array('name')), 'colis_list_etat', '', array(), array(), 'mbot10'); ?>
                    <?= render_select('f-ville', $cities, array('id', array('name')), 'city', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-ramassage-start', 'date_ramassage_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-ramassage-end', 'date_ramassage_end', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-livraison-start', 'date_livraison_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-livraison-end', 'date_livraison_end', '', array(), array(), 'mbot10'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="colis"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="colis"><?= _l('reset'); ?></i></div>
                    <button id="export-colis" type="submit" class="btn btn-warning width100p mbot5"><?= _l('export'); ?></i></button>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                    <?= form_close(); ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-statistique mright5"><?= _l('colis_summary'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a>
                        <div id="statistique" class="row small-text-span pleft15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="text-success no-margin"><?= _l('colis_summary'); ?></h3>
                                </div>
                                <center>
                                    <span class="filter-statistics-colis-client" onclick="showModalFiltreStatistiqueDashbord();">
                                        <i class="fa fa-filter mright5 mbot5"></i>Filtre
                                    </span>
                                </center>
                                <div class="col-md-4">
                                    <ul class="list-group">
                                        <li class="list-group-item text-info bold">
                                            <?= _l('number'); ?>
                                        </li>
                                        <li class="list-group-item bold curp" onclick="dt_colis_by_statuses('')">
                                            <span class="badge"><?= total_rows('tblcolis', 'id_expediteur = ' . get_expediteur_user_id() . $wherePeriode1); ?></span>
                                            <?= _l('total'); ?>
                                        </li>
                                        <?php
                                        foreach ($statuses as $status) {
                                            if (in_array($status['id'], $statuses_hide)) {
                                                continue;
                                            }
                                            $where = $wherePeriode1;
                                            if ($status['id'] == 2) {
                                                $where = $wherePeriode2;
                                            } else if ($status['id'] == 3) {
                                                $where = $wherePeriode3;
                                            }

                                            ?>
                                            <li class="list-group-item bold curp" onclick="dt_colis_by_statuses(<?= $status['id']; ?>)">
                                                <span class="badge" style="background-color: <?= $status['color']; ?>"><?= total_rows('tblcolis', '(status_id = ' . $status['id'] . ' OR status_reel = ' . $status['id'] . ') AND id_expediteur = ' . get_expediteur_user_id() . $where); ?></span>
                                                <?= ucwords($status['name']); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-group">
                                        <li class="list-group-item text-info bold">
                                            <?= _l('sum_of_prices'); ?>
                                        </li>
                                        <li class="list-group-item bold">
                                            <span class="badge"><?= number_format(sum_from_table('tblcolis', array('field' => 'crbt'), 'id_expediteur = ' . get_expediteur_user_id() . $wherePeriode1), 2, ',', ' ') . ' Dhs'; ?></span>
                                            <?= _l('total'); ?>
                                        </li>
                                        <?php
                                        foreach ($statuses as $status) {
                                            if (!in_array($status['id'], $statuses_sum)) {
                                                continue;
                                            }
                                            $where = $wherePeriode1;
                                            if ($status['id'] == 2) {
                                                $where = $wherePeriode2;
                                            } else if ($status['id'] == 3) {
                                                $where = $wherePeriode3;
                                            }

                                            ?>
                                            <li class="list-group-item bold">
                                                <span class="badge" style="background-color: <?= $status['color']; ?>"><?= number_format(sum_from_table('tblcolis', array('field' => 'crbt'), '(status_id = ' . $status['id'] . ' OR status_reel = ' . $status['id'] . ') AND id_expediteur = ' . get_expediteur_user_id() . $where), 2, ',', ' ') . ' Dhs'; ?></span>
                                                <?= ucwords($status['name']); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-group">
                                        <li class="list-group-item text-info bold">
                                            <?= _l('sum_of_costs'); ?>
                                        </li>
                                        <li class="list-group-item bold">
                                            <span class="badge"><?= number_format(sum_from_table('tblcolis', array('field' => 'frais'), 'id_expediteur = ' . get_expediteur_user_id() . $wherePeriode1), 2, ',', ' ') . ' Dhs'; ?></span>
                                            <?= _l('total'); ?>
                                        </li>
                                        <?php
                                        foreach ($statuses as $status) {
                                            if (!in_array($status['id'], $statuses_sum)) {
                                                continue;
                                            }
                                            $where = $wherePeriode1;
                                            if ($status['id'] == 2) {
                                                $where = $wherePeriode2;
                                            } else if ($status['id'] == 3) {
                                                $where = $wherePeriode3;
                                            }

                                            ?>
                                            <li class="list-group-item bold">
                                                <span class="badge" style="background-color: <?= $status['color']; ?>"><?= number_format(sum_from_table('tblcolis', array('field' => 'frais'), '(status_id = ' . $status['id'] . ' OR status_reel = ' . $status['id'] . ') AND id_expediteur = ' . get_expediteur_user_id() . $where), 2, ',', ' ') . ' Dhs'; ?></span>
                                                <?= ucwords($status['name']); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        $columns = array(
                            _l('code_barre'),
                            _l('num_commande')
                        );
                        // Check if option show point relai is actived
                        if (get_permission_module('points_relais') == 1) {
                            array_push($columns, _l('type_livraison'));
                        }
                        array_push($columns, _l('fullname'), _l('phone_number'), _l('colis_list_crbt'), _l('city'), _l('colis_list_date_pickup'), _l('date_livraison_ou_retour'), _l('colis_list_etat'), _l('status'), _l('options'));
                        render_datatable($columns, 'colis');

                        ?>
                    </div>
                </div>
            </div>
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
                                    <a href="#appels" aria-controls="appels" role="tab" data-toggle="tab">
                                        <?= _l('appels'); ?>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <?= form_hidden('historique-count', 0); ?>
                                <div role="tabpanel" class="tab-pane active" id="status">
                                    <?= form_hidden('f-code-barre'); ?>
                                    <?php
                                    render_datatable(array(
                                        _l('code_barre'),
                                        _l('status'),
                                        _l('location'),
                                        _l('date_created')
                                        ), 'historiques-status');

                                    ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="appels">
                                    <?= form_hidden('f-coli-id'); ?>
                                    <?php
                                    render_datatable(array(
                                        _l('delivery_man'),
                                        _l('colis_list_code_barre'),
                                        _l('colis_list_date_created')
                                        ), 'historiques-appels-livreur');

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    //Affichage du modal filtre statistique
    include_once(APPPATH . 'views/client/dashboard/modal-filtre-statistique.php');

    ?>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/colis/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
