<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis_en_attente extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
    }

    public function add()
    {
        $success = false;
        $message = 'Problème lors de l\'envoi de la commande.';

        //Get Paramètre
        $params = $this->input->post();
        if ($params && $params['token'] == '$2a$08$Wf13Vh3/fN5UArYrPVH31ueCqdiNYnEjiQB9C80z8NLzsh0Nl38Na') {
            $storeToken = $params['store_token'];
            if (!empty($storeToken)) {
                $expediteurId = $this->api_model->get_expediteur_id_by_token($storeToken);
                if (is_numeric($expediteurId)) {
                    //Vérification si le code à barre existe déjà
                    if (!empty($params['barcode'])) {
                        $data['id_expediteur'] = $expediteurId;
                        $data['num_commande'] = $params['barcode'];
                        $data['nom_complet'] = $params['full_name'];
                        $data['crbt'] = $params['total'];
                        $data['telephone'] = $params['phone_number'];
                        $data['adresse'] = $params['address'];
                        $data['ville'] = $params['city'];
                        $data['commentaire'] = $params['comment'] . ' ' . $params['quartier'];
                        //Add Colis en attente
                        $result = $this->api_model->add_colis_en_attente($data);
                        if (is_numeric($result)) {
                            $success = true;
                            $message = 'Commande envoyé avec succèss.';
                        }
                    } else {
                        $message = 'Le code à barre de la commande est vide.';
                    }
                } else {
                    $message = 'Token Store Invalide !!';
                }
            } else {
                $message = 'Token Store Vide !!';
            }
        } else {
            $message = 'Accès API Invalide !!';
        }

        //Return result
        echo json_encode(array('success' => $success, 'message' => $message));
    }
}
