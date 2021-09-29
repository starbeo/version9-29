<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appels extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('appels_model');
    }

    /**
     * List all calls
     */
    public function livreurs()
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblappelslivreur.livreur_id',
                'tblcolis.code_barre',
                'tblappelslivreur.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblappelslivreur';

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tblappelslivreur.colis_id',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblappelslivreur.livreur_id'
            );

            $where = array('AND tblappelslivreur.client_id = ' . get_expediteur_user_id());

            //Filtre
            if ($this->input->post('f-date-created-start') && is_date(to_sql_date($this->input->post('f-date-created-start')))) {
                array_push($where, ' AND tblappelslivreur.date_created >= "' . to_sql_date($this->input->post('f-date-created-start')) . ' 00:00:00"');
            }
            if ($this->input->post('f-date-created-end') && is_date(to_sql_date($this->input->post('f-date-created-end')))) {
                array_push($where, ' AND tblappelslivreur.date_created <= "' . to_sql_date($this->input->post('f-date-created-end')) . ' 23:59:59"');
            }
            //Affichage historiques appels coli
            if ($this->input->post('f-coli-id') && !empty($this->input->post('f-coli-id'))) {
                array_push($where, ' AND tblappelslivreur.colis_id = "' . $this->input->post('f-coli-id') . '"');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array("CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as livreur_name"));
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

                    switch ($aColumns[$i]) {
                        case 'tblappelslivreur.livreur_id' :
                            $_data = '<b>' . $aRow['livreur_name'] . '</b>';
                            break;
                        case 'tblcolis.code_barre' :
                            $_data = '<b>' . $_data . '</b>';
                            break;
                        case 'tblappelslivreur.date_created' :
                            if (!is_null($_data)) {
                                $_data = date(get_current_date_time_format(), strtotime($_data));
                            } else {
                                $_data = '';
                            }
                            break;
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('appels_livreurs');
        $this->load->view('client/appels/manage', $data);
    }
}
