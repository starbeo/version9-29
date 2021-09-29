<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Villes extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        //add the header here
        header('Content-Type: application/json');
        $success = false;
        $code = 404;
        $list = array();
        $message = 'Problème lors de l\'authentification';

        if ($this->input->post()) {
            if ($this->input->post('token') && $this->config->item('token_api') == $this->input->post('token')) {
                //Load model
                $this->load->model('villes_model');
                //Get cities
                $villes = $this->villes_model->get('', 1);
                if (count($villes) > 0) {
                    $success = true;
                    $code = 200;
                    $message = 'Liste villes';

                    foreach ($villes as $key => $ville) {
                        $list[$key]['value'] = $ville['id'];
                        $list[$key]['name'] = $ville['name'];
                    }
                }
            } else {
                $code = 500;
                $message = 'Token invalide';
            }
        }

        //Return result
        echo json_encode(array('success' => $success, 'code' => $code, 'message' => $message, 'list' => $list));
    }
}
