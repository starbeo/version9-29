<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');
    }

    /**
     * View all settings
     */
    public function index()
    {
        if ($this->input->post()) {
            $success = $this->settings_model->update_options_client($this->input->post());
            if ($success == true) {
                set_alert('success', _l('settings_updated'));
            }

            $red_url = client_url('settings');
            if ($this->input->get('tab_hash')) {
                $red_url = client_url('settings?tab_hash=' . $this->input->get('tab_hash'));
            }

            redirect($red_url, 'refresh');
        }

        $data['tab_hash'] = $this->input->get('tab_hash');
        $data['bodyclass'] = 'top-tabs';
        $data['title'] = _l('settings');
        $this->load->view('client/settings/all', $data);
    }
}
