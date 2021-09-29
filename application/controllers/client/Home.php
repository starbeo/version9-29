<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
    }

    /**
     * This is client home view
     */
    public function index()
    {
        // Get all activities log
        $this->load->model('misc_model');
        $data['activities'] = $this->misc_model->get_activities_log_client();
        // Get all activities log SMS
        $data['activities_log_sms'] = $this->misc_model->get_activities_log_sms_by_client();
        // Get sliders
        $this->load->model('sliders_model');
        $data['sliders'] = $this->sliders_model->get();
        // GET Periode
        $data['periodes'] = array(
            array('value' => 'all', 'name' => _l('all')),
            array('value' => 'day', 'name' => _l('per_day')),
            array('value' => 'week', 'name' => _l('per_week')),
            array('value' => 'month', 'name' => _l('per_month'))
        );


        $this->db->where('id', get_expediteur_user_id());
        $mhd = $this->db->get('tblexpediteurs')->row();
        $data['mhd'] =  $mhd->pass_check;
        $data['title'] = _l('dashboard_string');
        $data['bodyclass'] = 'home';
        $this->load->view('client/dashboard/home', $data);
    }

    /**
     * Change language 
     */
    public function change_language($language = '')
    {
        $this->session->set_userdata(array('language' => $language));
        redirect(client_url('home'));
    }

    /**
     * Statistique by periode
     */
    public function statistique_by_periode()
    {
        if ($this->input->is_ajax_request()) {
            $clientId = get_expediteur_user_id();
            $data = $this->input->post();
            $today = date('Y-m-d');
            $totalRowsColisEnAttente = 0;
            if (is_numeric($clientId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsColisEnAttente = total_rows('tblcolisenattente', array('id_expediteur' => $clientId, 'colis_id' => NULL));
                        break;
                    case 'day':
                        $totalRowsColisEnAttente = total_rows('tblcolisenattente', array('id_expediteur' => $clientId, 'colis_id' => NULL, 'date_creation' => $today));
                        break;
                    case 'week':
                        $totalRowsColisEnAttente = total_rows('tblcolisenattente', 'id_expediteur = ' . $clientId . ' AND colis_id IS NULL AND WEEK(date_creation, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsColisEnAttente = total_rows('tblcolisenattente', 'id_expediteur = ' . $clientId . ' AND colis_id IS NULL AND date_creation > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsColis = 0;
            $totalRowsColisLivre = 0;
            $totalRowsColisRetourner = 0;
            if (is_numeric($clientId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsColis = total_rows('tblcolis', array('id_expediteur' => $clientId));
                        $totalRowsColisLivre = total_rows('tblcolis', array('id_expediteur' => $clientId, 'status_id' => 2));
                        $totalRowsColisRetourner = total_rows('tblcolis', array('id_expediteur' => $clientId, 'status_id' => 3));
                        break;
                    case 'day':
                        $totalRowsColis = total_rows('tblcolis', 'id_expediteur = ' . $clientId . ' AND (date_ramassage = "' . $today . '" OR date_livraison = "' . $today . '" OR date_retour = "' . $today . '")');
                        $totalRowsColisLivre = total_rows('tblcolis', array('id_expediteur' => $clientId, 'status_id' => 2, 'date_livraison' => $today));
                        $totalRowsColisRetourner = total_rows('tblcolis', array('id_expediteur' => $clientId, 'status_id' => 3, 'date_retour' => $today));
                        break;
                    case 'week':
                        $totalRowsColis = total_rows('tblcolis', 'id_expediteur = ' . $clientId . ' AND (WEEK(date_ramassage, 1) = WEEK(CURDATE(), 1) OR WEEK(date_livraison, 1) = WEEK(CURDATE(), 1) OR WEEK(date_retour, 1) = WEEK(CURDATE(), 1))');
                        $totalRowsColisLivre = total_rows('tblcolis', 'id_expediteur = ' . $clientId . ' AND status_id = 2 AND WEEK(date_livraison, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsColisRetourner = total_rows('tblcolis', 'id_expediteur = ' . $clientId . ' AND status_id = 3 AND WEEK(date_retour, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsColis = total_rows('tblcolis', 'id_expediteur = ' . $clientId . ' AND (date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH) OR date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH) OR date_retour > DATE_SUB(now(), INTERVAL 1 MONTH))');
                        $totalRowsColisLivre = total_rows('tblcolis', 'id_expediteur = ' . $clientId . ' AND status_id = 2 AND date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsColisRetourner = total_rows('tblcolis', 'id_expediteur = ' . $clientId . ' AND status_id = 3 AND date_retour > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $totalRowsBonsLivraison = 0;
            if (is_numeric($clientId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraisoncustomer', 'id_expediteur = ' . $clientId);
                        break;
                    case 'day':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraisoncustomer', 'id_expediteur = ' . $clientId . ' AND date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraisoncustomer', 'id_expediteur = ' . $clientId . ' AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraisoncustomer', 'id_expediteur = ' . $clientId . ' AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsFactures = 0;
            if (is_numeric($clientId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsFactures = total_rows('tblfactures', 'id_expediteur = ' . $clientId);
                        break;
                    case 'day':
                        $totalRowsFactures = total_rows('tblfactures', 'id_expediteur = ' . $clientId . ' AND date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsFactures = total_rows('tblfactures', 'id_expediteur = ' . $clientId . ' AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsFactures = total_rows('tblfactures', 'id_expediteur = ' . $clientId . ' AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsDemandes = 0;
            if (is_numeric($clientId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsDemandes = total_rows('tbldemandes', 'client_id = ' . $clientId);
                        break;
                    case 'day':
                        $totalRowsDemandes = total_rows('tbldemandes', 'client_id = ' . $clientId . ' AND datecreated LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsDemandes = total_rows('tbldemandes', 'client_id = ' . $clientId . ' AND WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsDemandes = total_rows('tbldemandes', 'client_id = ' . $clientId . ' AND datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            $totalRowsReclamations = 0;
            if (is_numeric($clientId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsReclamations = total_rows('tblreclamations', array('relation_id' => $clientId));
                        break;
                    case 'day':
                        $totalRowsReclamations = total_rows('tblreclamations', array('relation_id' => $clientId, 'date_created' => $today));
                        break;
                    case 'week':
                        $totalRowsReclamations = total_rows('tblreclamations', 'relation_id = ' . $clientId . ' AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsReclamations = total_rows('tblreclamations', 'relation_id = ' . $clientId . ' AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            echo json_encode(
                array(
                    'totalRowsColisEnAttente' => $totalRowsColisEnAttente,
                    'totalRowsColis' => $totalRowsColis,
                    'totalRowsColisLivre' => $totalRowsColisLivre,
                    'totalRowsColisRetourner' => $totalRowsColisRetourner,
                    'totalRowsBonsLivraison' => $totalRowsBonsLivraison,
                    'totalRowsFactures' => $totalRowsFactures,
                    'totalRowsDemandes' => $totalRowsDemandes,
                    'totalRowsReclamations' => $totalRowsReclamations
                )
            );
        }

    }





}
