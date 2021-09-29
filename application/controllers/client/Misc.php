<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Misc extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('misc_model');
    }

    /**
     * Set notifications client to read
     */
    public function set_notifications_client_read()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->misc_model->set_notifications_client_read()
            ));
        }
    }

    /**
     * Set session that user clicked on customizer menu link to stay open
     */
    public function set_customizer_open()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'customizer-open' => true
            ));
        }
    }
    
    /**
     * Remove customizer open from database
     */
    public function set_customizer_closed()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'customizer-open' => ''
            ));
        }
    }
    
    /**
     * Search on top header
     */
    public function search()
    {
        $data['result'] = $this->misc_model->perform_search_client($this->input->post('q'));
        $this->load->view('client/dashboard/search', $data);
    }
    
    /**
     * Get city point relai
     */
    function get_city($id)
    {
        $this->load->model('points_relais_model');
        $pointRelai = $this->points_relais_model->get($id);
        $city = '';
        if ($pointRelai) {
            $city = $pointRelai->ville;
        }

        echo json_encode(array('city' => $city));
    }
}
