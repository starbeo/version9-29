<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body pbot30">
            <div class="row home-summary">
                <center>
                    <i class="fa fa-filter filter-statistics" onclick="showModalFiltreStatistiqueDashbord();"></i>
                </center>
                
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('colis_en_attente'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_en_attente'); ?></h3>
                        <h1 id="total-colis-en-attente" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis-en-attente.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('colis'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis'); ?></h3>
                        <h1 id="total-colis" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('colis/livrer'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_delivered'); ?></h3>
                        <h1 id="total-colis-livrer" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis-livrer.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('colis/retourner'); ?>">
                        <h3 class="bold no-margin"><?= _l('colis_returned'); ?></h3>
                        <h1 id="total-colis-retourner" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/colis-retourner.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('bons_livraison'); ?>">
                        <h3 class="bold no-margin"><?= _l('delivery_notes'); ?></h3>
                        <h1 id="total-bons-livraison" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/bons-de-livraison.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('factures'); ?>">
                        <h3 class="bold no-margin"><?= _l('factures'); ?></h3>
                        <h1 id="total-factures" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/factures.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('demandes'); ?>">
                        <h3 class="bold no-margin"><?= _l('demandes'); ?></h3>
                        <h1 id="total-demandes" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/demandes.png') ?>" />
                    </a>
                </div>
                <div class="col-md-1 col-xs-6 bloc-statistique-dashboard">
                    <a class="redirect-statistique" href="<?= client_url('reclamations'); ?>">
                        <h3 class="bold no-margin"><?= _l('reclamations'); ?></h3>
                        <h1 id="total-reclamations" class="bold no-margin">0</h1>
                        <img class="icon-dashboard" src="<?= site_url('assets/images/defaults/dashboard/reclamations.png') ?>" />
                    </a>
                </div>
                
                <!--div class="clearfix">  </div>
                <hr class="home-summary-separator"/>
                
                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('colis_en_attente'); ?>">
                        <h2 id="total-colis-en-attente" class="bold no-margin">0</h2>
                        <span class="bold text-primary mtop15 inline-block"><i class="fa fa-archive"></i> <?= _l('colis_en_attente'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('colis'); ?>">
                        <h2 id="total-colis" class="bold no-margin">0</h2>
                        <span class="bold text-default mtop15 inline-block"><i class="fa fa-archive"></i> <?= _l('colis'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('colis/index/2'); ?>">
                        <h2 id="total-colis-livrer" class="bold no-margin">0</h2>
                        <span class="bold text-success mtop15 inline-block"><i class="fa fa-archive"></i> <?= _l('colis_delivered'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('colis/index/3'); ?>">
                        <h2 id="total-colis-retourner" class="bold no-margin">0</h2>
                        <span class="bold text-danger mtop15 inline-block"><i class="fa fa-archive"></i> <?= _l('colis_returned'); ?></span>
                    </a>
                </div>

                <div class="clearfix">  </div>
                <hr class="home-summary-separator"/>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('bons_livraison'); ?>">
                        <h2 id="total-bons-livraison" class="bold no-margin">0</h2>
                        <span class="bold text-info mtop15 inline-block"><i class="fa fa-file-text-o"></i> <?= _l('delivery_notes'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('factures'); ?>">
                        <h2 id="total-factures" class="bold no-margin">0</h2>
                        <span class="bold mtop15 text-default inline-block"><i class="fa fa-balance-scale"></i> <?= _l('factures'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('demandes'); ?>">
                        <h2 id="total-demandes" class="bold no-margin">0</h2>
                        <span class="bold text-warning mtop15 inline-block"><i class="fa fa-warning"></i> <?= _l('demandes'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= client_url('reclamations'); ?>">
                        <h2 id="total-reclamations" class="bold no-margin">0</h2>
                        <span class="bold text-warning mtop15 inline-block"><i class="fa fa-envelope-o"></i> <?= _l('reclamations'); ?></span>
                    </a>
                </div-->
            </div>
        </div>
    </div>
</div>
<?php
//Affichage du modal filtre statistique
include_once(APPPATH . 'views/client/dashboard/modal-filtre-statistique.php');

?>