<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Misc extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('misc_model');
    }

    public function get_taxes_dropdown_template()
    {
        $name = $this->input->post('name');
        $taxid = $this->input->post('taxid');
        echo $this->misc_model->get_taxes_dropdown_template($name, $taxid);
    }

    public function get_taxes_dropdown_template_other_expenses()
    {
        $name = $this->input->post('name');
        $taxid = $this->input->post('taxid');
        echo $this->misc_model->get_taxes_dropdown_template_other_expenses($name, $taxid);
    }

    public function get_relation_data()
    {

        if ($this->input->post()) {
            $type = $this->input->post('type');
            $data = get_relation_data($type);

            if ($this->input->post('rel_id')) {
                $rel_id = $this->input->post('rel_id');
            } else {
                $rel_id = '';
            }

            init_relation_options($data, $type, $rel_id);
        }
    }

    public function send_file($clientid)
    {
        if ($this->input->post('send_file_email')) {
            if ($this->input->post('file_path')) {
                $this->load->model('emails_model');
                $this->emails_model->add_attachment(array(
                    'attachment' => $this->input->post('file_path'),
                    'filename' => $this->input->post('file_name'),
                    'type' => $this->input->post('filetype'),
                    'read' => true
                ));
                $message = $this->input->post('send_file_message');
                $message .= get_option('email_signature');
                $success = $this->emails_model->send_simple_email($this->input->post('send_file_email'), $this->input->post('send_file_subject'), $message);

                if ($success) {
                    set_alert('success', _l('custom_file_success_send', $this->input->post('send_file_email')));
                } else {
                    set_alert('warning', _l('custom_file_fail_send'));
                }
            }
        }

        redirect($this->input->post('return_url'));
    }
    /* Since version 1.0.2 add client reminder */

    public function add_reminder($rel_id_id, $rel_type)
    {

        $message = '';
        $alert_type = 'warning';
        if ($this->input->post()) {
            $success = $this->misc_model->add_reminder($this->input->post(), $rel_id_id);
            if ($success) {
                $alert_type = 'success';
                $message = _l('reminder_added_successfuly');
            }
        }

        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function run_cron_manually()
    {
        if (has_permission('editSettings')) {
            $this->load->model('cron_model');
            $this->cron_model->run(true);
            redirect(admin_url('settings'));
        }
    }
    /* Since Version 1.0.1 - General search */

    public function search()
    {
        $data['result'] = $this->misc_model->perform_search($this->input->post('q'));
        $this->load->view('admin/search', $data);
    }
    /* Remove customizer open from database */

    public function set_customizer_closed()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'customizer-open' => ''
            ));
        }
    }
    /* Set session that user clicked on customizer menu link to stay open */

    public function set_customizer_open()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'customizer-open' => true
            ));
        }
    }
    /* User dismiss announcement */

    public function dismiss_announcement()
    {
        if ($this->input->post()) {
            $this->misc_model->dismiss_announcement($this->input->post());
        }
    }
    /* Set notifications clients to read */

    public function set_notifications_clients_read()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->misc_model->set_notifications_clients_read()
            ));
        }
    }
    /* Set notifications to read */

    public function set_notifications_staffs_read()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->misc_model->set_notifications_staffs_read()
            ));
        }
    }
    /* Add new client /staff note from admin area */

    public function add_user_note($userid, $staff)
    {

        if (!$userid) {
            die('No user id found');
        }

        $redirect_controller = 'clients';
        $redirect_method = 'client';
        if ($staff == 1) {
            $redirect_controller = 'staff';
            $redirect_method = 'member';
        }

        if ($this->input->post()) {
            $success = $this->misc_model->add_user_note($this->input->post(), $userid, $staff);
            if ($success) {
                set_alert('success', 'User note added successfuly');
            }
            redirect(admin_url('' . $redirect_controller . '/' . $redirect_method . '/' . $userid));
        }
    }
    /* Delete client /staff note from admin area */

    public function remove_user_note($noteid, $userid, $staff)
    {
        if (!$noteid) {
            die('No note id found');
        }

        $redirect_controller = 'clients';
        $redirect_method = 'client';
        if ($staff == 1) {
            $redirect_controller = 'staff';
            $redirect_method = 'member';
        }

        $success = $this->misc_model->delete_user_note($noteid);
        if ($success) {
            set_alert('success', 'User note deleted successfuly');
        }

        redirect(admin_url('' . $redirect_controller . '/' . $redirect_method . '/' . $userid));
    }
    /* Check if staff email exists / ajax */

    public function staff_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $member_id = $this->input->post('memberid');

                if ($member_id != 'undefined') {
                    $this->db->where('staffid', $member_id);
                    $_current_email = $this->db->get('tblstaff')->row();

                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }

                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results('tblstaff');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
    /* Goes blank page but with messagae access danied / message set from session flashdata */

    public function access_denied()
    {
        $this->load->view('admin/blank_page');
    }
    /* Goes to blank page with message page not found / message set from session flashdata */

    public function not_found()
    {
        $this->load->view('admin/blank_page');
    }
    /* Get role permission for specific role id / Function relocated here becuase the Roles Model have statement on top if has role permission */

    public function get_role_permissions_ajax($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->roles_model->get_role_permissions($id));
            die();
        }
    }
    
    /**
     * Check if telephone has +212
     */
    public function check_telephone()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $telephone = $this->input->post('telephone');
                if (strlen($telephone) == 10) {
                    if (!preg_match("/^[0-9]{10}$/", $telephone)) {
                        echo json_encode(false);
                    } else {
                        echo json_encode(true);
                    }
                } else {
                    echo json_encode(false);
                }
                die();
            }
        }
    }
    
}
