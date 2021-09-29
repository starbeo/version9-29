<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
    }

    /**
     * When staff edit his profile
     */
    public function edit()
    {
        if ($this->input->post()) {
            handle_staff_profile_image_upload();
            $success = $this->staff_model->update($this->input->post(), get_staff_user_id());
            if ($success) {
                set_alert('success', _l('staff_profile_updated'));
            }
            redirect(point_relais_url('profile/edit/' . get_staff_user_id()));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $data['member'] = $member;

        $data['title'] = $member->firstname . ' ' . $member->lastname;
        $this->load->view('point-relais/profile/edit', $data);
    }

    /**
     * When staff change his password
     */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $response = $this->staff_model->change_password($this->input->post(), get_staff_user_id());

            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorect'));
            } else {
                if ($response == true) {
                    set_alert('success', _l('staff_password_changed'));
                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }

            redirect(point_relais_url('profile/edit'));
        }
    }

    /**
     * Remove staff profile image / ajax
     */
    public function remove_staff_profile_image()
    {
        $staffId = get_staff_user_id();
        $member = $this->staff_model->get($staffId);
        if (file_exists(STAFF_PROFILE_IMAGES_FOLDER . $staffId)) {
            delete_dir(STAFF_PROFILE_IMAGES_FOLDER . $staffId);
        }
        $this->db->where('staffid', $staffId);
        $this->db->update('tblstaff', array(
            'profile_image' => NULL
        ));
        if ($this->db->affected_rows() > 0) {
            redirect(point_relais_url('profile/edit/' . $staffId));
        }
    }
}
