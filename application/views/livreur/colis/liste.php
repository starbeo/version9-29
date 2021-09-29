<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile" style="background-color: <?= $background_color ?>;">
        <span class="icon-return-mobile"><i class="fa fa-arrow-left mright5"></i><?= _l('return') ?></span>
        <?= $title ?>
        <span class="title-nombre-total"><?= _l('total') ?> : <?= $total ?></span>
    </h4>
    <div class="content">
        <?php if (!empty($pagination)) { ?>
            <div class="row no-margin">  
                <div class="bloc-pagination">
                    <ul class="pagination bloc-ul-pagination">
                        <?= $pagination ?>
                    </ul>
                </div>   
            </div>
        <?php } ?>
        <div class="row no-margin">
            <?php
            if ($total > 0) {
                foreach ($colis as $coli) {
                    $phoneNumber = correctionPhoneNumber($coli['telephone']);

                    ?>
                    <div class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 bloc-colis">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <p class="mbot5"><i class="fa fa-barcode mright5 fs15"></i><span class="bold"><?= $coli['code_barre'] ?></span></p>
                                <p class="mbot5"><i class="fa fa-user mright5 fs15"></i><span class="bold"><?= ucwords($coli['nom_complet']) ?></span></p>
                                <p class="mbot5"><i class="fa fa-barcode mright5 fs15"></i><span class="bold"><?= _l('n_commande') ?> : <?= $coli['num_commande'] ?></span></p>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <p class="mbot5"><i class="fa fa-money mright5 fs15"></i><span class="bold"><?= $coli['crbt'] ?> MAD</span></p>
                                <p class="mbot5"><i class="fa fa-phone mright5 fs15"></i><span class="bold"><?= $phoneNumber ?></span></p>
                                <p class="mbot5"><i class="fa fa-map-marker mright10 fs15"></i><span class="bold"><?= $coli['ville_name'] ?></span></p>
                            </div>
                        </div
                        <p class="mbot5"><i class="fa fa-calendar mright5 fs15"></i><span class="bold"><?= _l('colis_list_date_pickup') ?> : <?= date(get_current_date_format(), strtotime($coli['date_ramassage'])) ?></span></p>
                        <?php if (!is_null($coli['date_livraison']) && $coli['status_reel'] == 2) { ?>
                            <p class="mbot5"><i class="fa fa-calendar-check-o mright5 fs15"></i><span class="bold"><?= _l('colis_list_date_livraison') ?> : <?= date(get_current_date_format(), strtotime($coli['date_livraison'])) ?></span></p>
                        <?php } else  ?>
                        <p class="mbot5"><i class="fa fa-map mright5 fs15"></i><span class="bold"><?= ucwords($coli['adresse']) ?></span></p>
                        <?php if (!empty($coli['commentaire'])) { ?>
                            <p class="mbot5"><i class="fa fa-comment mright5 fs15"></i><span class="bold"><?= $coli['commentaire'] ?></span></p>
                        <?php } ?>
                        <p class="mbot5 text-center">
                            <?= icon_btn('livreur/colis/detail/' . $coli['id'], 'eye', 'btn-info btn-bloc') ?>
                            <?php if (is_numeric($coli['status_reel']) && $coli['status_reel'] != 2 && $coli['status_reel'] != 3 && $coli['status_reel'] != 13) { ?>
                                <?= icon_btn('tel:' . $phoneNumber, 'phone', 'btn-success btn-bloc', array(), true) ?>
                                <?= icon_btn('https://api.whatsapp.com/send?phone=+212' . $phoneNumber . '&text=Salam ' . ucwords($coli['nom_complet']) . ', m3ak le livreur', 'whatsapp', 'btn-success btn-bloc', array(), true) ?>
                                <?= icon_btn('livreur/status/add/' . $coli['id'], 'plus', 'btn-info btn-bloc') ?>
                            <?php } ?>
                        </p>
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
        <?php if (!empty($pagination)) { ?>
            <div class="row no-margin">  
                <div class="bloc-pagination">
                    <ul class="pagination bloc-ul-pagination">
                        <?= $pagination ?>
                    </ul>
                </div>   
            </div>
        <?php } ?>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/colis/liste.js?v=' . version_sources()); ?>"></script>
</body>
</html>
