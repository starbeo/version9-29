<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                    <?= _l('client_add_edit_profile'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#frais" aria-controls="frais" role="tab" data-toggle="tab">
                                    <?= _l('client_add_edit_frais'); ?>
                                </a>
                            </li>
                            <?php if (isset($expediteur)) { ?>
                                <?php if (has_permission('colis', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#colis" aria-controls="colis" role="tab" data-toggle="tab">
                                            <?= _l('list_colis_expediteur'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('colis_en_attente', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#colis-en-attente" aria-controls="colis-en-attente" role="tab" data-toggle="tab">
                                            <?= _l('list_colis_en_attente_expediteur'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('bon_livraison', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#bon_livraison" aria-controls="bon-livraison" role="tab" data-toggle="tab">
                                            <?= _l('delivery_notes'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('invoices', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#factures" aria-controls="factures" role="tab" data-toggle="tab">
                                            <?= _l('als_factures'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('claim_shipper', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#reclamations" aria-controls="reclamations" role="tab" data-toggle="tab">
                                            <?= _l('als_reclamations'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li role="presentation">
                                    <a href="#activiti_log_customer" aria-controls="activiti_log_customer" role="tab" data-toggle="tab">
                                        <?= _l('utility_activity_log'); ?>
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#chart" aria-controls="chart" role="tab" data-toggle="tab">
                                        <?= _l('chart'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="panel-body">
                        <?= form_open($this->uri->uri_string(), array('id' => 'expediteur-form')); ?>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="profile">
                                <div class="col-md-4">
                                    <?php $value = (isset($expediteur) ? $expediteur->id : ''); ?>
                                    <input type="hidden" id="clientid" value="<?= $value; ?>">

                                    <?php $value = (isset($expediteur) ? $expediteur->nom : ''); ?>
                                    <?= render_input('nom', 'name', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->email : '') ?>
                                    <?= render_input('email', 'email', $value, 'email'); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->telephone : ''); ?>
                                    <?= render_input('telephone', 'phone_number', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->telephone2 : ''); ?>
                                    <?= render_input('telephone2', 'phone_number_2', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->adresse : ''); ?>
                                    <?= render_input('adresse', 'address', $value); ?>

                                    <?php $selected = (isset($expediteur) ? $expediteur->ville_id : ''); ?>
                                    <?= render_select('ville_id', $cities, array('id', array('name')), 'city', $selected); ?>

                                    <div class="form-group">
                                        <label for="password" class="control-label"><?= _l('staff_add_edit_password'); ?></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control password" name="password">
                                            <span class="input-group-addon">
                                                <a href="#" class="generate_password" onclick="generatePassword(this);
                                                        return false;"><i class="fa fa-refresh"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php $value = (isset($expediteur) ? $expediteur->contact : ''); ?>
                                    <?= render_input('contact', 'contact', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->marque : ''); ?>
                                    <?= render_input('marque', 'name_of_bank', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->rib : ''); ?>
                                    <?= render_input('rib', 'rib', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->registre_commerce : ''); ?>
                                    <?= render_input('registre_commerce', 'trade_registry', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->raison_sociale : ''); ?>
                                    <?= render_input('raison_sociale', 'social_reason', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->site_web : ''); ?>
                                    <?= render_input('site_web', 'website', $value); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->prefix : ''); ?>
                                    <?= render_input('prefix', 'expediteur_prefix', $value); ?>
                                </div>
                                <div class="col-md-4">
                                    <?php $value = (isset($expediteur) ? $expediteur->code_parrainage : ''); ?>
                                    <?php $disabled = (isset($expediteur) && !empty($expediteur->code_parrainage) ? 'disabled' : ''); ?>
                                    <?php if (!isset($expediteur) || (isset($expediteur) && empty($expediteur->code_parrainage))) { ?>
                                        <div class="form-group">
                                            <label for="code_parrainage" class="control-label"><?= _l('code_parrainage'); ?></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control code-parrainage" name="code_parrainage" value="<?= $value ?>" <?= $disabled ?>>
                                                <span class="input-group-addon">
                                                    <a href="#" class="code-parrainage" onclick="generateCodeAffiliation(this);
                                                                return false;"><i class="fa fa-refresh"></i></a>
                                                </span>

                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <?= render_input('code_parrainage', 'code_parrainage', $value, 'text', array('disabled' => true)); ?>
                                    <?php } ?>

                                    <?php if (isset($expediteur)) { ?>
                                    <?php $value = (isset($expediteur) ? $expediteur->affiliation_code : ''); ?>
                                    <?= render_input('affiliation_code', 'affiliation_code', $value, 'text', array('disabled' => true)); ?>

                                    <?php $value = (isset($expediteur) ? (format_money($expediteur->total_colis_parrainage * get_option('frais_parrainage'))) : 0); ?>
                                    <?= render_input('total_colis_parrainage', 'total_colis', $value, 'text', array('disabled' => true)); ?>
                                    <?php } ?>
                                    
                                    <?php $selected = (isset($expediteur) ? $expediteur->groupe_id : ''); ?>
                                    <?= render_select('groupe_id', $groupes, array('id', array('name')), 'groupe', $selected); ?>

                                    <?php $selected = (isset($expediteur) ? $expediteur->commerciale : ''); ?>
                                    <?= render_select('commerciale', $staffs, array('staffid', array('firstname', 'lastname')), 'commercial', $selected); ?>

                                    <?php $selected = (isset($expediteur) ? $expediteur->account_manager : ''); ?>
                                    <?= render_select('account_manager', $staffs, array('staffid', array('firstname', 'lastname')), 'account_manager', $selected); ?>

                                    <?php $selected = (isset($expediteur) ? $expediteur->livreur : ''); ?>
                                    <?= render_select('livreur', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_men', $selected); ?>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary pull-right" type="submit"><?= _l('submit'); ?></button>
                                    <?php if (isset($contrat) && !is_null($contrat)) { ?>
                                        <a href="<?= admin_url('contrats/pdf/' . $contrat->id) ?>" class="btn btn-success pull-right mright5"><?= _l('download_contract'); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="frais">
                                <div class="col-md-3">
                                    <?php $value = (isset($expediteur) ? $expediteur->frais_livraison_interieur : get_option('frais_livraison_interieur')); ?>
                                    <?= render_input('frais_livraison_interieur', 'fresh_delivery_interior', $value, 'number', array('step' => '0.1')); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->frais_livraison_exterieur : get_option('frais_livraison_exterieur')); ?>
                                    <?= render_input('frais_livraison_exterieur', 'fresh_delivery_exterior', $value, 'number', array('step' => '0.1')); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->frais_retourne : get_option('frais_livraison_retour')); ?>
                                    <?= render_input('frais_retourne', 'fresh_return', $value, 'number', array('step' => '0.1')); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->frais_refuse : get_option('frais_colis_refuse_par_defaut')); ?>
                                    <?= render_input('frais_refuse', 'fresh_refuse', $value, 'number', array('step' => '0.1')); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php $value = (isset($expediteur) ? $expediteur->frais_supplementaire : get_option('frais_supplementaire')); ?>
                                    <?= render_input('frais_supplementaire', 'frais_supplementaire', $value, 'number'); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->frais_stockage : get_option('frais_stockage')); ?>
                                    <?= render_input('frais_stockage', 'frais_stockage', $value, 'number', array('step' => '0.1')); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->frais_emballage : get_option('frais_emballage')); ?>
                                    <?= render_input('frais_emballage', 'frais_emballage', $value, 'number', array('step' => '0.1')); ?>

                                    <?php $value = (isset($expediteur) ? $expediteur->frais_etiquette : get_option('frais_etiquette')); ?>
                                    <?= render_input('frais_etiquette', 'frais_etiquette', $value, 'number', array('step' => '0.1')); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    $checked = '';
                                    if (isset($expediteur) && $expediteur->ouverture == 1) {
                                        $checked = 'checked';
                                    }

                                    ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="ouverture" <?= $checked; ?>>
                                        <label for="ouverture"><?= _l('colis_opening'); ?></label>
                                    </div>
                                    <?php
                                    $checked = '';
                                    if (isset($expediteur) && $expediteur->option_frais == 1) {
                                        $checked = 'checked';
                                    }

                                    ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="option_frais" <?= $checked; ?>>
                                        <label for="option_frais"><?= _l('option_frais'); ?></label>
                                    </div>
                                    <?php
                                    $checked = '';
                                    if (isset($expediteur) && $expediteur->option_frais_assurance == 1) {
                                        $checked = 'checked';
                                    }

                                    ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="option_frais_assurance" <?= $checked; ?>>
                                        <label for="option_frais_assurance"><?= _l('option_frais_assurance'); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary pull-right" type="submit"><?= _l('submit'); ?></button>
                                </div>
                            </div>
                            <?php if (isset($expediteur)) { ?>
                                <?php if (has_permission('colis', '', 'view')) { ?>
                                    <div role="tabpanel" class="tab-pane" id="colis">
                                        <div class="row small-text-span mbot15">
                                            <div class="col-md-6 mbot10">
                                                <h3 class="text-success no-margin"><?= _l('colis_summary'); ?></h3>
                                            </div>
                                            <div class="col-md-6 mbot10">
                                                <div class="btn-group pull-right" data-toggle="tooltip" title="<?= _l('state_colis_filter'); ?>">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-list"></i> <?= _l('state_colis_filter'); ?>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="#" onclick="dt_etat_colis_view(1, '.table-colis-expediteur');
                                                                            return false;">
                                                                   <?= _l('unpaid'); ?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" onclick="dt_etat_colis_view(2, '.table-colis-expediteur');
                                                                            return false;">
                                                                   <?= _l('paid'); ?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" onclick="dt_etat_colis_view(3, '.table-colis-expediteur');
                                                                            return false;">
                                                                   <?= _l('invoiced'); ?>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', 'id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view('all', '.table-colis-expediteur');
                                                                return false;"><span class="text-success bold"><?= _l('total'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_id' => 2, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(2, '.table-colis-expediteur');
                                                                return false;"><span class="text-success bold"><?= _l('colis_delivered'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_id' => 3, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(3, '.table-colis-expediteur');
                                                                return false;"><span class="text-danger bold"><?= _l('colis_returned'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_id' => 1, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(1, '.table-colis-expediteur');
                                                                return false;"><span class="text-warning bold"><?= _l('status_colis_current'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 4, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(4, '.table-colis-expediteur');
                                                                return false;"><span class="text-success bold"><?= _l('status_colis_shipped'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 9, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(9, '.table-colis-expediteur');
                                                                return false;"><span class="text-danger bold"><?= _l('status_colis_refused'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 11, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(11, '.table-colis-expediteur');
                                                                return false;"><span class="text-info bold"><?= _l('status_colis_postponed'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 10, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(10, '.table-colis-expediteur');
                                                                return false;"><span class="text-warning bold"><?= _l('status_colis_cancelled'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 7, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(7, '.table-colis-expediteur');
                                                                return false;"><span class="text-info bold"><?= _l('status_colis_unreachable'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 6, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(6, '.table-colis-expediteur');
                                                                return false;"><span class="text-primary bold"><?= _l('status_colis_no_answer'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 8, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(8, '.table-colis-expediteur');
                                                                return false;"><span class="text-primary bold"><?= _l('status_colis_wrong_number'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 13, 'id_expediteur' => $expediteur->id)); ?></h3>
                                                <a href="#" onclick="dt_custom_view(13, '.table-colis-expediteur');
                                                                return false;"><span class="text-success bold"><?= _l('status_colis_in'); ?></span></a>
                                            </div>
                                        </div>
                                        <div class="row small-text-span mbot15">
                                            <div class="col-md-12 mbot10">
                                                <h3 class="text-success no-margin"><?= _l('colis_sum'); ?></h3>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-6 text-center border-right">
                                                <h3 class="bold"><?= number_format(sum_from_table('tblcolis', array('field' => 'frais'), 'status_id = 2 AND id_expediteur = ' . $expediteur->id), 2, ',', ' ') . ' Dhs'; ?></h3>
                                                <a href="#"><span class="text-success bold"><?= _l('fresh_sum_colis_delivered'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-6 text-center border-right">
                                                <h3 class="bold"><?= number_format(sum_from_table('tblcolis', array('field' => 'crbt'), 'status_id = 2 AND id_expediteur = ' . $expediteur->id), 2, ',', ' ') . ' Dhs'; ?></h3>
                                                <a href="#"><span class="text-success bold"><?= _l('price_sum_colis_delivered'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-6 text-center border-right">
                                                <h3 class="bold"><?= number_format(sum_from_table('tblcolis', array('field' => 'frais'), 'status_id = 3 AND id_expediteur = ' . $expediteur->id), 2, ',', ' ') . ' Dhs'; ?></h3>
                                                <a href="#"><span class="text-danger bold"><?= _l('fresh_sum_colis_returned'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-6 text-center border-right">
                                                <h3 class="bold"><?= number_format(sum_from_table('tblcolis', array('field' => 'crbt'), 'status_id = 3 AND id_expediteur = ' . $expediteur->id), 2, ',', ' ') . ' Dhs'; ?></h3>
                                                <a href="#"><span class="text-danger bold"><?= _l('price_sum_colis_returned'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-6 text-center border-right">
                                                <h3 class="bold"><?= number_format(sum_from_table('tblcolis', array('field' => 'frais'), 'status_id = 1 AND id_expediteur = ' . $expediteur->id), 2, ',', ' ') . ' Dhs'; ?></h3>
                                                <a href="#"><span class="text-info bold"><?= _l('fresh_sum_colis_in_progress'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-6 text-center border-right">
                                                <h3 class="bold"><?= number_format(sum_from_table('tblcolis', array('field' => 'crbt'), 'status_id = 1 AND id_expediteur = ' . $expediteur->id), 2, ',', ' ') . ' Dhs'; ?></h3>
                                                <a href="#"><span class="text-info bold"><?= _l('price_sum_colis_in_progress'); ?></span></a>
                                            </div>
                                        </div>
                                        <?= form_hidden('custom_view'); ?>
                                        <?= form_hidden('etat'); ?>
                                        <?php init_colis_expediteur_table(); ?>
                                    </div>
                                <?php } ?>
                                <?php if (has_permission('colis_en_attente', '', 'view')) { ?>
                                    <div role="tabpanel" class="tab-pane" id="colis-en-attente">
                                        <div class="row small-text-span mbot15">
                                            <div class="col-md-6 mbot10">
                                                <h3 class="text-success no-margin"><?= _l('colis_summary'); ?></h3>
                                            </div>
                                            <div class="col-md-6 mbot10">
                                                <div class="btn-group pull-right" data-toggle="tooltip" title="Filtre colis en attente">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-list"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="#" onclick="dt_custom_view('all', '.table-colis-en-attente-expediteur');
                                                                            return false;">
                                                                   <?= _l('task_list_all'); ?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" onclick="dt_custom_view('converted', '.table-colis-en-attente-expediteur');
                                                                            return false;">
                                                                   <?= _l('colis_en_attente_converted_on_colis'); ?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" onclick="dt_custom_view('not_converted', '.table-colis-en-attente-expediteur');
                                                                            return false;">
                                                                   <?= _l('colis_en_attente_not_converted_on_colis'); ?>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolisenattente', 'id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view('all', '.table-colis-en-attente-expediteur');
                                                                return false;"><span class="text-info bold"><?= _l('total'); ?></span></a>
                                            </div>
                                            <div class="col-md-3 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolisenattente', 'colis_id IS NULL AND id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view('not_converted', '.table-colis-en-attente-expediteur');
                                                                return false;"><span class="text-warning bold"><?= _l('expediteurs_nav_add_colis_waiting_for_pick_up'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblcolisenattente', 'colis_id IS NOT NULL AND id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view('converted', '.table-colis-en-attente-expediteur');
                                                                return false;"><span class="text-warning bold"><?= _l('colis_en_attente_converted_on_colis'); ?></span></a>
                                            </div>
                                        </div>
                                        <?php init_colis_en_attente_expediteur_table(); ?>
                                    </div>
                                <?php } ?>
                                <?php if (has_permission('bon_livraison', '', 'view')) { ?>
                                    <div role="tabpanel" class="tab-pane" id="bon_livraison">
                                        <div class="row small-text-span mbot15">
                                            <div class="col-md-12 mbot10">
                                                <h3 class="text-success no-margin"><?= _l('delivery_notes_summary'); ?></h3>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblbonlivraisoncustomer', 'id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view('all', '.table-bons-livraison-expediteur');
                                                                return false;"><span class="text-info bold"><?= _l('total'); ?></span></a>
                                            </div>
                                        </div>
                                        <?php init_bons_livraison_expediteur_table(); ?>
                                    </div>
                                <?php } ?>
                                <?php if (has_permission('invoices', '', 'view')) { ?>
                                    <div role="tabpanel" class="tab-pane" id="factures">
                                        <div class="row small-text-span mbot15">
                                            <div class="col-md-12 mbot10">
                                                <h3 class="text-success no-margin"><?= _l('invoices_summary'); ?></h3>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblfactures', 'id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view('all', '.table-factures-expediteur');
                                                                return false;"><span class="text-info bold"><?= _l('total'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblfactures', 'type = 2 AND id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view(2, '.table-factures-expediteur');
                                                                return false;"><span class="text-success bold"><?= _l('status_colis_delivered'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblfactures', 'type = 3 AND id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view(3, '.table-factures-expediteur');
                                                                return false;"><span class="text-danger bold"><?= _l('status_colis_returned'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblfactures', 'status = 2 AND id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view(3, '.table-factures-expediteur');
                                                                return false;"><span class="text-success bold"><?= _l('facture_interne_status_regle'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblfactures', 'status = 1 AND id_expediteur = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view(3, '.table-factures-expediteur');
                                                                return false;"><span class="text-danger bold"><?= _l('facture_interne_status_non_regle'); ?></span></a>
                                            </div>
                                        </div>
                                        <?php init_factures_expediteur_table(); ?>
                                    </div>
                                <?php } ?>
                                <?php if (has_permission('claim_shipper', '', 'view')) { ?>
                                    <div role="tabpanel" class="tab-pane" id="reclamations">
                                        <div class="row small-text-span mbot15">
                                            <div class="col-md-12 mbot10">
                                                <h3 class="text-success no-margin"><?= _l('claims_summary'); ?></h3>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblreclamations', 'relation_id = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view('all', '.table-reclamations-expediteur');
                                                                return false;"><span class="text-info bold"><?= _l('total'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblreclamations', 'etat = 0 AND relation_id = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view(0, '.table-reclamations-expediteur');
                                                                return false;"><span class="text-danger bold"><?= _l('als_reclamations_unprocessed'); ?></span></a>
                                            </div>
                                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                                <h3 class="bold"><?= total_rows('tblreclamations', 'etat = 1 AND relation_id = ' . $expediteur->id); ?></h3>
                                                <a href="#" onclick="dt_custom_view(1, '.table-reclamations-expediteur');
                                                                return false;"><span class="text-success bold"><?= _l('als_reclamations_processed'); ?></span></a>
                                            </div>
                                        </div>
                                        <?php init_reclamations_expediteur_table(); ?>
                                    </div>
                                <?php } ?>
                                <div role="tabpanel" class="tab-pane" id="activiti_log_customer">
                                    <div class="row small-text-span mbot15">
                                        <div class="col-md-12 mbot10">
                                            <h3 class="text-success no-margin"><?= _l('authentication_summary'); ?></h3>
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                            <h3 class="bold"><?= total_rows('tblnumberofauthentication', 'clientid = ' . $expediteur->id); ?></h3>
                                            <a href="#"><span class="text-info bold"><?= _l('number_of_authentication'); ?></span></a>
                                        </div>
                                    </div>
                                    <?php init_activity_log_expediteur_table(); ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="chart">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" id="report-time">
                                                <label class="form-label"><?= _l('report_period'); ?></label>
                                                <select class="selectpicker" id="months-report-expediteur" data-width="100%">
                                                    <option value=""><?= _l('report_sales_months_all_time'); ?></option>
                                                    <option value="6"><?= _l('report_sales_months_six_months'); ?></option>
                                                    <option value="12"><?= _l('report_sales_months_twelve_months'); ?></option>
                                                    <option value="custom"><?= _l('report_sales_months_custom'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="date-range-expediteur" class="form-group hide animated">
                                            <div class="col-md-4">
                                                <label for="report-from" class="control-label"><?= _l('report_sales_from_date'); ?></label>
                                                <div class="input-group date">
                                                    <input type="text" class="form-control datepicker" id="report-from-expediteur">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar calendar-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="report-to" class="control-label"><?= _l('report_sales_to_date'); ?></label>
                                                <div class="input-group date">
                                                    <input type="text" class="form-control datepicker" disabled="disabled" id="report-to-expediteur">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar calendar-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <canvas id="chart-expediteur" class="animated fadeIn"></canvas>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/expediteurs/expediteur.js?v=' . version_sources()); ?>"></script>
<?php if (isset($expediteur)) { ?>
    <script src="<?= site_url('assets/js/admin/expediteurs/fresh-crbt-reports-expediteur.js?v=' . version_sources()); ?>"></script>
<?php } ?>
</body>
</html>
