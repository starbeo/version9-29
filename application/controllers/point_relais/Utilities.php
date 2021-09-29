<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilities extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();

        if (get_permission_module('activities_log') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * All activities log
     */
    public function activities_log()
    {
        $_custom_view = '';
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblactivitylog.id',
                'tblactivitylog.description',
                'tblactivitylog.date',
                'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_staff'
            );

            $sIndexColumn = "id";
            $sTable = 'tblactivitylog';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblactivitylog.staffid'
            );

            $where = array('AND tblactivitylog.staffid = ' . get_staff_user_id());
            //Filtre
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblactivitylog.date LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }

            $additionalSelect = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, $additionalSelect);
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

                    if ($aColumns[$i] == 'tblactivitylog.date') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'fullname_staff') {
                        $_data = '<b>' . $_data . '</b>';
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('utility_activity_log');
        $this->load->view('point-relais/utilities/activities-log', $data);
    }
}
