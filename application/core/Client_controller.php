<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_controller extends CRM_Controller
{

    function __construct()
    {
        parent::__construct();

        // Get default language client
        $language = get_option('active_language');
        $clientLanguage = get_client_default_language();
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

        if (!is_expediteur_logged_in()) {
            if (strpos($this->uri->uri_string(), 'authentication/client') === FALSE) {
                $this->session->set_userdata(array(
                    'red_url' => $this->uri->uri_string()
                ));
            }

            redirect(site_url('authentication/client'));
        }
        
        // Load expediteurs model
        $this->load->model('expediteurs_model');
        $this->load->vars(array(
            'expediteur' => $this->expediteurs_model->get(get_expediteur_user_id()),
            '_notifications' => $this->expediteurs_model->get_notifications_client(get_expediteur_user_id())
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
