<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bons_livraison extends Livreur_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bon_livraison_model');
    }

    /**
     * This is delivery notes view
     */
    public function index()
    {
        $data['title'] = _l('delivery_notes');
        $this->load->view('livreur/bons-livraison/menu', $data);
    }

    /**
     * List delivery notes
     */
    public function liste($type = false, $numeroPage = false, $nameFunction = false)
    {
        if (!is_numeric($type)) {
            redirect(livreur_url('bons_livraison'));
        }

        //Get title type
        if ($type == 1) {
            $data['title'] = _l('delivery_note_type_output');
            $data['background_color'] = '#03a9f4';
        } else {
            $data['title'] = _l('delivery_note_type_returned');
            $data['background_color'] = '#fc2d42';
        }

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
        $where = array('type' => $type, 'id_livreur' => get_staff_user_id());
        //Get total rows colis
        $totalRows = count($this->bon_livraison_model->get('', $where));
        //Pagination
        $url = livreur_url() . 'bons_livraison/' . $nameFunction;
        $data['pagination'] = generate_pagination($url, $totalRows, $nbrParPage);
        //Get colis
        $data['bons_livraison'] = $this->bon_livraison_model->get('', $where, '', '', $startLimit, $endLimit);
        $data['total'] = $totalRows;

        $this->load->view('livreur/bons-livraison/liste', $data);
    }

    /**
     * Detail delivery note
     */
    public function detail($id = false)
    {
        if (!is_numeric($id)) {
            redirect(livreur_url('bons_livraison'));
        }

        //Get delivery note
        $bonLivraison = $this->bon_livraison_model->get($id);
        if (!$bonLivraison || $bonLivraison->id_livreur != get_staff_user_id()) {
            set_alert('danger', _l('not_found', _l('delivery_note')));
            redirect(livreur_url('bons_livraison'));
        }
        //Get items delivery note
        $data['colis'] = $this->bon_livraison_model->get_items_bon_livraison($id);
        $data['total'] = count($data['colis']);

        $data['bon_livraison'] = $bonLivraison;
        $data['title'] = _l('list_colis');
        $this->load->view('livreur/bons-livraison/detail', $data);
    }

    /**
     * List delivery notes output
     */
    public function output($page = false)
    {
        $this->liste(1, $page, 'output');
    }

    /**
     * List delivery notes returned
     */
    public function returned($page = false)
    {
        $this->liste(2, $page, 'returned');
    }
}
