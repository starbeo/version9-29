<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_cost extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        
        if(get_permission_module('shipping_cost') == 0) {
            redirect(admin_url('home'));
        }
        
        if (!is_admin()) {
            access_denied('shipping_cost');
        }
        $this->load->model('shipping_cost_model');
    }
    /*
     * List all shipping cost
     */

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array('tblshippingcost.name', 'tblshippingcost.shipping_cost', 'tblshippingcost.datecreated', 'tblstaff.firstname');

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblshippingcost';

            $join = array('left join tblstaff ON tblstaff.staffid = tblshippingcost.addedfrom');
            $where = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('id', 'tblstaff.staffid', 'tblstaff.lastname'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'tblshippingcost.name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#shipping_cost_modal" data-id="' . $aRow['id'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblshippingcost.datecreated') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblstaff.firstname') {
                        $_data = staff_profile_image($aRow['staffid'], array('staff-profile-image-small'));
                        $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $aRow['tblstaff.firstname'] . ' ' . $aRow['lastname'] . '</a>';
                    }

                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#shipping_cost_modal', 'data-id' => $aRow['id']));
                $row[] = $options .= icon_btn('admin/shipping_cost/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('shipping_cost');
        $this->load->view('admin/shipping_cost/manage', $data);
    }
    /*
     * Add or edit shipping cost
     */

    public function shipping_cost()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->shipping_cost_model->add($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfuly', _l('shipping_cost'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            } else {
                $success = $this->shipping_cost_model->update($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfuly', _l('shipping_cost'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            }
        }
    }
    /*
     * Delete shipping cost
     */

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('shipping_cost'));
        }

        $response = $this->shipping_cost_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('shipping_cost_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('shipping_cost')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('shipping_cost_lowercase')));
        }

        redirect(admin_url('shipping_cost'));
    }
}
