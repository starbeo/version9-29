<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php if (isset($contract)) { ?>
                            <a href="<?= admin_url('contrats/pdf/' . $contract->id); ?>" class="btn btn-default" data-toggle="tooltip" title="<?= _l('view_pdf'); ?>" data-placement="bottom">
                                <i class="fa fa-file-pdf-o"></i>
                            </a>
                            <hr />
                            <div class="clearfix"></div>
                        <?php } ?>
                        <?= form_open($this->uri->uri_string(), array('id' => 'form-contract')); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (isset($contract) ? $contract->datestart : $defaultDateStart); ?>
                                <?= render_date_input('datestart', 'start_date', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($contract) ? $contract->dateend : ''); ?>
                                <?= render_date_input('dateend', 'end_date', $value); ?>
                            </div>
                        </div>
                        <?php $selected = (isset($contract) ? $contract->client_id : ''); ?>
                        <?= render_select('client_id', $clients, array('id', array('nom')), 'client', $selected); ?>
                        <?php $classBlocInfosClient = (isset($contract) ? '' : 'display-none'); ?>
                        <div id="bloc-infos-client" class="<?= $classBlocInfosClient; ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $value = (isset($contract) ? $contract->fullname : ''); ?>
                                    <?= render_input('fullname', 'fullname', $value); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($contract) ? $contract->contact : ''); ?>
                                    <?= render_input('contact', 'contact', $value); ?>
                                </div>
                            </div>  
                            <?php $value = (isset($contract) ? $contract->address : ''); ?>
                            <?= render_input('address', 'address', $value); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $value = (isset($contract) ? $contract->frais_livraison_interieur : ''); ?>
                                    <?= render_input('frais_livraison_interieur', 'fresh_delivery_interior', $value, 'number'); ?>
                                    <?php $value = (isset($contract) ? $contract->commercial_register : ''); ?>
                                    <?= render_input('commercial_register', 'trade_registry', $value); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($contract) ? $contract->frais_livraison_exterieur : ''); ?>
                                    <?= render_input('frais_livraison_exterieur', 'fresh_delivery_exterior', $value, 'number'); ?>
                                    <?php $value = (isset($contract) ? date(get_current_date_format(), strtotime($contract->date_created_client)) : ''); ?>
                                    <?= render_input('date_created_client', 'date_created', $value); ?>
                                </div>
                            </div>
                            <p class="bold cF00"><?= _l('to_download_the_contract_the_fields_link_to_the_customer_must_be_completed'); ?></p>
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-primary" data-toggle="tooltip" title="<?= _l('contract_trash_tooltip'); ?>">
                                <input type="checkbox" name="trash" <?php
                                if (isset($contract)) {
                                    if ($contract->trash == 1) {
                                        echo 'checked';
                                    }
                                };

                                ?>>
                                <label for=""><?= _l('trash'); ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="not_visible_to_client" <?php
                                if (isset($contract)) {
                                    if ($contract->not_visible_to_client == 1) {
                                        echo 'checked';
                                    }
                                };

                                ?>>
                                <label for=""><?= _l('not_visible_to_client'); ?></label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-heading"><?= _l('template'); ?></div>
                    <div class="panel-body">
                        <?= $template; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/contrats/contrat.js'); ?>"></script>
</body>
</html>
