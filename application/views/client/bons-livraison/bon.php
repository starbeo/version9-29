<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>

            <div class="col-md-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12 no-padding">
                            <h4><b><?= _l('detail_delivery_note'); ?></b></h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (isset($bon_livraison)) { ?>
                            <?= form_hidden('bonlivraison_id', $bon_livraison->id); ?>
                            <?php $value = (isset($bon_livraison) ? $bon_livraison->nom : 'BL-' . date('d/m/Y') . '-'); ?>
                            <?= render_input('nom', 'name', $value, 'text', array('disabled' => true)); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>

            <?php if (isset($bon_livraison)) { ?>
                <div class="col-md-9">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <h4><?= _l('list_colis'); ?> : <b></h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php init_colis_en_attente_bon_livraison_table(); ?>
                            <?= loader_waiting_ajax('5%', '45%'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <h4><?= _l('historical_delivery_note'); ?></h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php
                            render_datatable(array(
                                '',
                                _l('code_barre'),
                                _l('colis_list_name'),
                                _l('city'),
                                _l('crbt'),
                                _l('date_created')
                                ), 'historique-colis-bon-livraison');

                            ?>
                            <?= loader_waiting_ajax('2%', '45%'); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/bons-livraison/bon.js?v=' . version_sources()); ?>"></script>
</body>
</html>
