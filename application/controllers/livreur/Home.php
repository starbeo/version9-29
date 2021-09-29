<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Livreur_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
    }

    /**
     * This is client home view
     */
    public function index()
    {
        $data['title'] = _l('dashboard_string');
        $data['bodyclass'] = 'home';
        $this->load->view('livreur/dashboard/home', $data);
    }

    /**
     * Change language 
     */
    public function change_language($language = '')
    {
        $this->session->set_userdata(array('language' => $language));
        redirect(client_url('home'));
    }
}
