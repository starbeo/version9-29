<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        
        if(get_permission_module('colis') == 0) {
            redirect(admin_url('home'));
        }
        
        if (!is_admin()) {
            access_denied('Locations');
        }
        $this->load->model('locations_model');
    }
    /* List all Locations */

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array('name');

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tbllocations';

            $join = array();
            $where = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#location_modal', 'data-id' => $aRow['id']));
                $row[] = $options .= icon_btn('admin/locations/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('location');
        $this->load->view('admin/locations/manage', $data);
    }

    public function location()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->locations_model->add($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfuly', _l('location'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            } else {
                $success = $this->locations_model->update($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfuly', _l('location'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            }
        }
    }

    public function delete($id)
    {
        if (!is_numeric($id)) {
            redirect(admin_url('locations'));
        }

        $response = $this->locations_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('location_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('location')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('location_lowercase')));
        }

        redirect(admin_url('locations'));
    }
}
