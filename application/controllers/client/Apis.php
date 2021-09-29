<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apis extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('apis_model');
    }

    /**
     * List all apis
     */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblapis.title', 'tblapis.url', 'tblapis.description');
            $sIndexColumn = "id";
            $sTable = 'tblapis';
            $join = array();
            $where = array('AND tblapis.active = 1');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where);
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

                    if ($aColumns[$i] == 'tblapis.title') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblapis.url') {
                        $_data = '<b>' . base_url() . $_data . '</b>';
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get packs
        $this->load->model('apis_model');
        $data['packs'] = $this->apis_model->get_packs();
        // Check if client has pack
        $this->load->model('apis_model');
        $data['access'] = $this->apis_model->get_last_access_by_client_id(get_expediteur_user_id());

        $data['title'] = _l('api');
        $this->load->view('client/apis/manage', $data);
    }

    /**
     * List all access
     */
    public function access()
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblapipacks.name', 'tblapipacks.nbr_limit', 'tblapiaccess.status', 'tblapiaccess.token', 'tblapiaccess.date_created', 'tblapiaccess.date_start', 'tblapiaccess.date_end', 'tblapiaccess.nbr_appels');
            $sIndexColumn = "id";
            $sTable = 'tblapiaccess';
            $join = array('LEFT JOIN tblapipacks ON tblapipacks.id = tblapiaccess.pack_id');
            $where = array('AND tblapiaccess.client_id = ' . get_expediteur_user_id());

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where);
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

                    if ($aColumns[$i] == 'tblapipacks.name') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblapipacks.nbr_limit') {
                        $_data = '<p class="text-center"><span class="label label-success inline-block">' . $_data . ' ' . _l('requests_uppercase') . '</span></p>';
                    } else if ($aColumns[$i] == 'tblapiaccess.status') {
                        $_data = format_status_access_apis($_data);
                    } else if ($aColumns[$i] == 'tblapiaccess.token') {
                        if (is_null($_data)) {
                            $_data = '<p class="text-center"><span class="label label-default inline-block">' . _l('creation_of_the_access_key_is_in_progress') . '</span></p>';
                        }
                    } else if ($aColumns[$i] == 'tblapiaccess.date_created' || $aColumns[$i] == 'tblapiaccess.date_start' || $aColumns[$i] == 'tblapiaccess.date_end') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_time_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblapiaccess.nbr_appels') {
                        $_data = '<p class="text-center"><span class="label label-default inline-block">' . $_data . ' ' . _l('requests_uppercase') . '</span></p>';
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
     * Add access api
     */
    public function add_access($packId = false)
    {
        if (!is_numeric($packId)) {
            redirect(client_url('apis'));
        }
        
        //Add request api
        $success = $this->apis_model->add_access($packId);
        if(is_array($success) && $success['access_already_exist'] == true) {
            set_alert('warning', _l('problem_adding', _l('you_already_have_access')));
        } else if($success == true) {
            set_alert('success', _l('added_successfuly', _l('access')));
        } else {
            set_alert('warning', _l('problem_adding', _l('access')));
        }
        
        redirect(client_url('apis'));
    }

    /**
     * Generate access api
     */
    public function generate_access($clientId = false, $accessId = false)
    {
        if (!is_numeric($clientId) || !is_numeric($accessId)) {
            redirect(client_url('apis'));
        }
        
        //Add request api
        $success = $this->apis_model->generate_access($clientId, $accessId);
        if(is_array($success) && $success['access_already_exist'] == true) {
            set_alert('warning', _l('access_already_exists'));
        } else if(is_array($success) && $success['access_does_not_exists'] == true) {
            set_alert('warning', _l('access_does_not_exists'));
        } else if($success == true) {
            set_alert('success', _l('added_successfuly', _l('access')));
        } else {
            set_alert('warning', _l('problem_adding', _l('access')));
        }
        
        redirect(client_url('apis'));
    }
}
