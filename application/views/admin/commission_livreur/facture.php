<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>

            <?php if (isset($facture)) { ?>
                <?= form_hidden('facture_id', $facture->id); ?>
                <?= form_hidden('id_expediteur', $facture->id_livreur); ?>

            <?php } ?>
            <?= form_open($this->uri->uri_string(), array('id' => 'facture-form')); ?>
            <div class="col-md-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-9 no-padding">
                            <h4><b> les détailles de votre livraison</b></h4>
                        </div>
                        <?php if (!isset($facture)) { ?>
                            <div class="col-md-3 no-padding">
                                <a href="#" class="btn btn-primary pull-right mtop5" onclick="init_facture()"><i class="fa fa-refresh"></i></a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="panel-body">
                        <?php if (isset($facture)) { ?>
                            <?= form_hidden('isedit'); ?>
                            <?php $value = (isset($facture) ? $facture->nom : ''); ?>
                            <div class="form-group">
                                <label class="control-label"><?= _l('name'); ?></label>
                                <div class="input-group">
                                    <input type="text" disabled class="form-control" value="<?= $value; ?>">
                                    <div class="input-group-addon">
                                        <?= render_btn_copy('input-nom-facture', 'invoice_name', '', 'input'); ?>
                                    </div>
                                </div>
                            </div>
                            <?= render_input_hidden('input-nom-facture', 'nom', $value); ?>
                        <?php } ?>

                        <?php $selected = (isset($facture) ? $facture->id_livreur : '' ); ?>
                        <?php if (!isset($facture)) { ?>
                            <?= render_select('expediteurs', $expediteurs, array('staffid', array('nomeliv')), 'delivery_man', $selected); ?>
                        <?php } else { ?>
                            <?php echo $selected;  ?>
                            <?= render_select('expediteurs', $expediteurs, array('staffid', array('nomeliv')), 'delivery_man',$selected , array('disabled' => 'disabled')); ?>
                        <?php } ?>
                        <?= form_hidden('id_expediteur', $selected); ?>

                        <?php $selected = (isset($facture) ? $facture->id_utilisateur : $type); ?>
                        <?php if (!isset($facture)) { ?>
                            <?= render_select('types', $types, array('id', array('name')), 'invoice_type', $selected); ?>
                        <?php } else { ?>
                            <?= render_select('types', $types, array('id', array('name')), 'invoice_type', $selected, array('disabled' => 'disabled')); ?>
                        <?php } ?>
                        <?= form_hidden('type', $selected); ?>
                        <?php if (isset($facture)) { ?>

                            <?php $value = (isset($facture) ? $facture->total_frais : '0.00'); ?>
                            <?= render_input('total_frais', 'total_frais_et', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-received-facture'); ?>

                            <?php $value = (isset($facture) ? $facture->total_refuse : '0.00'); ?>
                            <?= render_input('total_refuse', 'total_refuse_et', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-rest-facture'); ?>

                            <?php $value = (isset($facture) ? $facture->total_manque : '0.00'); ?>
                            <?= render_input('total_manque', 'total_manque', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-received-facture'); ?>

                            <?php $value = (isset($facture) ? $facture->total_line : '0.00'); ?>
                            <?= render_input('total', 'total', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-rest-facture'); ?>

                            <button id="submit" class="btn btn-primary pull-right" type="submit"><?= _l('submit'); ?></button>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-6">
                            <h4><b>Liste ECL : </b></h4>
                        </div>
                        <div class="col-md-6">
                            <?php if (isset($facture)) { ?>
                                <button id="submit" class="btn btn-primary pull-right" type="submit"><?= _l('affect'); ?></button>
                            <?php } else { ?>
                                <button id="submit" class="btn btn-primary pull-right" type="submit"><?= _l('submit'); ?></button>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body">

     <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input class="check_all_product_checked" type="checkbox">
                                        <label>Sélectionner tous les ECL</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input class="uncheck_all_product_checked" type="checkbox">
                                        <label>Désélectionner tous les ECL</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php init_etat_facture_table(); ?>
                        <?= loader_waiting_ajax('2%', '45%'); ?>
                        <div id="checked-products"></div>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>

            <?php $value = (isset($facture) ? count($facture->items) : 0); ?>
            <input type="hidden" id="nbr_colis_selected" value="<?= $value ?>">
            <input type="hidden" id="alert_colis_selected" value="0">

            <?php if (isset($facture)) { ?>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4><b> Historique ECL </b></h4>
                            <?php
                            render_datatable(array(
                                '',
                                _l('name'),
                                _l('total'),
                              
                                _l('etat_colis_livrer_date_created'),
                                _l('number_of_colis'),
                                _l('number_colis_livre'),
                                 'Commission Livré',
                                _l('number_colis_refuse'),
                                'Commission Refuse',
                            ), 'historique-colis-factures');

                            ?>
                            <?= loader_waiting_ajax('5%', '45%'); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/commission_livreur/facture.js?v=' . version_sources()); ?>"></script>
</body>
</html>

