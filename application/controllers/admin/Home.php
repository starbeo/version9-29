<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
    }
    
    /* This is admin home view */
    public function index()
    {
        // LOAD MODEL
        $this->load->model('expediteurs_model');
        $this->load->model('staff_model');
        $this->load->model('misc_model');
        $this->load->model('cron_model');
        
        //$this->cron_model->sent_email_to_admin();
        // GET ALL CLIENTS
        $data['clients'] = $this->expediteurs_model->get();
        // GET ALL LIVREURS
        $data['livreurs'] = $this->staff_model->get_livreurs();
        // GET ALL TACHES STAFF
        $data['taches'] = array();
        //$data['taches'] = $this->misc_model->get_taches_staff();
        // GET ALL ACTIVITIES STAFF
        $data['activities'] = array();
        //$data['activities'] = $this->misc_model->get_activities_staff();
        // GET ALL APPELS LIVREUR
        $data['appels'] = array();
        //$data['appels'] = $this->misc_model->get_appels_livreur();
        // GET ALL ACTIVITIES LOG SMS
        $data['activities_log_sms'] = array();
        //$data['activities_log_sms'] = $this->misc_model->get_activities_log_sms();
        // GET Periode
        $data['periode'] = array(
            array('value' => 'day', 'name' => _l('per_day')),
            array('value' => 'week', 'name' => _l('per_week')),
            array('value' => 'month', 'name' => _l('per_month'))
        );
        
        $data['title'] = _l('dashboard_string');
        $data['bodyclass'] = 'home';
        $this->load->view('admin/home', $data);
    }
    
    /* Change language */
    public function change_language($language = '')
    {
        $this->session->set_userdata(array('language' => $language));
        redirect(site_url('admin'));
    }
    
    public function statistique_by_periode()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $today = date('Y-m-d');
            $totalRowsColisEnAttente = 0;
            if (has_permission('colis_en_attente', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsColisEnAttente = total_rows('tblcolisenattente', array('colis_id' => NULL, 'date_creation' => $today));
                        break;
                    case 'week':
                        $totalRowsColisEnAttente = total_rows('tblcolisenattente', 'colis_id IS NULL AND WEEK(date_creation, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsColisEnAttente = total_rows('tblcolisenattente', 'colis_id IS NULL AND date_creation > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsColisEnCours = 0;
            $totalRowsColisLivre = 0;
            $totalRowsColisRetourner = 0;
            if (has_permission('colis', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsColisEnCours = total_rows('tblcolis', array('status_id' => 1, 'status_reel !=' => 9, 'etat_id' => 1, 'date_ramassage' => $today));
                        $totalRowsColisLivre = total_rows('tblcolis', array('status_id' => 2, 'date_livraison' => $today));
                        $totalRowsColisRetourner = total_rows('tblcolis', array('status_id' => 3, 'date_livraison' => $today));
                        break;
                    case 'week':
                        $totalRowsColisEnCours = total_rows('tblcolis', 'status_id = 1 AND status_reel != 9 AND etat_id = 1 AND WEEK(date_ramassage, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsColisLivre = total_rows('tblcolis', 'status_id = 2 AND WEEK(date_livraison, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsColisRetourner = total_rows('tblcolis', 'status_id = 3 AND WEEK(date_livraison, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsColisEnCours = total_rows('tblcolis', 'status_id = 1 AND status_reel != 9 AND date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsColisLivre = total_rows('tblcolis', 'status_id = 2 AND date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsColisRetourner = total_rows('tblcolis', 'status_id = 3 AND date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsClients = 0;
            if (has_permission('shipper', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsClients = total_rows('tblexpediteurs', 'date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsClients = total_rows('tblexpediteurs', 'WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsClients = total_rows('tblexpediteurs', 'date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsBonsLivraison = 0;
            $totalRowsBonsLivraisonSortie = 0;
            $totalRowsBonsLivraisonRetourner = 0;
            if (has_permission('bon_livraison', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraison', 'date_created LIKE "' . $today . '%"');
                        $totalRowsBonsLivraisonSortie = total_rows('tblbonlivraison', 'type = 1 AND date_created LIKE "' . $today . '%"');
                        $totalRowsBonsLivraisonRetourner = total_rows('tblbonlivraison', 'type = 2 AND date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraison', 'WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsBonsLivraisonSortie = total_rows('tblbonlivraison', 'type = 1 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsBonsLivraisonRetourner = total_rows('tblbonlivraison', 'type = 2 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraison', 'date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsBonsLivraisonSortie = total_rows('tblbonlivraison', 'type = 1 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsBonsLivraisonRetourner = total_rows('tblbonlivraison', 'type = 2 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsUtilisateurs = 0;
            if (has_permission('staff', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $utilisateurs = total_rows('tblstaff', 'datecreated LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $utilisateurs = total_rows('tblstaff', 'WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $utilisateurs = total_rows('tblstaff', 'datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
                if($utilisateurs > 0) {
                    $totalRowsUtilisateurs = $utilisateurs - 1;
                }
            }
            $totalRowsEtatColisLivre = 0;
            $totalRowsEtatColisLivreNonRegle = 0;
            $totalRowsEtatColisLivreRegle = 0;
            if (has_permission('etat_colis_livrer', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsEtatColisLivre = total_rows('tbletatcolislivre', 'date_created LIKE "' . $today . '%"');
                        $totalRowsEtatColisLivreNonRegle = total_rows('tbletatcolislivre', 'etat = 1 AND date_created LIKE "' . $today . '%"');
                        $totalRowsEtatColisLivreRegle = total_rows('tbletatcolislivre', 'etat = 2 AND date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsEtatColisLivre = total_rows('tbletatcolislivre', 'WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsEtatColisLivreNonRegle = total_rows('tbletatcolislivre', 'etat = 1 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsEtatColisLivreRegle = total_rows('tbletatcolislivre', 'etat = 2 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsEtatColisLivre = total_rows('tbletatcolislivre', 'date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsEtatColisLivreNonRegle = total_rows('tbletatcolislivre', 'etat = 1 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsEtatColisLivreRegle = total_rows('tbletatcolislivre', 'etat = 2 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsFactures = 0;
            $totalRowsFacturesPaye = 0;
            $totalRowsFacturesImpaye = 0;
            if (has_permission('invoices', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsFactures = total_rows('tblfactures', 'date_created LIKE "' . $today . '%"');
                        $totalRowsFacturesPaye = total_rows('tblfactures', 'status = 2 AND date_created LIKE "' . $today . '%"');
                        $totalRowsFacturesImpaye = total_rows('tblfactures', 'status = 1 AND date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsFactures = total_rows('tblfactures', 'WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsFacturesPaye = total_rows('tblfactures', 'status = 2 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsFacturesImpaye = total_rows('tblfactures', 'status = 1 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsFactures = total_rows('tblfactures', 'date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsFacturesPaye = total_rows('tblfactures', 'status = 2 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsFacturesImpaye = total_rows('tblfactures', 'status = 1 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsFacturesInternes = 0;
            if (has_permission('factures_internes', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsFacturesInternes = total_rows('tblfacturesinternes', 'date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsFacturesInternes = total_rows('tblfacturesinternes', 'WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsFacturesInternes = total_rows('tblfacturesinternes', 'date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsNombrePaiements = 0;
            if (has_permission('payments', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsNombrePaiements = total_rows('tblfactureinternepaymentrecords', array('daterecorded' => $today));
                        $totalRowsPaiements = sum_from_table('tblfactureinternepaymentrecords', array('field' => 'amount'), 'daterecorded = "' . $today . '"');
                        break;
                    case 'week':
                        $totalRowsNombrePaiements = total_rows('tblfactureinternepaymentrecords', 'WEEK(daterecorded, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsPaiements = sum_from_table('tblfactureinternepaymentrecords', array('field' => 'amount'), 'WEEK(daterecorded, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsNombrePaiements = total_rows('tblfactureinternepaymentrecords', 'daterecorded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsPaiements = sum_from_table('tblfactureinternepaymentrecords', array('field' => 'amount'), 'daterecorded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsNombreDepenses = 0;
            $totalRowsDepenses = 0;
            if (has_permission('expenses', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsNombreDepenses = total_rows('tblexpenses', array('dateadded' => $today));
                        $totalRowsDepenses = sum_from_table('tblexpenses', array('field' => 'amount'), 'dateadded = "' . $today . '"');
                        break;
                    case 'week':
                        $totalRowsNombreDepenses = total_rows('tblexpenses', 'WEEK(dateadded, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsDepenses = sum_from_table('tblexpenses', array('field' => 'amount'), 'WEEK(dateadded, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsNombreDepenses = total_rows('tblexpenses', 'dateadded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsDepenses = sum_from_table('tblexpenses', array('field' => 'amount'), 'dateadded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsSupports = 0;
            if (has_permission('supports', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsSupports = total_rows('tblsupports', 'dateadded LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsSupports = total_rows('tblsupports', 'WEEK(dateadded, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsSupports = total_rows('tblsupports', 'dateadded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsDemandes = 0;
            $totalRowsDemandesEnCours = 0;
            $totalRowsDemandesCloturer = 0;
            if (has_permission('demandes', '', 'view')) {
                switch ($data['periode']) {
                    case 'day':
                        $totalRowsDemandes = total_rows('tbldemandes', 'datecreated LIKE "' . $today . '%"');
                        $totalRowsDemandesEnCours = total_rows('tbldemandes', 'status = 1 AND datecreated LIKE "' . $today . '%"');
                        $totalRowsDemandesCloturer = total_rows('tbldemandes', 'status = 4 AND datecreated LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsDemandes = total_rows('tbldemandes', 'WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsDemandesEnCours = total_rows('tbldemandes', 'status = 1 AND WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsDemandesCloturer = total_rows('tbldemandes', 'status = 4 AND WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsDemandes = total_rows('tbldemandes', 'datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsDemandesEnCours = total_rows('tbldemandes', 'status = 1 AND datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsDemandesCloturer = total_rows('tbldemandes', 'status = 4 AND datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            
            echo json_encode(
                array(
                    'totalRowsColisEnAttente' => $totalRowsColisEnAttente,
                    'totalRowsColisEnCours' => $totalRowsColisEnCours,
                    'totalRowsColisLivre' => $totalRowsColisLivre,
                    'totalRowsColisRetourner' => $totalRowsColisRetourner,
                    'totalRowsClients' => $totalRowsClients,
                    'totalRowsBonsLivraison' => $totalRowsBonsLivraison,
                    'totalRowsBonsLivraisonSortie' => $totalRowsBonsLivraisonSortie,
                    'totalRowsBonsLivraisonRetourner' => $totalRowsBonsLivraisonRetourner,
                    'totalRowsUtilisateurs' => $totalRowsUtilisateurs,
                    'totalRowsEtatColisLivre' => $totalRowsEtatColisLivre,
                    'totalRowsEtatColisLivreNonRegle' => $totalRowsEtatColisLivreNonRegle,
                    'totalRowsEtatColisLivreRegle' => $totalRowsEtatColisLivreRegle,
                    'totalRowsFactures' => $totalRowsFactures,
                    'totalRowsFacturesPaye' => $totalRowsFacturesPaye,
                    'totalRowsFacturesImpaye' => $totalRowsFacturesImpaye,
                    'totalRowsFacturesInternes' => $totalRowsFacturesInternes,
                    'totalRowsNombrePaiements' => $totalRowsNombrePaiements,
                    'totalRowsPaiements' => $totalRowsPaiements,
                    'totalRowsNombreDepenses' => $totalRowsNombreDepenses,
                    'totalRowsDepenses' => $totalRowsDepenses,
                    'totalRowsSupports' => $totalRowsSupports,
                    'totalRowsDemandes' => $totalRowsDemandes,
                    'totalRowsDemandesEnCours' => $totalRowsDemandesEnCours,
                    'totalRowsDemandesCloturer' => $totalRowsDemandesCloturer
                )
            );
        }
    }
}
