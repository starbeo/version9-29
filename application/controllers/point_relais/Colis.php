<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('colis_model');
    }

    /**
     * List all colis
     */
    public function index($status = false, $search = false)
    {
        //Get points relais staff
        $pointsRelaisStaff = get_staff_points_relais();
        if ($this->input->is_ajax_request()) {
            $aColumns = array('tblcolis.id', 'tblcolis.code_barre', 'tblpointsrelais.nom', 'tblcolis.num_commande', 'tblexpediteurs.nom', 'tblcolis.telephone', 'tblvilles.name', 'tblcolis.crbt', 'DATE_FORMAT(date_ramassage, "%d/%m/%Y")');

            $orderby = '';
            if ($this->input->post('custom_view')) {
                $view = $this->input->post('custom_view');
                if ($view == 11) {
                    array_push($aColumns, '(SELECT DATE_FORMAT(date_reporte, "%d/%m/%Y") FROM tblstatus WHERE tblstatus.code_barre = tblcolis.code_barre ORDER BY tblstatus.id DESC LIMIT 1) as date_reporte');
                    $orderby = 'ORDER BY date_reporte DESC';
                } else {
                    array_push($aColumns, 'date_livraison');
                }
            } else {
                array_push($aColumns, 'date_livraison');
            }

            array_push($aColumns, 'status_reel', 'etat_id');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';

            $join = array(
                'LEFT JOIN tblpointsrelais ON tblpointsrelais.id = tblcolis.point_relai_id',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur'
            );

            $where = array(' AND tblcolis.point_relai_id IN ' . $pointsRelaisStaff);
            // Filtre
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                array_push($where, ' AND tblcolis.point_relai_id = ' . $this->input->post('f-point-relai'));
            }
            if ($this->input->post('f-clients') && is_numeric($this->input->post('f-clients'))) {
                array_push($where, ' AND tblcolis.id_expediteur = ' . $this->input->post('f-clients'));
            }
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                array_push($where, ' AND (tblcolis.status_id = ' . $this->input->post('f-statut') . ' OR tblcolis.status_reel = ' . $this->input->post('f-statut') . ')');
            }
            if ($this->input->post('f-etat') && is_numeric($this->input->post('f-etat'))) {
                array_push($where, ' AND tblcolis.etat_id = ' . $this->input->post('f-etat'));
            }
            if ($this->input->post('f-ville') && is_numeric($this->input->post('f-ville'))) {
                array_push($where, ' AND tblcolis.ville = ' . $this->input->post('f-ville'));
            }
            if ($this->input->post('f-date-ramassage-start') && is_date(to_sql_date($this->input->post('f-date-ramassage-start')))) {
                array_push($where, ' AND tblcolis.date_ramassage >= "' . to_sql_date($this->input->post('f-date-ramassage-start')) . '"');
            }
            if ($this->input->post('f-date-ramassage-end') && is_date(to_sql_date($this->input->post('f-date-ramassage-end')))) {
                array_push($where, ' AND tblcolis.date_ramassage <= "' . to_sql_date($this->input->post('f-date-ramassage-end')) . '"');
            }
            if ($this->input->post('f-date-livraison-start') && is_date(to_sql_date($this->input->post('f-date-livraison-start')))) {
                array_push($where, ' AND tblcolis.date_livraison >= "' . to_sql_date($this->input->post('f-date-livraison-start')) . '"');
            }
            if ($this->input->post('f-date-livraison-end') && is_date(to_sql_date($this->input->post('f-date-livraison-end')))) {
                array_push($where, ' AND tblcolis.date_livraison <= "' . to_sql_date($this->input->post('f-date-livraison-end')) . '"');
            }

            if ($this->input->post('custom_view') && is_numeric($this->input->post('custom_view'))) {
                $view = $this->input->post('custom_view');
                $_where = '';
                if (is_numeric($view)) {
                    $_where .= ' AND tblcolis.status_reel = ' . $view;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            } else {
                if (is_numeric($status)) {
                    array_push($where, ' AND tblcolis.status_reel = ' . $status);
                }
            }

            // Search
            if ($search && !empty($search)) {
                array_push($where, ' AND tblcolis.code_barre = "' . $search . '"');
            }
            // Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        if ($status && $status == 2) {
                            array_push($where, ' AND tblcolis.date_livraison = "' . date('Y-m-d') . '"');
                        } else if ($status && $status == 3) {
                            array_push($where, ' AND tblcolis.date_retour = "' . date('Y-m-d') . '"');
                        } else {
                            array_push($where, ' AND (tblcolis.date_ramassage = "' . date('Y-m-d') . '" OR tblcolis.date_livraison = "' . date('Y-m-d') . '" OR tblcolis.date_retour = "' . date('Y-m-d') . '")');
                        }
                        break;
                    case 'week':
                        if ($status && $status == 2) {
                            array_push($where, ' AND WEEK(tblcolis.date_livraison, 1) = WEEK(CURDATE(), 1)');
                        } else if ($status && $status == 3) {
                            array_push($where, ' AND WEEK(tblcolis.date_retour, 1) = WEEK(CURDATE(), 1)');
                        } else {
                            array_push($where, ' AND (WEEK(tblcolis.date_ramassage, 1) = WEEK(CURDATE(), 1) OR WEEK(tblcolis.date_livraison, 1) = WEEK(CURDATE(), 1) OR WEEK(tblcolis.date_retour, 1) = WEEK(CURDATE(), 1))');
                        }
                        break;
                    case 'month':
                        if ($status && $status == 2) {
                            array_push($where, ' AND tblcolis.date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        } else if ($status && $status == 3) {
                            array_push($where, ' AND tblcolis.date_retour > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        } else {
                            array_push($where, ' AND (tblcolis.date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH) OR tblcolis.date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH) OR tblcolis.date_retour > DATE_SUB(now(), INTERVAL 1 MONTH))');
                        }
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, array('tblcolis.anc_crbt', 'tblcolis.date_ramassage', 'tblcolis.date_retour', 'tblcolis.id_expediteur', 'tblcolis.num_bonlivraison', 'tblcolis.num_etatcolislivrer', 'tblcolis.id_demande'), $orderby);
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcolis.code_barre') {
                        $checked = false;
                        if (!is_null($aRow['num_etatcolislivrer'])) {
                            $checked = true;
                        }
                        if ($checked == false) {
                            $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<a id="column-barcode-' . $key . '" href="#" data-toggle="modal" data-target="#colis" data-id="' . $aRow['tblcolis.id'] . '" data-expediteurid="' . $aRow['id_expediteur'] . '" >' . $_data . '</a>';
                        } else {
                            $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<b id="column-barcode-' . $key . '">' . $_data . '</b>';
                        }
                    } else if ($aColumns[$i] == 'tblpointsrelais.nom') {
                        $_data = render_icon_university() . '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<b>' . ucwords($_data) . '</b>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'date_livraison' || $aColumns[$i] == 'date_retour') {
                        if ($aRow['status_reel'] == 2) {
                            $_data = $aRow['date_livraison'];
                        } else if ($aRow['status_reel'] == 3) {
                            $_data = $aRow['date_retour'];
                        } else {
                            $_data = NULL;
                        }
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcolis.crbt') {
                        if ($aRow['anc_crbt'] > 0) {
                            $_data = $aRow['anc_crbt'];
                        }
                        $_data = '<p class="pright30" style="text-align: right;">' . format_money($_data) . '</p>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                $checked = false;
                if (!is_null($aRow['num_bonlivraison'])) {
                    $options .= '<a href="' . point_relais_url('bons_livraison/bon/' . $aRow['num_bonlivraison']) . '" class="btn btn-default btn-icon mbot5" target="_blank">BL-' . $aRow['num_bonlivraison'] . '</a>';
                }
                if (!is_null($aRow['num_etatcolislivrer'])) {
                    $checked = true;
                    $options .= '<a href="' . point_relais_url('etats_colis_livrer/etat/' . $aRow['num_etatcolislivrer']) . '" class="btn btn-info btn-icon mbot5" target="_blank">ECL-' . $aRow['num_etatcolislivrer'] . '</a>';
                }
                if (!is_null($aRow['id_demande'])) {
                    $options .= '<a href="' . point_relais_url('demandes/preview/' . $aRow['id_demande']) . '" class="btn btn-primary btn-icon mbot5" target="_blank">DMD-' . $aRow['id_demande'] . '</a>';
                }
                if ($checked == false) {
                    $options .= icon_btn('#', 'pencil-square-o', 'btn-default mbot5', array('data-toggle' => 'modal', 'data-target' => '#colis', 'data-id' => $aRow['tblcolis.id'], 'data-expediteurid' => $aRow['id_expediteur']));
                }
                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get clients
        $this->load->model('expediteurs_model');
        $data['expediteurs'] = $this->expediteurs_model->get();
        //Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();
        //Get statuses
        $this->load->model('statuses_colis_model');
        $data['statuses'] = $this->statuses_colis_model->get();
        //Get etats
        $this->load->model('etats_colis_model');
        $data['etats'] = $this->etats_colis_model->get();
        // Get points relais
        $this->load->model('points_relais_model');
        $data['points_relais'] = $this->points_relais_model->get('', 1, 'tblpointsrelais.id IN ' . $pointsRelaisStaff, 'tblpointsrelais.id, CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as nom');
        // Get points relais staff
        $data['points_relais_staff'] = $pointsRelaisStaff;

        $data['title'] = _l('als_colis');
        $this->load->view('point-relais/colis/manage', $data);
    }

    /**
     * List all colis with search
     */
    public function search($search)
    {
        if(!empty($search)) {
            $this->index(false, $search);
        } else {
            $this->index();
        }
    }

    /**
     * List all colis in progress
     */
    public function en_cours()
    {
        $this->index(100);
    }

    /**
     * List all colis received
     */
    public function recu()
    {
        $this->index(101);
    }

    /**
     * List all colis received by the delivery man
     */
    public function recu_par_livreur()
    {
        $this->index(102);
    }

    /**
     * List all colis delivred
     */
    public function livrer()
    {
        $this->index(2);
    }

    /**
     * List all colis returned
     */
    public function retourner()
    {
        $this->index(3);
    }

    /**
     * Get info coli
     */
    function get_info_colis($id)
    {
        $coli = $this->colis_model->get($id);
        //Get array points relais staff
        $pointsRelaisStaffArray = get_staff_points_relais(true);
        if (!$coli || is_null($coli->point_relai_id) || !in_array($coli->point_relai_id, $pointsRelaisStaffArray)) {
            $coli = null;
        }

        echo json_encode($coli);
    }

    /**
     * Edit or add new coli
     */
    public function coli($id = '')
    {
        $success = false;
        $type = 'warning';
        $message = '';
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == "") {
                $data['type_livraison'] = 'point_relai';
                $id = $this->colis_model->add($data);
                $message = _l('problem_adding', _l('colis'));
                if (is_numeric($id)) {
                    $success = true;
                    $type = 'success';
                    $message = _l('added_successfuly', _l('colis'));
                }
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->colis_model->update($data, $id);
                $message = _l('problem_updating', _l('colis'));
                if ($success) {
                    $type = 'success';
                    $message = _l('updated_successfuly', _l('colis'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Check if numero commande already exists for this colis
     */
    public function check_num_commande_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the refernce is the same
                $colisId = $this->input->post('colis_id');
                if ($colisId != 'undefined') {
                    $this->db->where('id', $colisId);
                    $currentColisEnAttente = $this->db->get('tblcolisenattente')->row();
                    if ($currentColisEnAttente && $currentColisEnAttente->num_commande == $this->input->post('num_commande')) {
                        echo json_encode(true);
                        die();
                    }
                }

                $numCommandeExistsInTableColis = total_rows('tblcolis', array('num_commande' => $this->input->post('num_commande')));
                $numCommandeExistsInTableColisEnAttente = total_rows('tblcolisenattente', array('num_commande' => $this->input->post('num_commande')));
                if ($numCommandeExistsInTableColisEnAttente > 0 || $numCommandeExistsInTableColis > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * Check if telephone has +212
     */
    public function check_telephone()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $telephone = $this->input->post('telephone');
                if (strlen($telephone) == 10) {
                    if (!preg_match("/^[0-9]{10}$/", $telephone)) {
                        echo json_encode(false);
                    } else {
                        echo json_encode(true);
                    }
                } else {
                    echo json_encode(false);
                }
                die();
            }
        }
    }
}
