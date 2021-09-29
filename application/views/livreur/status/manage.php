<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile default-background-color default-color">
        <span class="icon-return-mobile">
            <i class="fa fa-arrow-left mright5"></i><?= _l('return') ?>
        </span>
        <?= $title ?>
    </h4>
    <div class="content">
        <div class="row no-margin">
            <p class="text-center mbot0">
                <img src="<?= site_url('assets/images/defaults/menus/coli.png') ?>" class="img-detail" />
            </p>
            <h2 class="no-margin text-center bold"><?= $coli->code_barre ?></h2>
            <p class="mtop10 text-center">
                <?= icon_btn('tel:' . $coli->telephone, 'phone', 'btn-success btn-bloc width100p mbot5', array(), true, _l('call_customer')) ?>
                <?= icon_btn('https://api.whatsapp.com/send?phone=+212' . $coli->telephone . '&text=Salam ' . ucwords($coli->nom_complet) . ', m3ak le livreur', 'whatsapp', 'btn-success btn-bloc width100p mbot5', array(), true, _l('whatsapp_message')) ?>
                <div class="mbot5">
                    <label class="btn-info bloc-date"><?= _l('add_status') ?></label>
                    <?= form_open(livreur_url('status/add'), array('id' => 'form-add-status')); ?>
                    <div class="bloc-status">
                        <?php include_once(APPPATH . 'views/livreur/includes/alerts.php'); ?>
                        <div class="col-xs-12 col-sm-12 col-md-12 no-padding">
                            <?= render_input_hidden('url_referrer', 'url_referrer', $url_referrer); ?>
                            <?= render_input_hidden('code_barre_verifie', 'code_barre_verifie', $coli->code_barre); ?>
                            <?= render_input_hidden('clientid', 'clientid', $coli->id_expediteur); ?>
                            <?= render_input_hidden('coli_id', 'coli_id', $coli->id); ?>
                            <?= render_input_hidden('telephone', 'telephone', $coli->telephone); ?>
                            <?= render_input_hidden('crbt', 'crbt', $coli->crbt); ?>
                            <div id="body-form-add-status" class="col-md-12">
                                <?= render_select('type', $statuses, array('id', array('name')), 'status'); ?>
                                <?= render_select('emplacement_id', $locations, array('id', array('name')), 'location'); ?>
                                <?= render_date_input('date_reporte', 'date_reporte', '', array(), array('id' => 'date_reporte'), 'display-none'); ?>
                                <?= render_select('motif', $motifs, array('id', array('name')), 'status_motif', '', array(), array('id' => 'motif'), 'display-none'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <button id="btn-add-status" type="submit" class="btn btn-primary width100p mbot5"><?= _l('submit'); ?></button>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <a href="<?= $url_referrer ?>" class="btn btn-warning width100p"><?= _l('close'); ?></a>
                            </div>
                        </div>
                    </div>
                    <?= form_close(); ?>
                </div>
            </p>
        </div>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/status/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>
