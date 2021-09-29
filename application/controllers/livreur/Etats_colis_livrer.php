<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_colis_livrer extends Livreur_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('etat_colis_livrer_model');
    }

    /**
     * This is etats colis livrer view
     */
    public function index($page = false)
    {
        $this->liste(false, $page, 'index');
    }

    /**
     * List etats colis livrer
     */
    public function liste($status = false, $numeroPage = false, $nameFunction = false)
    {
        //Get title type
        if ($status == 1) {
            $data['title'] = _l('not_confirmed');
            $data['background_color'] = '#fc2d42';
        } else if ($status == 2) {
            $data['title'] = _l('confirmed');
            $data['background_color'] = '#fc2d42';
        } else {
            $data['title'] = _l('all');
            $data['background_color'] = get_option('espace_livreur_mobile_default_color');
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
        $where = 'id_livreur = ' . get_staff_user_id();
        if (is_numeric($status)) {
            $where .= ' AND status = ' . $status;
        }
        //Get total rows colis
        $totalRows = count($this->etat_colis_livrer_model->get('', $where));
        //Pagination
        $url = livreur_url() . 'etats_colis_livrer/' . $nameFunction;
        $data['pagination'] = generate_pagination($url, $totalRows, $nbrParPage);
        //Get colis
        $data['etats_colis_livrer'] = $this->etat_colis_livrer_model->get('', $where, '', '', $startLimit, $endLimit);
        $data['total'] = $totalRows;

        $this->load->view('livreur/etats-colis-livrer/liste', $data);
    }

    /**
     * Detail etat colis livrer
     */
    public function detail($id = false)
    {
        if (!is_numeric($id)) {
            redirect(livreur_url('etats_colis_livrer'));
        }

        //Get etat colis livrer
        $etatColisLivrer = $this->etat_colis_livrer_model->get($id);
        if (!$etatColisLivrer || $etatColisLivrer->id_livreur != get_staff_user_id()) {
            set_alert('danger', _l('not_found', _l('etat_colis_livrer')));
            redirect(livreur_url('etats_colis_livrer'));
        }
        //Get items etat colis livrer
        $data['colis'] = $this->etat_colis_livrer_model->get_items_etat_colis_livrer($id);
        $data['total'] = count($data['colis']);

        $data['etat_colis_livrer'] = $etatColisLivrer;
        $data['title'] = _l('list_colis');
        $this->load->view('livreur/etats-colis-livrer/detail', $data);
    }

    /**
     * List etat colis livrer not confirmed
     */
    public function not_confirmed($page = false)
    {
        $this->liste(1, $page, 'not_confirmed');
    }

    /**
     * List etat colis livrer confirmed
     */
    public function confirmed($page = false)
    {
        $this->liste(2, $page, 'confirmed');
    }
}
