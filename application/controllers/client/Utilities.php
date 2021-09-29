<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilities extends Client_controller
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * This is activities log client
     */
    public function activities_log()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblactivitylogcustomer.description',
                'tblactivitylogcustomer.date'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblactivitylogcustomer';

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblactivitylogcustomer.clientid'
            );

            $where = array();
            array_push($where, 'AND tblactivitylogcustomer.clientid = "' . get_expediteur_user_id() . '"');
            //Filtre
            if ($this->input->post('f-date-created-start') && is_date(to_sql_date($this->input->post('f-date-created-start')))) {
                array_push($where, ' AND tblactivitylogcustomer.date >= "' . to_sql_date($this->input->post('f-date-created-start')) . ' 00:00:00"');
            }
            if ($this->input->post('f-date-created-end') && is_date(to_sql_date($this->input->post('f-date-created-end')))) {
                array_push($where, ' AND tblactivitylogcustomer.date <= "' . to_sql_date($this->input->post('f-date-created-end')) . ' 23:59:59"');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array(), 'tblactivitylogcustomer.id', '', 'DESC');
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

                    if ($aColumns[$i] == 'tblactivitylogcustomer.date') {
                        $_data = _dt($_data);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('utility_activity_log');
        $this->load->view('client/utilities/activities_log', $data);
    }

    /**
     * This is activities log sms client
     */
    public function activities_log_sms()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblsmsactivitylog.code_barre',
                'tblsmsactivitylog.status_id',
                'tblsmsactivitylog.sms',
                'tblsmsactivitylog.sent',
                'tblsmsactivitylog.date'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblsmsactivitylog';

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.code_barre = tblsmsactivitylog.code_barre'
            );

            $where = array();
            array_push($where, 'AND tblcolis.id_expediteur = "' . get_expediteur_user_id() . '"');
            //Filtre
            if ($this->input->post('f-date-created-start') && is_date(to_sql_date($this->input->post('f-date-created-start')))) {
                array_push($where, ' AND tblsmsactivitylog.date >= "' . to_sql_date($this->input->post('f-date-created-start')) . ' 00:00:00"');
            }
            if ($this->input->post('f-date-created-end') && is_date(to_sql_date($this->input->post('f-date-created-end')))) {
                array_push($where, ' AND tblsmsactivitylog.date <= "' . to_sql_date($this->input->post('f-date-created-end')) . ' 23:59:59"');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array(), 'tblsmsactivitylog.id', '', 'DESC');
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

                    if ($aColumns[$i] == 'tblsmsactivitylog.code_barre') {
                        $_data = '<a href="' . client_url('colis/search/' . $_data) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblsmsactivitylog.status_id') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblsmsactivitylog.sent') {
                        if ($_data == 1) {
                            $label = _l('yes');
                            $colorLabel = 'success';
                        } else {
                            $label = _l('no');
                            $colorLabel = 'danger';
                        }
                        $_data = '<span class="label label-' . $colorLabel . '">' . $label . '</span>';
                    } else if ($aColumns[$i] == 'tblsmsactivitylog.date') {
                        $_data = _dt($_data);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('activities_log_sms');
        $this->load->view('client/utilities/activities_log_sms', $data);
    }
}
