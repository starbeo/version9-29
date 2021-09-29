<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
// set max execution time 2 hours / mostly used for exporting PDF
ini_set('max_execution_time', 3600);

class Utilities extends Admin_controller
{

    function __construct()
    {
        parent::__construct();

        if (get_permission_module('activities_log') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * All number of authentication
     */
    public function connected_customer()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblnumberofauthentication.id',
                'nom',
                'tblnumberofauthentication.date_created',
                'address_ip'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblnumberofauthentication';

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblnumberofauthentication.clientid'
            );

            $where = array('AND tblnumberofauthentication.date_created LIKE "' . date('Y-m-d') . '%"');

            $additionalSelect = array('clientid');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, $additionalSelect, '', 'GROUP BY clientid', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['clientid']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'tblnumberofauthentication.date_created') {
                        $_data = date('d/m/Y h:i:s', strtotime($_data));
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('connected_customer');
        $this->load->view('admin/utilities/connected_customer', $data);
    }

    /**
     * All activity log
     */
    public function activity_log()
    {
        $_custom_view = '';
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'id',
                'description',
                'date',
                'tblactivitylog.staffid'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblactivitylog';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblactivitylog.staffid',
                'LEFT JOIN tblentreprise ON tblentreprise.id_entreprise = tblactivitylog.id_entreprise'
            );

            $where = array();
            if (!is_admin()) {
                array_push($where, 'AND tblactivitylog.staffid = "' . get_staff_user_id() . '"');
            }

            if ($this->input->post('custom_view')) {
                $_custom_view = $this->input->post('custom_view');
                $view = $this->input->post('custom_view');
                $_where = '';
                if (!empty($view)) {
                    $_where = 'AND tblactivitylog.staffid = ' . $view;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            $additionalSelect = array(
                'firstname',
                'lastname'
            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, $additionalSelect);
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'date') {
                        $_data = _dt($_data);
                    } else if ($aColumns[$i] == 'tblactivitylog.staffid') {
                        $_data = staff_profile_image($aRow['tblactivitylog.staffid'], array(
                            'staff-profile-image-small'
                        ));
                        $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['tblactivitylog.staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get All Livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();

        $data['custom_view'] = $_custom_view;
        $data['title'] = _l('utility_activity_log');
        $this->load->view('admin/utilities/activity_log', $data);
    }

    /**
     * All activity log customer
     */
    public function activity_log_customer()
    {
        $_custom_view = '';
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblactivitylogcustomer.id',
                'description',
                'date',
                'tblactivitylogcustomer.clientid'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblactivitylogcustomer';

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblactivitylogcustomer.clientid',
                'LEFT JOIN tblentreprise ON tblentreprise.id_entreprise = tblactivitylogcustomer.id_entreprise'
            );

            $where = array();
            if ($this->input->post('custom_view')) {
                $_custom_view = $this->input->post('custom_view');
                $view = $this->input->post('custom_view');
                $_where = '';
                if (!empty($view)) {
                    $_where = 'AND tblactivitylogcustomer.clientid = ' . $view;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            $additionalSelect = array(
                'nom'
            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, $additionalSelect);
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'date') {
                        $_data = _dt($_data);
                    } else if ($aColumns[$i] == 'tblactivitylogcustomer.clientid') {
                        $_data = client_logo($aRow['tblactivitylogcustomer.clientid'], array(
                            'staff-profile-image-small'
                        ));
                        $_data .= ' <a href="' . admin_url('expediteurs/expediteur/' . $aRow['tblactivitylogcustomer.clientid']) . '">' . $aRow['nom'] . '</a>';
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get All Livreurs
        $this->load->model('expediteurs_model');
        $data['clients'] = $this->expediteurs_model->get();

        $data['custom_view'] = $_custom_view;
        $data['title'] = _l('utility_activity_log');
        $this->load->view('admin/utilities/activity_log_customer', $data);
    }

    /**
     * This is activities log sms
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

            $join = array();
            $where = array();

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
                        $_data = '<a href="' . admin_url('colis/search/' . $_data) . '">' . $_data . '</a>';
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
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_time_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('activities_log_sms');
        $this->load->view('admin/utilities/activity_log_sms', $data);
    }
}
