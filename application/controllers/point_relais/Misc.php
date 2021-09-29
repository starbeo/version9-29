<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Misc extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get city point relai
     */
    function get_city($id)
    {
        $this->load->model('points_relais_model');
        $pointRelai = $this->points_relais_model->get($id);
        $city = '';
        if ($pointRelai) {
            $city = $pointRelai->ville;
        }

        echo json_encode(array('city' => $city));
    }

    /**
     * Get ouverture colis
     */
    public function get_ouverture_colis($expediteur_id)
    {
        //Get Infos Client
        $this->load->model('expediteurs_model');
        $expediteur = $this->expediteurs_model->get($expediteur_id);
        $ouvertureColis = 0;
        $optionFrais = 0;
        if ($expediteur) {
            $ouvertureColis = $expediteur->ouverture;
            $optionFrais = $expediteur->option_frais;
            $optionFraisAssurance = $expediteur->option_frais_assurance;
        }

        echo json_encode(array('ouverture_colis' => $ouvertureColis, 'option_frais' => $optionFrais, 'option_frais_assurance' => $optionFraisAssurance));
    }

    /**
     * Get expediteur by id
     */
    public function get_expediteur_by_id($clientId, $villeId = '')
    {
        //Get Infos Client
        $this->load->model('expediteurs_model');
        $client = $this->expediteurs_model->get($clientId);
        if ($client) {
            $client->date_created = date(get_current_date_format(), strtotime($client->date_created));
        }
        //Get Infos Ville
        $fraisSpecial = 0;
        if (is_numeric($villeId)) {
            $this->load->model('villes_model');
            $ville = $this->villes_model->get($villeId);
            if ($ville) {
                $fraisSpecial = $ville->frais_special;
            }
        }
        //Get percent frais assurance
        $percentFraisAssurance = get_option('pourcentage_frais_assurance');

        echo json_encode(array('expediteur' => $client, 'frais_special' => $fraisSpecial, 'pourcentage_frais_assurance' => $percentFraisAssurance));
    }

    /**
     * Get shipping cost city
     */
    public function get_shipping_cost($cityId)
    {
        //Get Infos Ville
        $this->load->model('villes_model');
        $city = $this->villes_model->get_shipping_cost_city($cityId);
        $shippingCost = 0;
        if ($city) {
            if (is_numeric($city->shipping_cost)) {
                $shippingCost = $city->shipping_cost;
            }
        }

        echo json_encode(array('shipping_cost' => $shippingCost));
    }
    
    /**
     * General search
     */
    public function search()
    {
        $data['result'] = $this->misc_model->perform_search_point_relais($this->input->post('q'));
        $this->load->view('point-relais/dashboard/search', $data);
    }
}
