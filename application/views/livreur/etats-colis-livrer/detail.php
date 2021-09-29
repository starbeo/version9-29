<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile default-background-color default-color">
        <span class="icon-return-mobile"><i class="fa fa-arrow-left mright5"></i><?= _l('return') ?></span>
        <?= $title ?>
        <span class="title-nombre-total"><?= _l('total') ?> : <?= $total ?></span>
    </h4>
    <div class="content">
        <div class="row no-margin">
            <?php include_once(APPPATH . 'views/livreur/includes/alerts.php'); ?>
            <p class="text-center mbot0">
                <img src="<?= site_url('assets/images/defaults/menus/factures.png') ?>" class="img-detail" />
            </p>
            <h2 class="no-margin text-center bold"><?= $etat_colis_livrer->nom ?></h2>
            <h4 class="mbot5"><i class="fa fa-money mright5 fs20"></i><span class="bold"><?= _l('total_to_pay') ?> : </span><span class="pull-right bold"><?= $etat_colis_livrer->total ?> MAD</span></h4>
            <h4 class="mbot5"><i class="fa fa-money mright5 fs20"></i><span class="bold"><?= _l('total_paid') ?> : </span><span class="pull-right bold"><?= $etat_colis_livrer->total_received ?> MAD</span></h4>
            <h4 class="mbot5"><i class="fa fa-money mright5 fs20"></i><span class="bold"><?= _l('rest') ?> : </span><span class="pull-right bold"><?= $etat_colis_livrer->manque ?> MAD</span></h4>
        </div>

        <div class="row no-margin">
            <?php
            if ($total > 0) {
                foreach ($colis as $coli) {
                    $phoneNumber = correctionPhoneNumber($coli['telephone']);

                    ?>
                    <div class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 bloc-colis">
                        <p class="text-center mbot5 bold"><?= format_status_colis($coli['status_reel']) ?></p>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <p class="mbot5"><i class="fa fa-barcode mright5 fs15"></i><span class="bold"><?= $coli['code_barre'] ?></span></p>
                                <p class="mbot5"><i class="fa fa-user mright5 fs15"></i><span class="bold"><?= ucwords($coli['nom_complet']) ?></span></p>
                                <p class="mbot5"><i class="fa fa-barcode mright5 fs15"></i><span class="bold"><?= _l('n_commande') ?> : <?= $coli['num_commande'] ?></span></p>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <p class="mbot5"><i class="fa fa-money mright5 fs15"></i><span class="bold"><?= $coli['crbt'] ?> MAD</span></p>
                                <p class="mbot5"><i class="fa fa-phone mright5 fs15"></i><span class="bold"><?= $phoneNumber ?></span></p>
                                <p class="mbot5"><i class="fa fa-map-marker mright10 fs15"></i><span class="bold"><?= $coli['ville'] ?></span></p>
                            </div>
                        </div>
                        <p class="mbot5"><i class="fa fa-calendar mright5 fs15"></i><span class="bold"><?= _l('colis_list_date_pickup') ?> : <?= date(get_current_date_format(), strtotime($coli['date_ramassage'])) ?></span></p>
                        <?php if (!is_null($coli['date_livraison']) && $coli['status_reel'] == 2) { ?>
                            <p class="mbot5"><i class="fa fa-calendar-check-o mright5 fs15"></i><span class="bold"><?= _l('colis_list_date_livraison') ?> : <?= date(get_current_date_format(), strtotime($coli['date_livraison'])) ?></span></p>
                        <?php } else  ?>
                        <p class="mbot5"><i class="fa fa-map mright5 fs15"></i><span class="bold"><?= ucwords($coli['adresse']) ?></span></p>
                        <?php if (!empty($coli['commentaire'])) { ?>
                            <p class="mbot5"><i class="fa fa-comment mright5 fs15"></i><span class="bold"><?= $coli['commentaire'] ?></span></p>
                        <?php } ?>
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
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/etats-colis-livrer/detail.js?v=' . version_sources()); ?>"></script>
</body>
</html>
