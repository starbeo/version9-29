<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('contrats', '', 'create')) { ?>
                            <a href="<?= admin_url('contrats/contrat'); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_contract'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right btn-statistique-contracts mright5"><?= _l('contracts_summary'); ?></a>
                        <a href="#" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
                            <div class="col-md-3">
                                <?= render_select('f-client', $clients, array('id', array('nom')), 'client'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('f-date-start', 'start_date'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('f-date-end', 'end_date'); ?>
                            </div>
                            <div class="col-md-3">
                                <?= render_date_input('f-date-created', 'date_created'); ?>
                            </div>
                            <div class="col-md-12 text-right">
                                <button id="filtre-submit" class="btn btn-primary"><?= _l('filter'); ?></button>
                                <button id="filtre-reset" class="btn btn-default"><?= _l('empty'); ?></i></button>
                            </div>
                        </div>
                        <div id="statistique-contracts" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-success no-margin"><?= _l('contracts_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcontrats'); ?></h3>
                                <a href="#"><span class="text-muted bold"><?= _l('total'); ?></span></a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcontrats', array('DATE(dateend) >' => date('Y-m-d'), 'trash' => 0, 'id_entreprise' => $idEntreprise)); ?></h3>
                                <span class="text-info bold"><?= _l('active'); ?></span>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcontrats', array('DATE(dateend) <' => date('Y-m-d'), 'trash' => 0, 'id_entreprise' => $idEntreprise)); ?></h3>
                                <span class="text-danger bold"><?= _l('expired'); ?></span>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcontrats', 'dateend BETWEEN "' . $minusSevenDays . '" AND "' . $plusSevenDays . '" AND trash = 0 AND id_entreprise = ' . $idEntreprise); ?></h3>
                                <span class="text-warning bold"><?= _l('about_to_expire'); ?></span>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <h3 class="bold"><?= total_rows('tblcontrats', 'date_created BETWEEN "' . $minusSevenDays . '" AND "' . $plusSevenDays . '" AND trash = 0 AND id_entreprise = ' . $idEntreprise); ?></h3>
                                <span class="text-info bold"><?= _l('recently_added'); ?></span>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center">
                                <h3 class="bold"><?= total_rows('tblcontrats', array('trash' => 1, 'id_entreprise' => $idEntreprise)); ?></h3>
                                <span class="text-info bold"><?= _l('trash'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('subject'),
                            _l('client'),
                            _l('start_date'),
                            _l('end_date'),
                            _l('staff'),
                            _l('date_created'),
                            _l('actions')
                            ), 'contrats');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/contrats/manage.js'); ?>"></script>
</body>
</html>
