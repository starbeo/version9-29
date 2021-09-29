<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
    }

    /**
     * This is point relais home view
     */
    public function index()
    {
        // Get all activities log
        $this->load->model('misc_model');
        $data['activities'] = $this->misc_model->get_activities_staff();
        // Get periode
        $data['periodes'] = array(
            array('value' => 'all', 'name' => _l('all')),
            array('value' => 'day', 'name' => _l('per_day')),
            array('value' => 'week', 'name' => _l('per_week')),
            array('value' => 'month', 'name' => _l('per_month'))
        );

        $data['title'] = _l('dashboard_string');
        $data['bodyclass'] = 'home';
        $this->load->view('point-relais/dashboard/home', $data);
    }

    /**
     * Statistique by periode
     */
    public function statistique_by_periode()
    {
        if ($this->input->is_ajax_request()) {
            $staffId = get_staff_user_id();
            $pointsRelaisStaff = get_staff_points_relais();
            $data = $this->input->post();
            $today = date('Y-m-d');

            $wherePointRelaisIdIn = ' point_relai_id IN ' . $pointsRelaisStaff;

            $totalRowsColis = 0;
            $totalRowsColisEnCoursAuPointRelais = 0;
            $totalRowsColisReceptionAuPointRelais = 0;
            $totalRowsColisReceptionLivreurPointRelais = 0;
            $totalRowsColisLivre = 0;
            $totalRowsColisRetourner = 0;
            if (is_numeric($staffId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsColis = total_rows('tblcolis', $wherePointRelaisIdIn);
                        $totalRowsColisEnCoursAuPointRelais = total_rows('tblcolis', 'status_id = 100 AND ' . $wherePointRelaisIdIn);
                        $totalRowsColisReceptionAuPointRelais = total_rows('tblcolis', 'status_id = 101 AND ' . $wherePointRelaisIdIn);
                        $totalRowsColisReceptionLivreurPointRelais = total_rows('tblcolis', 'status_id = 102 AND ' . $wherePointRelaisIdIn);
                        $totalRowsColisLivre = total_rows('tblcolis', 'status_id = 2 AND ' . $wherePointRelaisIdIn);
                        $totalRowsColisRetourner = total_rows('tblcolis', 'status_id = 3 AND ' . $wherePointRelaisIdIn);
                        break;
                    case 'day':
                        $totalRowsColis = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND (date_ramassage = "' . $today . '" OR date_livraison = "' . $today . '" OR date_retour = "' . $today . '")');
                        $totalRowsColisEnCoursAuPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 100 AND date_ramassage = "' . $today . '"');
                        $totalRowsColisReceptionAuPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 101 AND date_ramassage = "' . $today . '"');
                        $totalRowsColisReceptionLivreurPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 102 AND date_ramassage = "' . $today . '"');
                        $totalRowsColisLivre = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 2 AND date_livraison = "' . $today . '"');
                        $totalRowsColisRetourner = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 3 AND date_retour = "' . $today . '"');
                        break;
                    case 'week':
                        $totalRowsColis = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND (WEEK(date_ramassage, 1) = WEEK(CURDATE(), 1) OR WEEK(date_livraison, 1) = WEEK(CURDATE(), 1) OR WEEK(date_retour, 1) = WEEK(CURDATE(), 1))');
                        $totalRowsColisEnCoursAuPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 100 AND WEEK(date_ramassage, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsColisReceptionAuPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 101 AND WEEK(date_ramassage, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsColisReceptionLivreurPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 102 AND WEEK(date_ramassage, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsColisLivre = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 2 AND WEEK(date_livraison, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsColisRetourner = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 3 AND WEEK(date_retour, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsColis = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND (date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH) OR date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH) OR date_retour > DATE_SUB(now(), INTERVAL 1 MONTH))');
                        $totalRowsColisEnCoursAuPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 100 AND date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsColisReceptionAuPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 101 AND date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsColisReceptionLivreurPointRelais = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 102 AND date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsColisLivre = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 2 AND date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsColisRetourner = total_rows('tblcolis', $wherePointRelaisIdIn . ' AND status_id = 3 AND date_retour > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $totalRowsBonsLivraison = 0;
            $totalRowsBonsLivraisonSortie = 0;
            $totalRowsBonsLivraisonRetourner = 0;
            if (is_numeric($staffId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraison', $wherePointRelaisIdIn);
                        $totalRowsBonsLivraisonSortie = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 1');
                        $totalRowsBonsLivraisonRetourner = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 2');
                        break;
                    case 'day':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND date_created LIKE "' . $today . '%"');
                        $totalRowsBonsLivraisonSortie = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 1 AND date_created LIKE "' . $today . '%"');
                        $totalRowsBonsLivraisonRetourner = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 2 AND date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsBonsLivraisonSortie = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 1 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsBonsLivraisonRetourner = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 2 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsBonsLivraison = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsBonsLivraisonSortie = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 1 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsBonsLivraisonRetourner = total_rows('tblbonlivraison', $wherePointRelaisIdIn . ' AND type = 2 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $totalRowsEtatsColisLivrer = 0;
            $totalRowsEtatsColisLivrerNonRegler = 0;
            $totalRowsEtatsColisLivrerRegler = 0;
            if (is_numeric($staffId)) {
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsEtatsColisLivrer = total_rows('tbletatcolislivre', $wherePointRelaisIdIn);
                        $totalRowsEtatsColisLivrerNonRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 1');
                        $totalRowsEtatsColisLivrerRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 2');
                        break;
                    case 'day':
                        $totalRowsEtatsColisLivrer = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND date_created LIKE "' . $today . '%"');
                        $totalRowsEtatsColisLivrerNonRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 1 AND date_created LIKE "' . $today . '%"');
                        $totalRowsEtatsColisLivrerRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 2 AND date_created LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsEtatsColisLivrer = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsEtatsColisLivrerNonRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 1 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsEtatsColisLivrerRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 2 AND WEEK(date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsEtatsColisLivrer = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsEtatsColisLivrerNonRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 1 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsEtatsColisLivrerRegler = total_rows('tbletatcolislivre', $wherePointRelaisIdIn . ' AND etat = 2 AND date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $totalRowsDemandes = 0;
            $totalRowsDemandesEnCours = 0;
            $totalRowsDemandesRepondu = 0;
            $totalRowsDemandesCloturer = 0;
            if (is_numeric($staffId)) {
                $whereDemandeAddedFrom = 'addedfrom = ' . get_staff_user_id();
                switch ($data['periode']) {
                    case 'all':
                        $totalRowsDemandes = total_rows('tbldemandes', $whereDemandeAddedFrom);
                        $totalRowsDemandesEnCours = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 1');
                        $totalRowsDemandesRepondu = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 2');
                        $totalRowsDemandesCloturer = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 4');
                        break;
                    case 'day':
                        $totalRowsDemandes = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND datecreated LIKE "' . $today . '%"');
                        $totalRowsDemandesEnCours = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 1 AND datecreated LIKE "' . $today . '%"');
                        $totalRowsDemandesRepondu = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 2 AND datecreated LIKE "' . $today . '%"');
                        $totalRowsDemandesCloturer = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 4 AND datecreated LIKE "' . $today . '%"');
                        break;
                    case 'week':
                        $totalRowsDemandes = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsDemandesEnCours = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 1 AND WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsDemandesRepondu = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 2 AND WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        $totalRowsDemandesCloturer = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 4 AND WEEK(datecreated, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        $totalRowsDemandes = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsDemandesEnCours = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 1 AND datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsDemandesRepondu = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 2 AND datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        $totalRowsDemandesCloturer = total_rows('tbldemandes', $whereDemandeAddedFrom . ' AND status = 4 AND datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            echo json_encode(
                array(
                    'totalRowsColis' => $totalRowsColis,
                    'totalRowsColisEnCoursAuPointRelai' => $totalRowsColisEnCoursAuPointRelais,
                    'totalRowsColisReceptionAuPointRelais' => $totalRowsColisReceptionAuPointRelais,
                    'totalRowsColisReceptionLivreurPointRelai' => $totalRowsColisReceptionLivreurPointRelais,
                    'totalRowsColisLivre' => $totalRowsColisLivre,
                    'totalRowsColisRetourner' => $totalRowsColisRetourner,
                    'totalRowsBonsLivraison' => $totalRowsBonsLivraison,
                    'totalRowsBonsLivraisonSortie' => $totalRowsBonsLivraisonSortie,
                    'totalRowsBonsLivraisonRetourner' => $totalRowsBonsLivraisonRetourner,
                    'totalRowsEtatsColisLivrer' => $totalRowsEtatsColisLivrer,
                    'totalRowsEtatsColisLivrerNonRegler' => $totalRowsEtatsColisLivrerNonRegler,
                    'totalRowsEtatsColisLivrerRegler' => $totalRowsEtatsColisLivrerRegler,
                    'totalRowsDemandes' => $totalRowsDemandes,
                    'totalRowsDemandesEnCours' => $totalRowsDemandesEnCours,
                    'totalRowsDemandesRepondu' => $totalRowsDemandesRepondu,
                    'totalRowsDemandesCloturer' => $totalRowsDemandesCloturer
                )
            );
        }
    }
}
