<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends Livreur_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('misc_model');
    }

    /**
     * This is notifications view
     */
    public function index()
    {
        //Get notifications
        $data['notifications'] = $this->misc_model->get_user_notifications(0);

        $data['title'] = _l('notifications');
        $this->load->view('livreur/notifications/manage', $data);
    }
}
