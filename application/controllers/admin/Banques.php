<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banques extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('banques_model');
        
        if(get_permission_module('banques') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all banques
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('Banques');
        }
        
        if ($this->input->is_ajax_request()) {
            $aColumns = array('tblbanques.name', 'tblbanques.addedfrom', 'tblbanques.datecreated');

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblbanques';

            $join = array('left join tblstaff ON tblstaff.staffid = tblbanques.addedfrom');
            $where = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblbanques.id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_staff'));
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

                    if ($aColumns[$i] == 'tblbanques.name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#banque_modal" data-id="' . $aRow['id'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblbanques.addedfrom') {
                        $staffId = $_data;
                        $_data = staff_profile_image($staffId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $staffId) . '" target="_blank">' . $aRow['name_staff'] . '</a>';
                    } else if ($aColumns[$i] == 'tblbanques.datecreated') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#banque_modal', 'data-id' => $aRow['id']));
                $row[] = $options .= icon_btn('admin/banques/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        
        $data['title'] = _l('banques');
        $this->load->view('admin/banques/manage', $data);
    }

    /**
     * Edit or add new banque
     */
    public function banque()
    {
        $success = false;
        $type = 'warning';
        $message = _l('access_denied');
        if ($this->input->is_ajax_request() && is_admin()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->banques_model->add($data);
                $message = _l('problem_adding', _l('banque'));
                if ($success) {
                    $type = 'success';
                    $message = _l('added_successfuly', _l('banque'));
                }
            } else {
                $success = $this->banques_model->update($data);
                $message = _l('problem_updating', _l('banque'));
                if ($success) {
                    $type = 'success';
                    $message = _l('updated_successfuly', _l('banque'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Delete banque
     */
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Banques');
        }
        if (!$id) {
            redirect(admin_url('banques'));
        }

        $response = $this->banques_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('banque')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('banque_lowercase')));
        }

        redirect(admin_url('banques'));
    }
}
