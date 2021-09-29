<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends CI_Controller
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
                if (!empty($this->input->post('barcode'))) {
                    //Load model
                    $this->load->model('status_model');
                    //Get status
                    $statuts = $this->status_model->suivi_colis($this->input->post('barcode'));
                    if (count($statuts) > 0) {
                        $success = true;
                        $code = 200;
                        $message = 'Liste statuts';

                        foreach ($statuts as $key => $statut) {
                            $list[$key]['statut'] = $statut['name'];
                            $list[$key]['couleur_statut'] = $statut['color'];
                            $list[$key]['emplacement'] = $statut['emplacement'];
                            $list[$key]['date'] = $statut['date_created'];
                        }
                    } else {
                        $code = 300;
                        $message = 'Aucun statut n\'est trouvé pour ce code d\'envoi, vérifier le';
                    }
                } else {
                    $code = 403;
                    $message = 'Le code d\'envoi est obligatoire';
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
