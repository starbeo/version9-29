<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body pbot30">
            <div class="row home-summary">
                <center>
                    <i class="fa fa-filter filter-statistics" onclick="showModalFiltreStatistiqueDashbord();"></i>
                </center>
                
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('colis'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis'); ?></h3>
                        <h1 id="total-colis" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('colis/en_cours'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_in_progress'); ?></h3>
                        <h1 id="total-colis-en-cours-au-point-relais" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('colis/recu'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_received'); ?></h3>
                        <h1 id="total-colis-reception-au-point-relais" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('colis/recu_par_livreur'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_received_by_the_delivery_man'); ?></h3>
                        <h1 id="total-colis-reception-par-le-livreur" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('colis/livrer'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_delivered'); ?></h3>
                        <h1 id="total-colis-livrer" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis-livrer.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('colis/retourner'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_returned'); ?></h3>
                        <h1 id="total-colis-retourner" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis-retourner.png') ?>" />
                    </a>
                </div>
                
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('bons_livraison'); ?>">
                        <h3 class="bold no-margin"><?= _l('delivery_notes'); ?></h3>
                        <h1 id="total-bons-livraison" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/bons-de-livraison.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('bons_livraison/sortie'); ?>">
                        <h3 class="bold no-margin"><?= _l('delivery_notes_exit'); ?></h3>
                        <h1 id="total-bons-livraison-sortie" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/bons-de-livraison.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('bons_livraison/retourner'); ?>">
                        <h3 class="bold no-margin"><?= _l('delivery_notes_returned'); ?></h3>
                        <h1 id="total-bons-livraison-retourner" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/bons-de-livraison.png') ?>" />
                    </a>
                </div>
                
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('etats_colis_livrer'); ?>">
                        <h3 class="bold no-margin"><?= _l('etats_colis_livrer'); ?></h3>
                        <h1 id="total-etats-colis-livrer" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/factures.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('etats_colis_livrer/non_regle'); ?>">
                        <h3 class="bold no-margin"><?= _l('etat_colis_livrer_non_regle'); ?></h3>
                        <h1 id="total-etats-colis-livrer-non-regler" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/factures.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('etats_colis_livrer/regle'); ?>">
                        <h3 class="bold no-margin"><?= _l('etat_colis_livrer_regle'); ?></h3>
                        <h1 id="total-etats-colis-livrer-regler" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/factures.png') ?>" />
                    </a>
                </div>
                
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('demandes'); ?>">
                        <h3 class="bold no-margin"><?= _l('requests'); ?></h3>
                        <h1 id="total-demandes" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/demandes.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('demandes/en_cours'); ?>">
                        <h3 class="bold no-margin"><?= _l('requests_in_progress'); ?></h3>
                        <h1 id="total-demandes-en-cours" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/demandes.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('demandes/repondu'); ?>">
                        <h3 class="bold no-margin"><?= _l('requests_responded'); ?></h3>
                        <h1 id="total-demandes-repondu" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/demandes.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= point_relais_url('demandes/cloturer'); ?>">
                        <h3 class="bold no-margin"><?= _l('requests_fencing'); ?></h3>
                        <h1 id="total-demandes-cloturer" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/demandes.png') ?>" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
//Affichage du modal filtre statistique
include_once(APPPATH . 'views/point-relais/dashboard/modal-filtre-statistique.php');

?>