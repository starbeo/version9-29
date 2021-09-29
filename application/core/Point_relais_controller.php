<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Point_relais_controller extends CRM_Controller
{

    function __construct()
    {
        parent::__construct();

        // Get default language client
        $language = get_option('active_language');
        $pointRelaisLanguage = get_staff_default_language();
        if (!empty($pointRelaisLanguage)) {
            if (file_exists(APPPATH . 'language/' . $pointRelaisLanguage)) {
                $language = $pointRelaisLanguage;
            }
        }
        // Check language in session
        if (!is_null($this->session->userdata('language')) && !empty($this->session->userdata('language'))) {
            $_language = $this->session->userdata('language');
            if (file_exists(APPPATH . 'language/' . $_language)) {
                $language = $_language;
            }
        }
        // Load language
        $this->lang->load($language . '_lang', $language);
        // Load authentication model
        $this->load->model('authentication_model');
        $this->authentication_model->autologin();

        if (!is_point_relais_logged_in()) {
            if (strpos($this->uri->uri_string(), 'authentication/point_relais') === FALSE) {
                $this->session->set_userdata(array(
                    'red_url' => $this->uri->uri_string()
                ));
            }

            redirect(site_url('authentication/point_relais'));
        }

        // Load staff model
        $this->load->model('staff_model');
        $this->load->vars(array(
            '_staff' => $this->staff_model->get(get_staff_user_id()),
            '_notifications' => $this->misc_model->get_user_notifications(false)
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
