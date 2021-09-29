<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends Livreur_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('status_model');
    }

    /**
     * Add new status with json
     */
    public function add_json()
    {
        if ($this->input->post() && has_permission('status', '', 'create')) {
            $id = $this->status_model->add($this->input->post());
            if (is_numeric($id)) {
                $success = true;
                $type = 'success';
                $message = _l('added_successfuly', _l('status'));
            } else {
                $success = false;
                $type = 'warning';
                $message = _l('problem_adding', _l('status'));
            }
            echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
        } else {
            $message = _l('access_denied');
            echo json_encode(array('success' => 'false', 'type' => 'danger', 'message' => $message));
        }
    }

    /**
     * Add new status
     */
    public function add($coliId = false)
    {
        //Get url referrer
        $this->load->library('user_agent');
        $urlReferrer = livreur_url();
        if (!is_null($this->agent->referrer())) {
            $urlReferrer = $this->agent->referrer();
        }

        if ($this->input->post()) {
            if (has_permission('status', '', 'create')) {
                $data = $this->input->post();
                $oldUrlReferrer = $data['url_referrer'];
                unset($data['url_referrer']);
                $id = $this->status_model->add($data);
                if (is_numeric($id)) {
                    set_alert('success', _l('added_successfuly', _l('status')));
                    redirect($oldUrlReferrer);
                } else {
                    set_alert('warning', _l('problem_adding', _l('status')));
                }
            } else {
                set_alert('danger', _l('access_denied'));
            }

            redirect($urlReferrer);
        }

        if (!is_numeric($coliId)) {
            redirect($urlReferrer);
        }

        //Get colis
        $this->load->model('colis_model');
        $coli = $this->colis_model->get($coliId);
        if (!$coli || $coli->livreur != get_staff_user_id()) {
            set_alert('danger', _l('not_found', _l('colis')));
            redirect($urlReferrer);
        }
        //Correction numéro téléphone coli
        $coli->telephone = correctionPhoneNumber($coli->telephone);
        //Get Statuses Colis
        $this->load->model('statuses_colis_model');
        $data['statuses'] = $this->statuses_colis_model->get('', array('show_in_delivery_app' => 1));
        //Get Locations
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->get();
        //Get Motif
        $data['motifs'] = $this->status_model->get_motif_status();


        $data['coli'] = $coli;
        $data['url_referrer'] = $urlReferrer;
        $data['title'] = _l('add_status');
        $this->load->view('livreur/status/manage', $data);
    }
}
