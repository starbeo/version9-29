<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis extends Livreur_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('colis_model');
    }

    /**
     * This is livreur menu view
     */
    public function index()
    {
        $data['title'] = _l('colis');
        $this->load->view('livreur/colis/menu', $data);
    }

    /**
     * List colis
     */
    public function liste($status = false, $numeroPage = false, $nameFunction = false)
    {
        if (!is_numeric($status)) {
            redirect(livreur_url('colis'));
        }

        //Get Statuses Colis
        $this->load->model('statuses_colis_model');
        $statut = $this->statuses_colis_model->get($status);
        if (!$statut) {
            redirect(livreur_url('colis'));
        }

        //Get title & color status
        $data['title'] = $statut->name;
        $data['background_color'] = $statut->color;
        //Get Nombre par page
        $nbrParPage = get_option('tables_pagination_limit');
        //Get limit
        $startLimit = $nbrParPage;
        $endLimit = 0;
        if (is_numeric($numeroPage)) {
            $startLimit = $nbrParPage;
            $endLimit = $nbrParPage * ($numeroPage - 1);
        }
        //Where
        $where = array('status_reel' => $status, 'livreur' => get_staff_user_id());
        //Get total rows colis
        $totalRows = count($this->colis_model->get('', $where));
        //Pagination
        $url = livreur_url() . 'colis/' . $nameFunction;
        $data['pagination'] = generate_pagination($url, $totalRows, $nbrParPage);
        //Get colis
        $data['colis'] = $this->colis_model->get('', $where, '', '', $startLimit, $endLimit);
        $data['total'] = $totalRows;

        $this->load->view('livreur/colis/liste', $data);
    }

    /**
     * Detail coli
     */
    public function detail($id = false)
    {
        if (!is_numeric($id)) {
            redirect(livreur_url('colis'));
        }

        //Get colis
        $coli = $this->colis_model->get($id);
        if (!$coli || $coli->livreur != get_staff_user_id()) {
            set_alert('danger', _l('not_found', _l('colis')));
            redirect(livreur_url('colis'));
        }
        //Correction numéro téléphone coli
        $coli->telephone = correctionPhoneNumber($coli->telephone);
        //Get status coli
        $this->load->model('status_model');
        $data['statuts'] = $this->status_model->get('', array('code_barre' => $coli->code_barre), 'desc');

        //Get Statuses Colis
        $this->load->model('statuses_colis_model');
        $data['statuses'] = $this->statuses_colis_model->get('', array('show_in_delivery_app' => 1));
        //Get Locations
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->get();
        //Get Motif
        $data['motifs'] = $this->status_model->get_motif_status();
        //Get url referrer
        $this->load->library('user_agent');
        $data['url_referrer'] = '';
        if (!is_null($this->agent->referrer())) {
            $data['url_referrer'] = $this->agent->referrer();
        }
        
        $data['coli'] = $coli;
        $data['title'] = _l('detail');
        $this->load->view('livreur/colis/detail', $data);
    }

    /**
     * List colis delivred
     */
    public function delivred($page = false)
    {
        $this->liste(2, $page, 'delivred');
    }

    /**
     * List colis shipped
     */
    public function shipped($page = false)
    {
        $this->liste(4, $page, 'shipped');
    }

    /**
     * List colis returned
     */
    public function returned($page = false)
    {
        $this->liste(3, $page, 'returned');
    }

    /**
     * List colis postponed
     */
    public function postponed($page = false)
    {
        $this->liste(11, $page, 'postponed');
    }

    /**
     * List colis unreachable
     */
    public function unreachable($page = false)
    {
        $this->liste(7, $page, 'unreachable');
    }

    /**
     * List colis no answer
     */
    public function no_answer($page = false)
    {
        $this->liste(6, $page, 'no_answer');
    }

    /**
     * List colis refused
     */
    public function refused($page = false)
    {
        $this->liste(9, $page, 'refused');
    }

    /**
     * List colis cancelled
     */
    public function cancelled($page = false)
    {
        $this->liste(10, $page, 'cancelled');
    }

    /**
     * List colis wrong number
     */
    public function wrong_number($page = false)
    {
        $this->liste(8, $page, 'wrong_number');
    }
}
