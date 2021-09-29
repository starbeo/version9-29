<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Livreur_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
    }

    /**
     * This is staff profile view
     */
    public function index()
    {
        $data['title'] = _l('delivery_men');
        $this->load->view('livreur/profile/manage', $data);
    }
}
