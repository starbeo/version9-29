<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('sms_model');
        
        if(get_permission_module('sms') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all sms
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('sms');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array('title', 'tblsmstemplates.status_id', 'automatic_sending', 'tblsmstemplates.active', 'tblsmstemplates.datecreated', 'tblsmstemplates.addedfrom');

            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblsmstemplates';

            $join = array(
                'LEFT JOIN tblstatuscolis ON tblstatuscolis.id = tblsmstemplates.status_id',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblsmstemplates.addedfrom'
            );

            $where = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblsmstemplates.id', 'tblstatuscolis.color as status_color', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur'));
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

                    if ($aColumns[$i] == 'title') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#sms_modal" data-id="' . $aRow['id'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblsmstemplates.status_id') {
                        $_data = format_status_colis($aRow['tblsmstemplates.status_id'], $aRow['status_color']);
                    } else if ($aColumns[$i] == 'automatic_sending') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblsmstemplates.active') {
                        $checked = '';
                        if ($aRow['tblsmstemplates.active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['id'] . '" data-switch-url="admin/sms/change_sms_status" ' . $checked . '>';
                        // For exporting
                        $_data .= '<span class="hide" title="' . _l('change_sms_status') . '">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                    } else if ($aColumns[$i] == 'tblsmstemplates.datecreated') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblsmstemplates.addedfrom') {
                        $utilisateurId = $_data;
                        $_data = staff_profile_image($utilisateurId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $utilisateurId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'eye', 'btn-primary', array('data-toggle' => 'modal', 'data-target' => '#sms_modal', 'data-id' => $aRow['id'], 'data-action' => 'view'));
                $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#sms_modal', 'data-id' => $aRow['id'], 'data-action' => 'edit'));
                $options .= icon_btn('admin/sms/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');
                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get Statuses
        $this->load->model('statuses_colis_model');
        $data['statuses'] = $this->statuses_colis_model->get();
        //Get Available merge fields
        $this->load->model('emails_model');
        $data['available_merge_fields'] = $this->emails_model->get_available_merge_fields();

        $data['title'] = _l('sms');
        $this->load->view('admin/sms/manage', $data);
    }

    /**
     * Get infos sms
     */
    function get_infos_sms($id)
    {
        echo json_encode($this->sms_model->get($id));
    }

    /**
     * Change status sms to active or inactive / ajax
     */
    public function change_sms_status($id, $status)
    {
        if (is_admin()) {
            if ($this->input->is_ajax_request()) {
                $this->sms_model->change_sms_status($id, $status);
            }
        }
    }

    /**
     * Add new sms or edit existing
     */
    public function sms()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);
            if ($id == '') {
                $success = $this->sms_model->add($data);
                $message = _l('problem_adding', _l('sms'));
                if (is_array($success) && isset($success['already_exist']) && $success['already_exist'] == true) {
                    $success = false;
                    $message = _l('sms_already_added_with_the_same_status');
                } else if ($success == true) {
                    $message = _l('added_successfuly', _l('sms'));
                }
            } else {
                $success = $this->sms_model->update($data, $id);
                $message = _l('problem_updating', _l('sms'));
                if ($success == true) {
                    $message = _l('updated_successfuly', _l('sms'));
                }
            }

            echo json_encode(array('success' => $success, 'message' => $message));
        }
    }

    /**
     * Test send sms
     */
    public function test()
    {
        $success = false;
        $message = _l('problem_sending', _l('sms'));
        if ($this->input->is_ajax_request()) {
            $phoneNumberTest = $this->input->post('phone_number_test');
            $messageTest = $this->input->post('message_test');
            if (!empty($phoneNumberTest) && !empty($messageTest)) {
                $result = send_sms_to_recipient($phoneNumberTest, $messageTest);
                if ($result) {
                    $success = true;
                    $message = _l('sending_successfuly', _l('sms'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'message' => $message));
    }

    /**
     * Delete sms
     */
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('sms');
        }
        if (!$id) {
            redirect(admin_url('sms'));
        }

        $response = $this->sms_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('sms')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('sms_lowercase')));
        }

        redirect(admin_url('sms'));
    }
}
