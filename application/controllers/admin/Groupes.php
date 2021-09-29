<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groupes extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('groupes_model');
        
        if(get_permission_module('shipper') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all groups customers
     */
    public function clients()
    {
        if (!is_admin()) {
            access_denied('Groupes');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array('tblgroupes.name', 'tblgroupes.addedfrom', 'tblgroupes.datecreated');

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblgroupes';

            $join = array('left join tblstaff ON tblstaff.staffid = tblgroupes.addedfrom');
            $where = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblgroupes.id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_staff'));
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

                    if ($aColumns[$i] == 'tblgroupes.name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#groupe_modal" data-id="' . $aRow['id'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblgroupes.addedfrom') {
                        $staffId = $_data;
                        $_data = staff_profile_image($staffId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $staffId) . '" target="_blank">' . $aRow['name_staff'] . '</a>';
                    } else if ($aColumns[$i] == 'tblgroupes.datecreated') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#groupe_modal', 'data-id' => $aRow['id']));
                $row[] = $options .= icon_btn('admin/groupes/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get groups
        $this->load->model('groupes_model');
        $data['groupes'] = $this->groupes_model->get();
        // Get clients
        $this->load->model('expediteurs_model');
        $data['clients'] = $this->expediteurs_model->get();

        $data['title'] = _l('groupes');
        $this->load->view('admin/groupes/clients/manage', $data);
    }

    /**
     * Edit or add new group
     */
    public function groupe()
    {
        $success = false;
        $type = 'warning';
        $message = _l('access_denied');
        if ($this->input->is_ajax_request() && is_admin()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->groupes_model->add($data);
                $message = _l('problem_adding', _l('groupe'));
                if ($success) {
                    $type = 'success';
                    $message = _l('added_successfuly', _l('groupe'));
                }
            } else {
                $success = $this->groupes_model->update($data);
                $message = _l('problem_updating', _l('groupe'));
                if ($success) {
                    $type = 'success';
                    $message = _l('updated_successfuly', _l('groupe'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Assignment group to point relai
     */
    public function affectation()
    {
        $success = false;
        $message = _l('problem_assignment', _l('groupe'));
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if (is_array($data['clients']) && count($data['clients']) > 0) {
                $success = $this->groupes_model->affectation($data);
                if ($success) {
                    $message = _l('assignment_successfuly', _l('groupe'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'message' => $message));
    }

    /**
     * Delete group
     */
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Groupes');
        }
        if (!$id) {
            redirect(admin_url('groupes'));
        }

        $response = $this->groupes_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('groupe')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('groupe_lowercase')));
        }

        redirect(admin_url('groupes'));
    }
}
