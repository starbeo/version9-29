<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bons_livraison extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bon_livraison_customer_model');
    }

    /**
     * List all bons livraison
     */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblbonlivraisoncustomer.nom',
                '(SELECT COUNT(id) FROM tblbonlivraisoncustomercolis WHERE bonlivraison_id = tblbonlivraisoncustomer.id) as nbr_colis',
                'tblbonlivraisoncustomer.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblbonlivraisoncustomer';

            $join = array();
            $where = array('AND tblbonlivraisoncustomer.id_expediteur = ' . get_expediteur_user_id());
            //Filtre
            if ($this->input->post('f-date-created-start') && is_date(to_sql_date($this->input->post('f-date-created-start')))) {
                array_push($where, ' AND tblbonlivraisoncustomer.date_created >= "' . to_sql_date($this->input->post('f-date-created-start')) . ' 00:00:00"');
            }
            if ($this->input->post('f-date-created-end') && is_date(to_sql_date($this->input->post('f-date-created-end')))) {
                array_push($where, ' AND tblbonlivraisoncustomer.date_created <= "' . to_sql_date($this->input->post('f-date-created-end')) . ' 23:59:59"');
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblbonlivraisoncustomer.date_created LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblbonlivraisoncustomer.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblbonlivraisoncustomer.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblbonlivraisoncustomer.id'), 'tblbonlivraisoncustomer.id', '', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblbonlivraisoncustomer.nom') {
                        $_data = '<a href="' . client_url('bons_livraison/bon/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'nbr_colis') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    } else if ($aColumns[$i] == 'tblbonlivraisoncustomer.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }
                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('client/bons_livraison/bon/' . $aRow['id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Bon Livraison'));
                if ($aRow['nbr_colis'] > 0) {
                    $options .= icon_btn('client/bons_livraison/pdf/' . $aRow['id'], 'file-pdf-o', 'btn-danger', array('title' => 'Imprimer PDF Bon Livraison'));
                    $options .= icon_btn('client/bons_livraison/etiquette/' . $aRow['id'], 'file-image-o', 'btn-info', array('title' => 'Imprimer Etiquette'));
                }
                $options .= icon_btn('client/bons_livraison/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm', array('title' => 'Supprimer Bon Livraison'));
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('delivery_note');
        $this->load->view('client/bons-livraison/manage', $data);
    }

    /**
     * Add or Edit Delivery note
     */
    public function bon($id = '')
    {
        if (!is_numeric($id)) {
            $lastBonLivraison = $this->bon_livraison_customer_model->get_last_bon_livraison();
            if (!is_null($lastBonLivraison) && total_rows('tblbonlivraisoncustomercolis', array('bonlivraison_id' => $lastBonLivraison->id)) == 0) {
                $success = $this->bon_livraison_customer_model->update(array(), $lastBonLivraison->id);
                if ($success) {
                    redirect(client_url('bons_livraison/bon/' . $lastBonLivraison->id));
                } else {
                    redirect(client_url('bons_livraison'));
                }
            } else {
                $id = $this->bon_livraison_customer_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('delivery_note')));
                    redirect(client_url('bons_livraison/bon/' . $id));
                }
            }
        } else {
            $bonLivraison = $this->bon_livraison_customer_model->get($id);
            if (!$bonLivraison) {
                set_alert('warning', _l('not_found', _l('delivery_note')));
                redirect(client_url('bons_livraison'));
            }

            $data['bon_livraison'] = $bonLivraison;
            $title = _l('delivery_note_lowercase');
        }

        $data['title'] = $title;
        $this->load->view('client/bons-livraison/bon', $data);
    }

    /**
     * Init colis bons livraison
     */
    public function init_colis_bon_livraison()
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblcolisenattente.id',
                'tblcolisenattente.code_barre',
                'tblcolisenattente.nom_complet',
                'tblvilles.name',
                'tblcolisenattente.crbt',
                'tblcolisenattente.date_creation'
            );

            $sIndexColumn = "id";
            $sTable = 'tblcolisenattente';

            $join = array('LEFT JOIN tblvilles ON tblvilles.id = tblcolisenattente.ville');
            
            $where = array('AND tblcolisenattente.id_expediteur = ' . get_expediteur_user_id());
            array_push($where, ' AND tblcolisenattente.num_bonlivraison IS NULL');
            array_push($where, ' AND tblcolisenattente.colis_id IS NULL');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array(), 'tblcolisenattente.id', '', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcolisenattente.id') {
                        $_data = icon_btn('#', 'plus', 'btn-success colis_added', array('data-id' => $_data));
                    } else if ($aColumns[$i] == 'tblcolisenattente.code_barre') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblcolisenattente.date_creation') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * Init historique colis bons livraison
     */
    public function init_historique_colis_bon_livraison($bonLivraisonId)
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblbonlivraisoncustomercolis.id as colisbonlivraison_id',
                'tblcolisenattente.code_barre',
                'tblcolisenattente.nom_complet',
                'tblvilles.name',
                'tblcolisenattente.crbt',
                'tblcolisenattente.date_creation'
            );

            $sIndexColumn = "id";
            $sTable = 'tblbonlivraisoncustomercolis';

            $join = array(
                'LEFT JOIN tblcolisenattente ON tblcolisenattente.id = tblbonlivraisoncustomercolis.colis_id',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolisenattente.ville'
            );
            
            $where = array('AND tblbonlivraisoncustomercolis.bonlivraison_id = ' . $bonLivraisonId);
            array_push($where, ' AND tblcolisenattente.id_expediteur = ' . get_expediteur_user_id());
            
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array(), 'tblbonlivraisoncustomercolis.id', '', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'colisbonlivraison_id') {
                        $_data = icon_btn('#', 'remove', 'btn-danger colis_remove', array('data-colisbonlivraison-id' => $_data));
                    } else if ($aColumns[$i] == 'tblcolisenattente.code_barre') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblcolisenattente.date_creation') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * Add colis to bons livraison
     */
    public function add_colis_to_bon_livraison()
    {
        $bonlivraisonId = $this->input->post('bonlivraison_id');
        $colisId = $this->input->post('colis_id');

        $success = false;
        $type = 'warning';
        $message = _l('problem_adding', _l('colis_delivery_note_lowercase'));
        if (is_numeric($bonlivraisonId) && is_numeric($colisId)) {
            $id = $this->bon_livraison_customer_model->add_colis_to_bon_livraison($bonlivraisonId, $colisId);
            if (is_numeric($id)) {
                $success = true;
                $type = 'success';
                $message = _l('added_successfuly', _l('colis_delivery_note_lowercase'));
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Remove colis to bons livraison
     */
    public function remove_colis_to_bon_livraison()
    {
        $colisbonlivraisonId = $this->input->post('colisbonlivraison_id');

        $success = false;
        $type = 'warning';
        $message = _l('problem_deleting', _l('colis_delivery_note_lowercase'));
        if (is_numeric($colisbonlivraisonId)) {
            $success = $this->bon_livraison_customer_model->remove_colis_to_bon_livraison($colisbonlivraisonId);
            if ($success) {
                $type = 'success';
                $message = _l('deleted', _l('colis_delivery_note_lowercase'));
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Print PDF
     */
    public function pdf($id)
    {
        if (!is_numeric($id)) {
            redirect(client_url('bons_livraison'));
        }

        $bonLivraison = $this->bon_livraison_customer_model->get($id);
        if ($bonLivraison->id_expediteur == get_expediteur_user_id() || is_staff_logged_in() == true) {
            $this->load->model('expediteurs_model');
            $expediteur = $this->expediteurs_model->get($bonLivraison->id_expediteur);
            $bonLivraison->items = $this->bon_livraison_customer_model->get_items_bon_livraison($id);
            if (count($bonLivraison->items) == 0) {
                set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
                redirect(client_url('bons_livraison'));
            } else {
                $bonLivraison->nom_expediteur = $expediteur->nom;
                $bonLivraison->telephone_expediteur = $expediteur->telephone;
                $bonLivraison->contact_expediteur = $expediteur->contact;
                bon_livraison_customer_pdf($bonLivraison);
            }
        } else {
            redirect(client_url('bons_livraison'));
        }
    }

    /**
     * Print PDF
     */
    public function etiquette($id)
    {
        if (!is_numeric($id)) {
            redirect(client_url('bons_livraison'));
        }

        $bonLivraison = $this->bon_livraison_customer_model->get($id);
        if ($bonLivraison->id_expediteur == get_expediteur_user_id() || is_staff_logged_in() == true) {
            $bonLivraison->items = $this->bon_livraison_customer_model->get_items_bon_livraison($id);
            $this->load->model('expediteurs_model');
            $expediteur = $this->expediteurs_model->get($bonLivraison->id_expediteur);
            if ($expediteur) {
                $bonLivraison->client = $expediteur;
            }

            if (count($bonLivraison->items) == 0) {
                set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
                redirect(client_url('bons_livraison'));
            } else {
                etiquette_bon_livraison_customer_pdf($bonLivraison);
            }
        } else {
            redirect(client_url('bons_livraison'));
        }
    }

    /**
     * Delete bon livraison from database
     */
    public function delete($id)
    {
        if (!is_numeric($id)) {
            redirect(client_url('bons_livraison'));
        }
        
        $success = $this->bon_livraison_customer_model->delete($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('delivery_note')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('delivery_note_lowercase')));
        }

        redirect(client_url('bons_livraison'));
    }
}
