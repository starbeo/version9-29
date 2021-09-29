<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reception_sms extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function add()
    {
        //add the header here
        header('Content-Type: application/json');
        $success = false;
        $code = 404;
        $message = 'Problème lors de l\'authentification';
        
        if ($this->input->post()) {
            if ($this->input->post('token') && $this->config->item('token_reception_sms_api') == $this->input->post('token')) {
                if (!empty($this->input->post('sender')) && !empty($this->input->post('message'))) {
                    $data['telephone'] = $this->input->post('sender');
                    $data['message'] = $this->input->post('message');
                    $data['id_entreprise'] = 0;
                    //Add sms received
                    $this->load->model('reception_sms_model');
                    $success = $this->reception_sms_model->add($data);
                    if ($success) {
                        $code = 200;
                        $message = 'Authentification avec succès';
                    } else {
                        $code = 400;
                        $message = 'Problème lors de l\'insertion';
                    }
                } else {
                    $code = 403;
                    $message = 'Paramètres vide';
                }
            } else {
                $code = 500;
                $message = 'Token invalide';
            }
        }

        echo json_encode(array('success' => $success, 'code' => $code, 'message' => $message));
    }
}
