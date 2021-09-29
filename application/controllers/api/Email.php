<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function send()
    {
        //add the header here
        header('Content-Type: application/json');
        $success = false;
        $code = 404;
        $message = 'Problème lors de l\'authentification';

        if ($this->input->post()) {
            if ($this->input->post('token') && $this->config->item('token_api') == $this->input->post('token')) {
                if (!empty($this->input->post('email')) && !empty($this->input->post('subject')) && !empty($this->input->post('message'))) {
                    //Get Paramètre
                    $params = $this->input->post();
                    $email = $params['email'];
                    $subject = $params['subject'];
                    $message = strip_tags($params['message']);
                    //Load model
                    $this->load->model('emails_model');
                    $send = $this->emails_model->send_simple_email($email, $subject, $message);
                    if ($send) {
                        $success = true;
                        $code = 200;
                        $message = 'Email envoyé avec succées';
                    } else {
                        $code = 300;
                        $message = 'Problème lors de l\'envoi de l\'email';
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
