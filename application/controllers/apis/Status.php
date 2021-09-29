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
            if ($this->input->post('token') && !empty($this->input->post('token'))) {
                $token = $this->input->post('token');
                //Load model
                $this->load->model('apis_model');
                //Check if access exist
                $exist = $this->apis_model->get_access_by_token($token);
                if($exist) {
                    $accessId = $exist->id;
                    //Get access
                    $access = $this->apis_model->get_access($accessId, array('status' => 2));
                    if($access) {
                        $clientId = $access->client_id;
                        $packId = $access->pack_id;
                        $nbrAppels = $access->nbr_appels;
                        //Get pack
                        $pack = $this->apis_model->get_packs($packId);
                        if($pack) {
                            if($pack->nbr_limit > $nbrAppels) {
                                if (!empty($this->input->post('barcode'))) {
                                    //Load model
                                    $this->load->model('colis_model');
                                    $this->load->model('status_model');
                                    //Get coli
                                    $coli = $this->colis_model->get('', array('code_barre' => $this->input->post('barcode')));
                                    if($coli && is_numeric($coli[0]['id_expediteur']) && $coli[0]['id_expediteur'] == $clientId) {
                                        //Get status
                                        $statuts = $this->status_model->suivi_colis($this->input->post('barcode'), array(''));
                                        if (count($statuts) > 0) {
                                            //Update access
                                            $this->apis_model->increment_number_of_appels_access($accessId);
                                
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
                                        $code = 301;
                                        $message = 'Le code d\'envoi n\'existe pas';
                                    }
                                } else {
                                    $code = 302;
                                    $message = 'Le code d\'envoi est obligatoire';
                                }
                            } else {
                                //Update access
                                $this->apis_model->block_access($accessId);
                                $code = 303;
                                $message = 'Nombre d\'appels épuisé';
                            }
                        }
                    } else {
                        $code = 304;
                        $message = 'Accès bloqué';
                    }
                } else {
                    $code = 405;
                    $message = 'Token invalide';
                }
            } else {
                $code = 500;
                $message = 'Token vide';
            }
        }

        //Return result
        echo json_encode(array('success' => $success, 'code' => $code, 'message' => $message, 'list' => $list));
    }
}
