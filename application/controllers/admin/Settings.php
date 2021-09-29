<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');

        if (get_permission_module('settings') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * View all settings
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('settings');
        }

        if ($this->input->post()) {
            $success = $this->settings_model->update($this->input->post());
            if ($success == true) {
                set_alert('success', _l('settings_updated'));
            }

            $red_url = admin_url('settings');
            if ($this->input->get('tab_hash')) {
                $red_url = admin_url('settings?tab_hash=' . $this->input->get('tab_hash'));
            }

            redirect($red_url, 'refresh');
        }

        // Get backgrounds authentication
        $this->load->model('colis_model');
        $data['backgrounds_authentication'] = $this->settings_model->get_backgrounds_authentication();
        //Get Available merge fields
        $this->load->model('emails_model');
        $data['available_merge_fields'] = $this->emails_model->get_available_merge_fields();
        // Get Statuses
        $this->load->model('colis_model');
        $data['statuses'] = $this->colis_model->get_status_colis();
        // Get themes
        $data['themes'] = array(
            array('id' => 'theme_1', 'name' => _l('theme_1')),
            array('id' => 'theme_2', 'name' => _l('theme_2'))
        );

        $data['tab_hash'] = $this->input->get('tab_hash');
        $data['bodyclass'] = 'top-tabs';
        $data['title'] = _l('settings');
        $data['ckeditor_assets'] = true;
        $this->load->view('admin/settings/all', $data);
    }
}
