<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>

            <?= form_open($this->uri->uri_string(), array('id' => 'bon-livraison-form')); ?>
            <div class="<?= $class1 ?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12 no-padding">
                            <h4 class="bold"><?= _l('detail_delivery_note'); ?></h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (isset($bon_livraison)) { ?>
                            <?= form_hidden('bonlivraison_id', $bon_livraison->id); ?>
                            <?= form_hidden('bonlivraison_status', $bon_livraison->status); ?>
                            <?= form_hidden('ids[]', $bon_livraison->id); ?>
                            <?php $value = (isset($bon_livraison) ? $bon_livraison->nom : ''); ?>
                            <div class="form-group">
                                <label class="control-label"><?= _l('name'); ?></label>
                                <div class="input-group">
                                    <input type="text" disabled class="form-control" value="<?= $value; ?>">
                                    <div class="input-group-addon">
                                        <?= render_btn_copy('input-nom-bl', 'name_of_delivery_note', '', 'input'); ?>
                                    </div>
                                </div>
                            </div>
                            <?= render_input_hidden('input-nom-bl', 'nom', $value); ?>
                        <?php } ?>

                        <?php
                        $classPointRelaiActive = '';
                        if (get_permission_module('points_relais') == 0) {
                            $classPointRelaiActive = 'display-none';
                        }

                        ?>
                        <?php
                        $attrSelect = array();
                        if (isset($bon_livraison)) {
                            $attrSelect = array('disabled' => 'disabled');
                        }
                        $showSelectLivreur = '';
                        $showSelectPointRelai = 'display-none';
                        if (isset($bon_livraison)) {
                            if ($bon_livraison->type_livraison == 'a_domicile') {
                                $showSelectLivreur = '';
                                $showSelectPointRelai = 'display-none';
                            } else {
                                $showSelectLivreur = 'display-none';
                                $showSelectPointRelai = '';
                            }
                        }

                        ?>
                        <?php $selected = (isset($bon_livraison) ? $bon_livraison->type_livraison : 'a_domicile'); ?>
                        <?= render_select('type_livraison', $types_livraison, array('id', array('name')), 'type_livraison', $selected, $attrSelect, array(), $classPointRelaiActive); ?>

                        <?php $selected = (isset($bon_livraison) ? $bon_livraison->id_livreur : ''); ?>
                        <?= render_select('id_livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man', $selected, $attrSelect, array('id' => 'bloc-select-livreur'), $showSelectLivreur); ?>

                        <?php $selected = (isset($bon_livraison) ? $bon_livraison->point_relai_id : ''); ?>
                        <?= render_select('point_relai_id', $points_relais, array('id', array('nom')), 'point_relai', $selected, $attrSelect, array('id' => 'bloc-select-point-relai'), $showSelectPointRelai); ?>

                        <?php $selected = (isset($bon_livraison) ? $bon_livraison->type : $type); ?>
                        <?= render_select('type', $types, array('id', array('name')), 'type', $selected, $attrSelect); ?>

                        <?php if (!isset($bon_livraison)) { ?>
                            <button id="submit" class="btn btn-primary" type="submit" style="width: 100%;"><?= _l('submit'); ?></button>
                        <?php } else { ?>
                            <input id="barcode-douchette" type="text" class="form-control" placeholder="Code d'envoi douchette">
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>

            <?php if (isset($bon_livraison)) { ?>
                <div class="<?= $class2 ?>">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4><?= _l('historical_delivery_note'); ?></h4>
                            <?php
                            render_datatable(array(
                                '',
                                _l('code_barre'),
                                _l('client'),
                                _l('city'),
                                _l('crbt'),
                                _l('colis_list_fresh'),
                                _l('colis_list_date_pickup'),
                                _l('colis_list_etat'),
                                _l('status')
                            ), 'historique-colis-bon-livraison');

                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <h4 class="bold"><?= _l('list_colis'); ?> :</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php init_colis_bon_livraison_table(); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/bons-livraison/bon.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/bons-livraison/general.js?v=' . version_sources()); ?>"></script>
<link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<style>
    .swal2-popup {
        display: none;
        position: relative;
        box-sizing: border-box;
        flex-direction: column;
        justify-content: center;
        width: 555px !important;
        padding: 1.25em;
        border: none;
        border-radius: 5px;
        background: #ffffff !important;
        font-family: inherit;
        font-size: 14px!important;
    }
</style>
</body>
</html>

