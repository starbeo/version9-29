<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends CI_Controller
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
            if ($this->input->post('token') && $this->config->item('token_api') == $this->input->post('token')) {
                if (!empty($this->input->post('store')) && !empty($this->input->post('contact')) && !empty($this->input->post('phone_number')) && !empty($this->input->post('email')) && !empty($this->input->post('adress')) && is_numeric($this->input->post('city'))) {
                    //Load model
                    $this->load->model('clients_en_attente_model');
                    //Get Paramètre
                    $params = $this->input->post();
                    //Check if client already exist
                    $clientEnAttente = $this->clients_en_attente_model->get('', '', array('email' => $params['email']));
                    if (count($clientEnAttente) == 0) {
                        //Data
                        $data['societe'] = $params['store'];
                        $data['personne_a_contacte'] = $params['contact'];
                        $data['telephone'] = $params['phone_number'];
                        $data['email'] = $params['email'];
                        $data['adresse'] = $params['adress'];
                        $data['ville_id'] = $params['city'];
                        //Add clients en attente
                        $result = $this->clients_en_attente_model->add($data);
                        if (is_numeric($result)) {
                            $success = true;
                            $code = 200;
                            $message = 'Client ajouté avec succées';
                        } else {
                            $code = 300;
                            $message = 'Problème lors de l\'ajout du client.';
                        }
                    } else {
                        $code = 400;
                        $message = 'Client existe déjà avec cette adresse email.';
                    }
                } else {
                    $code = 403;
                    $message = 'Tous les champs sont obligatoire';
                }
            } else {
                $code = 500;
                $message = 'Token invalide';
            }
        }

        //Return result
        echo json_encode(array('success' => $success, 'code' => $code, 'message' => $message));
    }
}
