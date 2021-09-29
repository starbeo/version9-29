<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Livreur_controller extends CRM_Controller
{

    function __construct()
    {
        parent::__construct();

        // Get default language livreur
        $language = get_option('active_language');
        $clientLanguage = get_staff_default_language();
        if (!empty($clientLanguage)) {
            if (file_exists(APPPATH . 'language/' . $clientLanguage)) {
                $language = $clientLanguage;
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

        if (!is_staff_logged_in()) {
            if (strpos($this->uri->uri_string(), 'authentication/admin') === FALSE) {
                $this->session->set_userdata(array(
                    'red_url' => $this->uri->uri_string()
                ));
            }

            redirect(site_url('authentication/admin'));
        }
        
        // Load model
        $this->load->model('staff_model');
        $this->load->vars(array(
            '_staff' => $this->staff_model->get(get_staff_user_id()),
            '_notifications' => $this->misc_model->get_user_notifications(false),
            '_notifications_clients' => $this->misc_model->get_notifications_client(false),
            '_notifications_staff' => $this->misc_model->get_notifications_staff(false),
            'google_api_key' => get_option('google_api_key'),
            '_default_color' => get_option('espace_livreur_mobile_default_color'),
            '_default_writing_color' => get_option('espace_livreur_mobile_default_writing_color')
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
