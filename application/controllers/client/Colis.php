<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('colis_model');
    }

    /**
     * List all colis
     */
    public function index($status = false, $barcode = '')
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblcolis.code_barre', 'tblcolis.num_commande');
            // Check if option show point relai is actived
            if (get_permission_module('points_relais') == 1) {
                array_push($aColumns, 'tblcolis.type_livraison');
            }
            array_push($aColumns, 'tblcolis.nom_complet', 'tblcolis.telephone', 'tblcolis.crbt', 'tblvilles.name', 'tblcolis.date_ramassage', 'tblcolis.date_livraison', 'tblcolis.etat_id', 'tblcolis.status_reel');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';

            $join = array(
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville'
            );

            $where = array('AND tblcolis.id_expediteur = ' . get_expediteur_user_id());
            // Filtre
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                array_push($where, ' AND tblcolis.type_livraison = "' . $this->input->post('f-type-livraison') . '"');
            }
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                array_push($where, ' AND tblcolis.point_relai_id = ' . $this->input->post('f-point-relai'));
            }
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                array_push($where, ' AND (tblcolis.status_id = ' . $this->input->post('f-statut') . ' OR tblcolis.status_reel = ' . $this->input->post('f-statut'). ')');
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
            // Get
            if (is_numeric($status)) {
                array_push($where, ' AND (tblcolis.status_id = ' . $status . ' OR tblcolis.status_reel = ' . $status . ')');
            }
            if (!empty($barcode)) {
                array_push($where, 'AND tblcolis.code_barre = "' . $barcode . '"');
            }

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
                    default :
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.id', 'tblcolis.anc_crbt', 'tblcolis.date_retour'), 'tblcolis.id', '', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcolis.code_barre') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#historiques" data-coli-id="' . $aRow['id'] . '" data-barcode="' . $aRow['tblcolis.code_barre'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblcolis.type_livraison') {
                        $_data = format_type_livraison_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolis.etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolis.status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolis.date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcolis.date_livraison' || $aColumns[$i] == 'date_retour') {
                        if ($aRow['tblcolis.status_reel'] == 2) {
                            $_data = $aRow['tblcolis.date_livraison'];
                        } else if ($aRow['tblcolis.status_reel'] == 3) {
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
                $options .= icon_btn('#', 'eye', 'btn-primary', array('data-toggle' => 'modal', 'data-target' => '#historiques', 'data-coli-id' => $aRow['id'], 'data-barcode' => $aRow['tblcolis.code_barre']));
                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get period
        if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
            $data['periode'] = $this->input->get('periode');
        } else {
            $data['periode'] = 'all';
        }
        $data['wherePeriode1'] = '';
        $data['wherePeriode2'] = '';
        $data['wherePeriode3'] = '';
        switch ($data['periode']) {
            case 'day':
                $data['wherePeriode1'] = ' AND tblcolis.date_ramassage = "' . date('Y-m-d') . '"';
                $data['wherePeriode2'] = ' AND tblcolis.date_livraison = "' . date('Y-m-d') . '"';
                $data['wherePeriode3'] = ' AND tblcolis.date_retour = "' . date('Y-m-d') . '"';
                break;
            case 'week':
                $data['wherePeriode1'] = ' AND WEEK(tblcolis.date_ramassage, 1) = WEEK(CURDATE(), 1)';
                $data['wherePeriode2'] = ' AND WEEK(tblcolis.date_livraison, 1) = WEEK(CURDATE(), 1)';
                $data['wherePeriode3'] = ' AND WEEK(tblcolis.date_retour, 1) = WEEK(CURDATE(), 1)';
                break;
            case 'month':
                $data['wherePeriode1'] = ' AND tblcolis.date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH)';
                $data['wherePeriode2'] = ' AND tblcolis.date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH)';
                $data['wherePeriode3'] = ' AND tblcolis.date_retour > DATE_SUB(now(), INTERVAL 1 MONTH)';
                break;
            default :
                break;
        }

        // Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get('', 1);
        // Get statuses
        $this->load->model('statuses_colis_model');
        $data['statuses'] = $this->statuses_colis_model->get();
        // Get states
        $this->load->model('etats_colis_model');
        $data['etats'] = $this->etats_colis_model->get();
        // Get periodes
        $data['periodes'] = array(
            array('value' => 'all', 'name' => _l('all')),
            array('value' => 'day', 'name' => _l('per_day')),
            array('value' => 'week', 'name' => _l('per_week')),
            array('value' => 'month', 'name' => _l('per_month'))
        );
        //Hide Statuses in statistique
        $data['statuses_hide'] = array(5, 10, 12, 14, 15, 100, 101, 102);
        $data['statuses_sum'] = array(1, 2, 3);
        //Get types livraison
        $data['types_livraison'] = $this->colis_model->get_types_livraison();
        // Check if option show point relai is actived
        $data['points_relais'] = array();
        if (get_permission_module('points_relais') == 1) {
            // Get points relais
            $this->load->model('points_relais_model');
            $data['points_relais'] = $this->points_relais_model->get('', 1, array(), 'tblpointsrelais.id, CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as nom');
        }

        $data['title'] = _l('als_colis');
        $this->load->view('client/colis/manage', $data);
    }

    /**
     * Colis livrer
     */
    public function livrer()
    {
        $this->index(2);
    }

    /**
     * Colis Retourner
     */
    public function retourner()
    {
        $this->index(3);
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
     * List all status colis
     */
    public function status()
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblstatus.code_barre',
                'tblstatus.type',
                'tbllocations.name',
                'tblstatus.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblstatus';

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.code_barre = tblstatus.code_barre',
                'LEFT JOIN tbllocations ON tbllocations.id = tblstatus.emplacement_id'
            );

            $where = array('AND tblcolis.id_expediteur = ' . get_expediteur_user_id());
            //Affichage historiques status coli
            if ($this->input->post('f-code-barre') && !empty($this->input->post('f-code-barre'))) {
                array_push($where, ' AND tblstatus.code_barre = "' . $this->input->post('f-code-barre') . '"');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array(), 'tblstatus.id', '', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblstatus.code_barre') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblstatus.type') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblstatus.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * Export All Colis expediteur
     */
    public function export()
    {
        if ($this->input->post()) {
            // Filtre
            $where = ' tblcolis.id_expediteur = ' . get_expediteur_user_id();
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                $where .= ' AND tblcolis.type_livraison = "' . $this->input->post('f-type-livraison') . '"';
            }
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                $where .= ' AND tblcolis.point_relai_id = ' . $this->input->post('f-point-relai');
            }
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                $where .= ' AND (tblcolis.status_id = ' . $this->input->post('f-statut') . ' OR tblcolis.status_reel = ' . $this->input->post('f-statut') . ')';
            }
            if ($this->input->post('f-etat') && is_numeric($this->input->post('f-etat'))) {
                $where .= ' AND tblcolis.etat_id = ' . $this->input->post('f-etat');
            }
            if ($this->input->post('f-ville') && is_numeric($this->input->post('f-ville'))) {
                $where .= ' AND tblcolis.ville = ' . $this->input->post('f-ville');
            }
            if ($this->input->post('f-date-ramassage-start') && is_date(to_sql_date($this->input->post('f-date-ramassage-start')))) {
                $where .= ' AND tblcolis.date_ramassage >= "' . to_sql_date($this->input->post('f-date-ramassage-start')) . '"';
            }
            if ($this->input->post('f-date-ramassage-end') && is_date(to_sql_date($this->input->post('f-date-ramassage-end')))) {
                $where .= ' AND tblcolis.date_ramassage <= "' . to_sql_date($this->input->post('f-date-ramassage-end')) . '"';
            }
            if ($this->input->post('f-date-livraison-start') && is_date(to_sql_date($this->input->post('f-date-livraison-start')))) {
                $where .= ' AND tblcolis.date_livraison >= "' . to_sql_date($this->input->post('f-date-livraison-end')) . '"';
            }
            if ($this->input->post('f-date-livraison-end') && is_date(to_sql_date($this->input->post('f-date-livraison-end')))) {
                $where .= ' AND tblcolis.date_livraison <= "' . to_sql_date($this->input->post('f-date-livraison-end')) . '"';
            }

            //Get list colis
            $colis = $this->colis_model->get_colis_expediteur_export($where);
            if (count($colis) > 0) {
                $fileName = 'Liste colis ' . date("d-m-Y") . '.xls';
                colis_client_excel($fileName, $colis);
                set_alert('success', _l('export_successfuly', _l('colis')));
            } else {
                set_alert('warning', _l('no_colis'));
            }
        }

        redirect(client_url('colis'));
    }

    /**
     * Total colis par defaut / ajax chart
     */
    public function default_total_colis()
    {
        echo json_encode($this->colis_model->default_total_colis());
    }
}
