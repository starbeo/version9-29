<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile default-background-color default-color">
        <span class="icon-return-mobile">
            <i class="fa fa-arrow-left mright5"></i><?= _l('return') ?>
        </span>
        <?= $title ?>
    </h4>
    <div class="content">
        <?= render_input_hidden('url_referrer', 'url_referrer', $url_referrer); ?>
        <div class="row no-margin">
            <div class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 no-padding">
                <p class="text-center mbot0">
                    <img src="<?= site_url('assets/images/defaults/menus/coli.png') ?>" class="img-detail" />
                </p>
                <h2 class="no-margin text-center bold"><?= $coli->code_barre ?></h2>
                <h4 class="mbot5"><i class="fa fa-barcode mright5 fs20"></i><span class="bold"><?= _l('code_barre') ?> : </span><?= $coli->code_barre ?></h4>
                <h4 class="mbot5"><i class="fa fa-barcode mright5 fs20"></i><span class="bold"><?= _l('n_commande') ?> : </span><?= $coli->num_commande ?></h4>
                <h4 class="mbot5"><i class="fa fa-user mright5 fs20"></i><span class="bold"><?= _l('fullname') ?> : </span><?= ucwords($coli->nom_complet) ?></h4>
                <h4 class="mbot5"><i class="fa fa-phone mright5 fs20"></i><span class="bold"><?= _l('phone_number') ?> : </span><?= $coli->telephone ?></h4>
                <h4 class="mbot5"><i class="fa fa-money mright5 fs20"></i><span class="bold"><?= _l('price') ?> : </span><span class="default-txt-color"><?= $coli->crbt ?> MAD</span></h4>
                <h4 class="mbot5"><i class="fa fa-map-marker mright5 fs20"></i><span class="bold"><?= _l('city') ?> : </span><?= $coli->ville_name ?></h4>
                <h4 class="mbot5"><i class="fa fa-map mright5 fs20"></i><span class="bold"><?= _l('address') ?> : </span><?= $coli->adresse ?></h4>
                <h4 class="mbot5"><i class="fa fa-calendar mright5 fs20"></i><span class="bold"><?= _l('colis_list_date_pickup') ?> : </span><?= date(get_current_date_format(), strtotime($coli->date_ramassage)) ?></h4>
                <?php if (!is_null($coli->date_livraison) && $coli->date_livraison == 2) { ?>
                    <h4 class="mbot5"><i class="fa fa-calendar-check-o mright5 fs20"></i><span class="bold"><?= _l('colis_list_date_livraison') ?> : </span><?= date(get_current_date_format(), strtotime($coli->date_livraison)) ?></h4>
                <?php } ?>
                <?php if (!empty($coli->commentaire)) { ?>
                    <h4 class="mbot5">
                        <i class="fa fa-comment mright5 fs20"></i>
                        <span class="bold">
                            <?= _l('address') ?> : 
                        </span>
                        <?= $coli->adresse ?>
                    </h4>
                <?php } ?>
                <p class="mtop10 text-center">
                    <?php if (is_numeric($coli->status_reel) && $coli->status_reel != 2 && $coli->status_reel != 3 && $coli->status_reel != 13) { ?>
                        <?= icon_btn('tel:' . $coli->telephone, 'phone', 'btn-success btn-bloc width100p mbot5', array(), true, _l('call_customer')) ?>
                        <?= icon_btn('https://api.whatsapp.com/send?phone=+212' . $coli->telephone . '&text=Salam ' . ucwords($coli->nom_complet) . ', m3ak le livreur', 'whatsapp', 'btn-success btn-bloc width100p mbot5', array(), true, _l('whatsapp_message')) ?>
                        <?= icon_btn('javascript:void(0)', 'plus', 'btn-info btn-bloc btn-add-status width100p mbot5', array(), true, _l('status_change')) ?>
                    <div id="add-status" class="mbot5 display-none">
                        <label class="btn-info bloc-date"><?= _l('add_status') ?></label>
                        <?= form_open(livreur_url('status/add_json'), array('id' => 'form-add-status')); ?>
                        <div class="row no-margin bloc-status">
                            <div class="row">
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
                                    <button id="btn-close-status" type="button" class="btn btn-warning width100p" data-dismiss="modal"><?= _l('close'); ?></button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                <?php } ?>
                </p>
                <h4 class="bloc-title-mobile default-background-color default-color mbot5">
                    <?= _l('list_of_statutes') ?>
                </h4>
                <?php
                if (count($statuts) > 0) {
                    foreach ($statuts as $statut) {

                        ?>
                        <div class="mbot5">
                            <label class="btn-info bloc-date" style="background-color: <?= get_status_color($statut['type']) ?> !important;"><?= date(get_current_date_time_format(), strtotime($statut['date_created'])) ?></label>
                            <div class="row no-margin bloc-status">
                                <h5 class="mbot5"><i class="fa fa-calendar-check-o mright5 fs15"></i><span class="bold"><?= _l('status') ?> : </span><?= format_status_colis($statut['type']) ?></h5>
                                <?php if (!is_null($statut['date_reporte']) && $statut['date_reporte'] != '0000-00-00') { ?>
                                    <h5 class="mbot5"><i class="fa fa-calendar mright5 fs15"></i><span class="bold"><?= _l('date_reporte') ?> : </span><?= date(get_current_date_format(), strtotime($statut['date_reporte'])) ?></h5>
                                <?php } ?>
                                <h5 class="mbot5"><i class="fa fa-map-marker mright10 fs15"></i><span class="bold"><?= _l('location') ?> : </span><?= get_location_name($statut['emplacement_id']) ?></h5>
                                <?php if (is_numeric($statut['motif']) && $statut['motif'] != 0) { ?>
                                    <h5 class="mbot5"><i class="fa fa-comment mright5 fs15"></i><span class="bold"><?= _l('comment') ?> : </span><?= format_status_colis($statut['motif']) ?></h5>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {

                    ?>
                    <h2 class="text-center"><?= _l('dt_empty_table') ?></h2>
                    <?php
                }

                ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/colis/detail.js?v=' . version_sources()); ?>"></script>
</body>
</html>
