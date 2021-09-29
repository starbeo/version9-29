<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <?= form_open($this->uri->uri_string(), array('id' => 'facture-interne-form')); ?>
            <div class="col-md-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12 no-padding">
                            <h4><b><?= _l('detail_facture_interne'); ?></b></h4>
                        </div>
                    </div>
                    <div class="panel-body">


                        <?php if (isset($facture)) { ?>
                            <?= form_hidden('facture_id', $facture->id); ?>
                            <?php $value = (isset($facture) ? $facture->nom : ''); ?>
                            <div class="form-group">
                                <label class="control-label"><?= _l('name'); ?></label>
                                <input type="text" disabled class="form-control" value="<?= $value; ?>">
                            </div>
                            <?= form_hidden('nom', $value); ?>

                            <?php $value = (isset($facture) ? $facture->total : '0.00'); ?>
                            <?= render_input('total', 'facture_interne_total_crbt', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-facture-blue'); ?>

                            <?php $value = (isset($facture) ? $facture->total_frais : '0.00'); ?>
                            <?= render_input('total_frais', 'facture_interne_total_fresh', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-facture-blue'); ?>

                            <?php $value = (isset($facture) ? $facture->total_refuse : '0.00'); ?>
                            <?= render_input('total_refuse', 'total_refuse', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-facture-blue'); ?>

                            <?php $value = (isset($facture) ? $facture->total_parrainage : '0.00'); ?>
                            <?= render_input('total_parrainage', 'total_parrainage', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-facture-blue'); ?>

                            <?php $value = (isset($facture) ? $facture->total_remise : '0.00'); ?>
                            <?= render_input('total_remise', 'facture_interne_total_discount', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-facture-blue'); ?>

                            <?php $value = (isset($facture) ? $facture->total_net : '0.00'); ?>
                            <?= render_input('total_net', 'facture_interne_total_net', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-facture-blue'); ?>

                            <?php $value = (isset($facture) ? $facture->total_received : '0.00'); ?>
                            <?= render_input('total_received', 'facture_interne_total_received', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-received-facture'); ?>

                            <?php $value = (isset($facture) ? $facture->rest : '0.00'); ?>
                            <?= render_input('rest', 'facture_interne_rest', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-rest-facture'); ?>

                            <?php $value = (isset($facture) ? $facture->motif : ''); ?>
                            <?php
                            $display = (is_null($facture->motif) ? 'display-none' : '');
                            if (is_null($facture->motif) && $facture->rest < 0) {
                                $display = '';
                            }

                            ?>
                            <?= render_textarea('motif', 'facture_interne_motif', $value, array(), array(), $display); ?>

                            <p class="bold"><i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> (Avant de quitter cette page il faut enregistrer le calcule du TOTAL.)</p>

                        <?php } ?>

                        <button id="submit" class="btn btn-primary" type="submit" style="width: 100%;"><?= _l('submit'); ?></button>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>

            <?php if (isset($facture)) { ?>
                <div class="col-md-9">
                    <div class="panel_s">
                        <div class="panel-body">

                            <div class="col-md-12">
                                <h4><?= _l('list_factures'); ?> : <b></h4>
                            </div>

                            <div class="row">

                                <button id="submit_f_i" class="btn btn-primary pull-right" type="submit"><?= _l('submit'); ?></button>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-primary">
                                            <input class="check_all_product_checked" type="checkbox">
                                            <label>Sélectionner tous les factures</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-primary">
                                            <input class="uncheck_all_product_checked" type="checkbox">
                                            <label>Désélectionner tous les Factures</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php init_items_facture_interne_table(); ?>
                            <?= loader_waiting_ajax('2%', '45%'); ?>
                            <div id="checked-products"></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php $value = (isset($facture) ? count($facture->items) : 0); ?>
            <input type="hidden" id="nbr_colis_selected" value="<?= $value ?>">
            <input type="hidden" id="alert_colis_selected" value="0">

            <?php if (isset($facture)) { ?>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><b><?= _l('historical_facture_interne'); ?></b></h4>
                                </div>
                                <div class="col-md-6 text-right">
                                    <h4><b><?= _l('total'); ?> : <span class="total_table text-primary"></span></b></h4>
                                </div>
                            </div>
                            <?php
                            render_datatable(array(
                                _l('name'),
                                _l('total_net'),
                                _l('invoice_type'),
                                _l('status'),
                                _l('facture_interne_date_created'),
                                _l('facture_interne_client'),
                                _l('options')
                                ), 'historique-items-facture-interne');

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
<script src="<?= site_url('assets/js/admin/factures-internes/facture-interne.js?v=' . version_sources()); ?>"></script>
</body>
</html>

