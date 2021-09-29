<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body pbot30">
            <div class="row home-summary">

                <center>
                    <i class="fa fa-filter filter-statistics" onclick="showModalFiltreStatistiqueDashbord();"></i>
                </center>
                
                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= admin_url('colis_en_attente'); ?>">
                        <h2 id="total-colis-en-attente" class="bold no-margin"><?php if (!has_permission('colis_en_attente', '', 'view')) { echo '0'; } else { echo total_rows('tblcolisenattente', array('colis_id' => NULL, 'date_creation' => date('Y-m-d'))); } ?></h2>
                        <span class="bold text-primary mtop15 inline-block"><i class="fa fa-archive"></i> <?= _l('als_colis_en_attente'); ?></span>
                    </a>
                </div>
                
                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= admin_url('colis/index?status=1'); ?>">
                        <h2 id="total-colis-en-cours" class="bold no-margin"><?php if (!has_permission('colis', '', 'view')) { echo '0'; } else { echo total_rows('tblcolis', array('status_id' => 1, 'status_reel !=' => 9, 'date_ramassage' => date('Y-m-d'))); } ?></h2>
                        <span class="bold text-warning mtop15 inline-block"><i class="fa fa-clock-o"></i> <?= _l('status_colis_current'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= admin_url('colis/index?status=2'); ?>">
                        <h2 id="total-colis-livre" class="bold no-margin"><?php if (!has_permission('colis', '', 'view')) { echo '0'; } else { echo total_rows('tblcolis', array('status_id' => 2, 'date_livraison' => date('Y-m-d'))); } ?></h2>
                        <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?= _l('colis_delivered'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center">
                    <a class="redirect-statistique" href="<?= admin_url('colis/index?status=3'); ?>">
                        <h2 id="total-colis-retourner" class="bold no-margin"><?php if (!has_permission('colis', '', 'view')) { echo '0'; } else { echo total_rows('tblcolis', array('status_id' => 3, 'date_livraison' => date('Y-m-d'))); } ?></h2>
                        <span class="bold mtop15 text-danger inline-block"><i class="fa fa-times"></i> <?= _l('colis_returned'); ?></span>
                    </a>
                </div>

                <div class="clearfix">  </div>
                <hr class="home-summary-separator"/>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= admin_url('expediteurs'); ?>">
                        <h2 id="total-clients" class="bold no-margin"><?php if (!has_permission('shipper', '', 'view')) { echo '0'; } else { echo total_rows('tblexpediteurs', array('date_created' => date('Y-m-d'))); } ?></h2>
                        <span class="bold text-info mtop15 inline-block"><i class="fa fa-users"></i> <?= _l('als_expediteur'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= admin_url('bon_livraison'); ?>">
                        <h2 id="total-bons-livraison" class="bold no-margin"><?php if (!has_permission('bon_livraison', '', 'view')) { echo '0'; } else { echo total_rows('tblbonlivraison', array('date_created' => date('Y-m-d'))); } ?></h2>
                        <span class="bold mtop15 text-default inline-block"><i class="fa fa-ticket"></i> <?= _l('delivery_notes'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center border-right">
                    <a class="redirect-statistique" href="<?= admin_url('bon_livraison/sortie'); ?>">
                        <h2 id="total-bons-livraison-sortie" class="bold no-margin"><?php if (!has_permission('bon_livraison', '', 'view')) { echo '0'; } else { echo total_rows('tblbonlivraison', array('type' => 1, 'date_created' => date('Y-m-d'))); } ?></h2>
                        <span class="bold mtop15 text-info inline-block"><i class="fa fa-ticket"></i> <?= _l('delivery_notes_exit'); ?></span>
                    </a>
                </div>

                <div class="col-md-5ths col-xs-6 text-center">
                    <a class="redirect-statistique" href="<?= admin_url('bon_livraison/retourner'); ?>">
                        <h2 id="total-bons-livraison-retourner" class="bold no-margin"><?php if (!has_permission('bon_livraison', '', 'view')) { echo '0'; } else { echo total_rows('tblbonlivraison', array('type' => 2, 'date_created' => date('Y-m-d'))); } ?></h2>
                        <span class="bold mtop15 text-danger inline-block"><i class="fa fa-ticket"></i> <?= _l('delivery_notes_returned'); ?></span>
                    </a>
                </div>

                <center>
                    <i class="fa fa-chevron-circle-down circle-down-statistics show_hide_bloc"></i>
                </center>

                <div id="bloc_to_show_hide" class="hide">
                    <div class="clearfix"></div>
                    <hr class="home-summary-separator"/>
                    
                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('staff'); ?>">
                            <h2 id="total-utilisateurs" class="bold no-margin"><?php if (!has_permission('staff', '', 'view')) { echo '0'; } else { echo total_rows('tblstaff', 'datecreated LIKE "' . date('Y-m-d') . '"'); } ?></h2>
                            <span class="bold text-info mtop15 inline-block"><i class="fa fa-users"></i> <?= _l('staffs'); ?></span>
                        </a>
                    </div>
                    
                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('etat_colis_livrer'); ?>">
                            <h2 id="total-etat-colis-livre" class="bold no-margin"><?php if (!has_permission('etat_colis_livrer', '', 'view')) { echo '0'; } else { echo total_rows('tbletatcolislivre', array('date_created' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-default inline-block"><i class="fa fa-file"></i> <?= _l('als_etat_colis_livrer'); ?></span>
                        </a>
                    </div>
                    
                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('etat_colis_livrer/non_regle'); ?>">
                            <h2 id="total-etat-colis-livre-non-regle" class="bold no-margin"><?php if (!has_permission('etat_colis_livrer', '', 'view')) { echo '0'; } else { echo total_rows('tbletatcolislivre', array('status' => 1, 'date_created' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-danger inline-block"><i class="fa fa-file"></i> <?= _l('etat_colis_livrer_non_regle'); ?></span>
                        </a>
                    </div>
                    
                    <div class="col-md-5ths col-xs-6 text-center">
                        <a class="redirect-statistique" href="<?= admin_url('etat_colis_livrer/regle'); ?>">
                            <h2 id="total-etat-colis-livre-regle" class="bold no-margin"><?php if (!has_permission('etat_colis_livrer', '', 'view')) { echo '0'; } else { echo total_rows('tbletatcolislivre', array('status' => 2, 'date_created' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-success inline-block"><i class="fa fa-file"></i> <?= _l('etat_colis_livrer_regle'); ?></span>
                        </a>
                    </div>
                
                    <div class="clearfix"></div>
                    <hr class="home-summary-separator"/>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('factures'); ?>">
                            <h2 id="total-factures" class="bold no-margin"><?php if (!has_permission('invoices', '', 'view')) { echo '0'; } else { echo total_rows('tblfactures', array('date_created' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-muted inline-block"><i class="fa fa-balance-scale"></i> <?= _l('invoices'); ?></span>
                        </a>
                    </div>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('factures/index/false/2'); ?>">
                            <h2 id="total-factures-paye" class="bold no-margin"><?php if (!has_permission('invoices', '', 'view')) { echo '0'; } else { echo total_rows('tblfactures', array('status' => 2, 'date_created' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-success inline-block"><i class="fa fa-balance-scale"></i> <?= _l('invoices_paid'); ?></span>
                        </a>
                    </div>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('factures/index/false/1'); ?>">
                            <h2 id="total-factures-impaye" class="bold no-margin"><?php if (!has_permission('invoices', '', 'view')) { echo '0'; } else { echo total_rows('tblfactures', array('status' => 1, 'date_created' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-danger inline-block"><i class="fa fa-balance-scale"></i> <?= _l('invoices_unpaid'); ?></span>
                        </a>
                    </div>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('factures_internes'); ?>">
                            <h2 id="total-factures-internes" class="bold no-margin"><?php if (!has_permission('factures_internes', '', 'view')) { echo '0'; } else { echo total_rows('tblfacturesinternes', array('date_created' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-muted inline-block"><i class="fa fa-balance-scale"></i> <?= _l('als_factures_internes'); ?></span>
                        </a>
                    </div>

                    <div class="clearfix">  </div>
                    <hr class="home-summary-separator"/>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('payments'); ?>">
                            <h2 id="total-nombre-paiements" class="bold no-margin"><?php if (!has_permission('payments', '', 'view')) { echo '0'; } else { echo total_rows('tblfactureinternepaymentrecords', array('daterecorded' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-info inline-block"><i class="fa fa-money"></i> <?= _l('als_payments'); ?></span>
                        </a>
                    </div>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('payments'); ?>">
                            <h2 id="total-paiements" class="bold no-margin"><?php if (!has_permission('payments', '', 'view')) { echo '0'; } else { echo sum_from_table('tblfactureinternepaymentrecords', array('field' => 'amount'), 'daterecorded = "' . date('Y-m-d') . '"'); } ?></h2>
                            <span class="bold mtop15 text-success inline-block"><i class="fa fa-heartbeat"></i> <?= _l('total_payments'); ?></span>
                        </a>
                    </div>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('expenses/list_expenses'); ?>">
                            <h2 id="total-nombre-depenses" class="bold no-margin"><?php if (!has_permission('expenses', '', 'view')) { echo '0'; } else { echo total_rows('tblexpenses', array('dateadded' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-default inline-block"><i class="fa fa-heartbeat"></i> <?= _l('als_expenses'); ?></span>
                        </a>
                    </div>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('expenses/list_expenses'); ?>">
                            <h2 id="total-depenses" class="bold no-margin"><?php if (!has_permission('expenses', '', 'view')) { echo '0'; } else { echo sum_from_table('tblexpenses', array('field' => 'amount'), 'dateadded = "' . date('Y-m-d') . '"'); } ?></h2>
                            <span class="bold mtop15 text-danger inline-block"><i class="fa fa-heartbeat"></i> <?= _l('total_expenses'); ?></span>
                        </a>
                    </div>

                    <div class="clearfix">  </div>
                    <hr class="home-summary-separator"/>

                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('supports'); ?>">
                            <h2 id="total-supports" class="bold no-margin"><?php if (!has_permission('supports', '', 'view')) { echo '0'; } else { echo total_rows('tblsupports', array('dateadded' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-warning inline-block"><i class="fa fa-tasks"></i> <?= _l('als_supports'); ?></span>
                        </a>
                    </div>
                    
                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('demandes'); ?>">
                            <h2 id="total-demandes" class="bold no-margin"><?php if (!has_permission('demandes', '', 'view')) { echo '0'; } else { echo total_rows('tbldemandes', array('status' => 1, 'datecreated' => date('Y-m-d'))); } ?></h2>
                            <span class="bold mtop15 text-default inline-block"><i class="fa fa-warning"></i> <?= _l('requests'); ?></span>
                        </a>
                    </div>
                    
                    <div class="col-md-5ths col-xs-6 text-center border-right">
                        <a class="redirect-statistique" href="<?= admin_url('demandes/en_cours'); ?>">
                            <h2 id="total-demandes-en-cours"  class="bold no-margin"></h2>
                            <span class="bold mtop15 text-warning inline-block"><i class="fa fa-warning"></i> <?= _l('requests_in_progress'); ?></span>
                        </a>
                    </div>

                    <div class="col-md-5ths col-xs-6 text-center">
                        <a class="redirect-statistique" href="<?= admin_url('demandes/cloturer'); ?>">
                            <h2 id="total-demandes-cloturer" class="bold no-margin"></h2>
                            <span class="bold mtop15 text-success inline-block"><i class="fa fa-warning"></i> <?= _l('requests_fencing'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filter-statisique-dashbord" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="edit-title"><?= _l('filter'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
					    <div class="row">
							<div class="col-md-12">
								<?= render_select('periode', $periode, array('value', array('name')), 'periode'); ?>
							</div>
                            <div id="wait-filtre-periode-statisique" class="col-md-12 text-center display-none">
                                <img class="width50 mtop10" src="<?= site_url('assets/images/wait.gif'); ?>" alt="Veuillez patienter SVP..." />
                                <h6 class="bold">Patientez pendant le chargement du contenu.</h6>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>