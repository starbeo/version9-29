<?php init_head_point_relais(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/point-relais/includes/alerts.php'); ?>
            <?= form_open($this->uri->uri_string(), array('id' => 'etat-colis-livrer-form')); ?>
            <div class="<?= $class1; ?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12 no-padding">
                            <h4 class="bold"><?= _l('detail_etat_colis_livrer'); ?></h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (isset($etat)) { ?>
                            <?= form_hidden('etat_id', $etat->id); ?>
                            <?= form_hidden('etat_colis_livrer_status', $etat->status); ?>
                            <?php $value = (isset($etat) ? $etat->nom : ''); ?>
                            <div class="form-group">
                                <label class="control-label"><?= _l('name'); ?></label>
                                <div class="input-group">
                                    <input type="text" disabled class="form-control" value="<?= $value; ?>">
                                    <div class="input-group-addon">
                                        <?= render_btn_copy('input-nom-etat', 'name_of_etat_colis_livrer', '', 'input'); ?>
                                    </div>
                                </div>
                            </div>
                            <?= render_input_hidden('input-nom-etat', 'nom', $value); ?>
                        <?php } ?>
                        
                        <?php
                        $attrSelect = array();
                        if (isset($etat)) {
                            $attrSelect = array('disabled' => 'disabled');
                        }
                        ?>
                        
                        <?php $selected = (isset($etat) ? $etat->user_point_relais : get_staff_user_id()); ?>
                        <?= render_select('user_point_relais', $point_relais_users, array('staffid', array('firstname', 'lastname')), 'agent_point_relai', $selected, $attrSelect); ?>
                        
                        <?php if (isset($etat)) { ?>
                            <?php $value = (isset($etat) ? $etat->total_received : '0.00'); ?>
                            <?= render_input('total_received', 'total_versement', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-received-facture'); ?>

                            <?php $value = (isset($etat) ? $etat->commision : '0.00'); ?>
                            <?= render_input('commision', 'total_commision', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-commision'); ?>

                            <?php $value = (isset($etat) ? $etat->total : '0.00'); ?>
                            <?= render_input('total', 'facture_interne_total', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-total-facture'); ?>

                            <?php $value = (isset($etat) ? $etat->manque : '0.00'); ?>
                            <?= render_input('manque', 'rest', $value, 'number', array('step' => 'any', 'disabled' => 'disabled'), array(), '', 'input-rest-facture'); ?>

                            <?php $value = (isset($etat) ? $etat->justif : ''); ?>
                            <?php $display = (is_null($etat->justif) ? 'display-none' : ''); ?>
                            <?= render_textarea('justif', 'justification', $value, array(), array(), $display); ?>

                            <p class="bold"><i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> (Avant de quitter cette page il faut enregistrer le calcule du TOTAL.)</p>
                        <?php } ?>

                        <button id="submit" class="btn btn-primary" type="submit" style="width: 100%;"><?= _l('submit'); ?></button>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>

            <?php if (isset($etat)) { ?>
                <div class="<?= $class2; ?>">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <h4 class="bold"><?= _l('list_colis'); ?> :</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php
                            render_datatable(array(
                                '',
                                _l('code_barre'),
                                _l('client'),
                                _l('crbt'),
                                _l('cost'),
                                _l('date_pickup'),
                                _l('delivery_date'),
                                _l('state'),
                                _l('status')
                                ), 'items-etat-colis-livrer');

                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (isset($etat)) { ?>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="bold"><?= _l('historical_etat_colis_livrer'); ?></h4>
                                </div>
                                <div class="col-md-6 text-right">
                                    <h4 class="bold"><?= _l('total'); ?> : <span class="total_table text-primary"></span></h4>
                                </div>
                            </div>
                            <?php
                            render_datatable(array(
                                '',
                                _l('code_barre'),
                                _l('client'),
                                _l('crbt'),
                                _l('cost'),
                                _l('date_pickup'),
                                _l('delivery_date'),
                                _l('state'),
                                _l('status')
                                ), 'historique-items-etat-colis-livrer');

                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<?php init_tail_point_relais(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/point-relais/etats-colis-livrer/etat.js?v=' . version_sources()); ?>"></script>
</body>
</html>