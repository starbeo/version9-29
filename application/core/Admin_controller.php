<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_controller extends CRM_Controller
{

    function __construct()
    {
        parent::__construct();

        $language = get_option('active_language');
        $staff_language = get_staff_default_language();

        if (!empty($staff_language)) {
            if (file_exists(APPPATH . 'language/' . $staff_language)) {
                $language = $staff_language;
            }
        }

        if (!is_null($this->session->userdata('language')) && !empty($this->session->userdata('language'))) {
            $_language = $this->session->userdata('language');
            if (file_exists(APPPATH . 'language/' . $_language)) {
                $language = $_language;
            }
        }

        $this->lang->load($language . '_lang', $language);

        $this->load->model('authentication_model');
        $this->authentication_model->autologin();

        if (!is_staff_logged_in()) {
            if (strpos($this->uri->uri_string(), 'authentication/admin') === FALSE) {
                $this->session->set_userdata(array(
                    'red_url' => $this->uri->uri_string()
                ));
            }

            redirect(site_url('authentication/admin'));
        }

        // In case staff have setup logged in as client
        $this->session->unset_userdata('client_user_id');
        $this->session->unset_userdata('client_logged_in');
        $this->session->unset_userdata('logged_in_as_client');

        $this->load->model('staff_model');
        $this->load->vars(array(
            '_staff' => $this->staff_model->get(get_staff_user_id()),
            '_notifications' => $this->misc_model->get_user_notifications(false),
            '_notifications_clients' => $this->misc_model->get_notifications_client(false),
            '_notifications_staff' => $this->misc_model->get_notifications_staff(false),
            'google_api_key' => get_option('google_api_key')
        ));

        $this->load->library('user_agent');
        if ($this->agent->is_mobile()) {
            $this->session->set_userdata(array(
                'is_mobile' => true
            ));
        } else {
            $this->session->unset_userdata('is_mobile');
        }
    }
}
