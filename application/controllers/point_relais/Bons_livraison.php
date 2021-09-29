<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bons_livraison extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bon_livraison_model');
    }

    /**
     * List all bon livraison
     */
    public function index($type = false, $status = false)
    {
        //Get points relais staff
        $pointsRelaisStaff = get_staff_points_relais();
        if ($this->input->is_ajax_request()) {
            $aColumns = array('tblbonlivraison.id', 'tblbonlivraison.nom', 'tblbonlivraison.type', 'tblbonlivraison.status', '(SELECT count(id) FROM tblcolisbonlivraison WHERE bonlivraison_id = tblbonlivraison.id) as nbr_colis', 'tblpointsrelais.nom as point_relai', 'tblbonlivraison.date_created', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_staff');

            $sIndexColumn = "id";
            $sTable = 'tblbonlivraison';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblbonlivraison.id_utilisateur',
                'LEFT JOIN tblpointsrelais ON tblpointsrelais.id = tblbonlivraison.point_relai_id'
            );

            $where = array('AND tblbonlivraison.point_relai_id IN ' . $pointsRelaisStaff);
            if (is_numeric($type)) {
                array_push($where, 'AND tblbonlivraison.type = ' . $type);
            }
            if (is_numeric($status)) {
                array_push($where, 'AND tblbonlivraison.status = ' . $status);
            }

            //Filtre
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                array_push($where, ' AND tblbonlivraison.point_relai_id = ' . $this->input->post('f-point-relai'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblbonlivraison.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblbonlivraison.date_created LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblbonlivraison.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblbonlivraison.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, array());
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblbonlivraison.nom') {
                        $_data = render_btn_copy('column-name-bl-' . $key, 'name_of_delivery_note') . '<a id="column-name-bl-' . $key . '" href="' . point_relais_url('bons_livraison/bon/' . $aRow['tblbonlivraison.id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.type') {
                        if ($_data == 1) {
                            $_data = '<span class="label label-info">' . _l('delivery_note_type_output') . '</span>';
                        } else if ($_data == 2) {
                            $_data = '<span class="label label-danger">' . _l('delivery_note_type_returned') . '</span>';
                        }
                    } else if ($aColumns[$i] == 'tblbonlivraison.status') {
                        $_data = format_status_bon_livraison($_data);
                    } else if ($aColumns[$i] == 'nbr_colis') {
                        $_data = render_nombre_colis($_data);
                    } else if ($aColumns[$i] == 'point_relai') {
                        $_data = render_icon_university() . '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'fullname_staff') {
                        $_data = render_icon_user() . '<b>' . $_data . '</b>';
                    }
                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('point_relais/bons_livraison/bon/' . $aRow['tblbonlivraison.id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Bon Livraison'));
                if ($aRow['nbr_colis'] > 0) {
                    $options .= icon_btn('point_relais/bons_livraison/pdf/' . $aRow['tblbonlivraison.id'], 'file-pdf-o', 'btn-success', array('title' => 'Imprimer PDF Bon Livraison'));
                    $options .= icon_btn('point_relais/bons_livraison/etiquette/' . $aRow['tblbonlivraison.id'], 'file-image-o', 'btn-info', array('title' => 'Imprimer Etiquette'));
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get Type
        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }
        // Get points relais
        $this->load->model('points_relais_model');
        $data['points_relais'] = $this->points_relais_model->get('', 1, 'tblpointsrelais.id IN ' . $pointsRelaisStaff, 'tblpointsrelais.id, CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as nom');

        $data['title'] = _l('delivery_note');
        $this->load->view('point-relais/bons-livraison/manage', $data);
    }

    /**
     * Bon de livraison sortie
     */
    public function sortie()
    {
        $this->index(1);
    }

    /**
     * Bon de livraison Retourner
     */
    public function retourner()
    {
        $this->index(2);
    }

    /**
     * Bon de livraison non confirmer
     */
    public function non_confirmer()
    {
        $this->index(false, 1);
    }

    /**
     * Bon de livraison confirmer
     */
    public function confirmer()
    {
        $this->index(false, 2);
    }

    /**
     * Edit or add new bon
     */
    public function bon($id = false, $type = false)
    {
        if ($this->input->post() && !is_numeric($id)) {
            $id = $this->bon_livraison_model->add($this->input->post());
            if ($id) {
                set_alert('success', _l('added_successfuly', _l('delivery_note')));
                redirect(point_relais_url('bons_livraison/bon/' . $id));
            }
        }

        if (!is_numeric($id)) {
            $title = _l('add_new', _l('delivery_note_lowercase'));
            $data['class1'] = 'col-md-6';
            $data['class2'] = '';
        } else {
            $bonLivraison = $this->bon_livraison_model->get($id);
            //Get array points relais staff
            $pointsRelaisStaffArray = get_staff_points_relais(true);
            if (!$bonLivraison || is_null($bonLivraison->point_relai_id) || !in_array($bonLivraison->point_relai_id, $pointsRelaisStaffArray)) {
                set_alert('warning', _l('not_found', _l('delivery_note')));
                redirect(point_relais_url('bons_livraison'));
            }

            $data['bon_livraison'] = $bonLivraison;
            $title = $bonLivraison->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        //Get points relais staff
        $pointsRelaisStaff = get_staff_points_relais();
        //Get types
        $data['types'] = $this->bon_livraison_model->get_types();
        // Get points relais
        $this->load->model('points_relais_model');
        $data['points_relais'] = $this->points_relais_model->get('', 1, 'tblpointsrelais.id IN ' . $pointsRelaisStaff, 'tblpointsrelais.id, CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as nom');
        //Get type
        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }

        $data['title'] = $title;
        $this->load->view('point-relais/bons-livraison/bon', $data);
    }

    /**
     * Get id coli
     */
    public function get_id_coli()
    {
        $barcode = $this->input->post('barcode');
        $this->load->model('colis_model');
        $coli = $this->colis_model->get_id_coli_by_barcode($barcode);

        $success = false;
        $message = 'Problème lors de la récupération de l\'id de la coli';
        $id = '';
        if ($coli && is_numeric($coli->id)) {
            $success = true;
            $message = 'Id récupérer avec succées.';
            $id = $coli->id;
        }

        echo json_encode(array('success' => $success, 'message' => $message, 'id' => $id));
    }

    /**
     * Add colis to bon ivraison
     */
    public function add_colis_to_bon_livraison()
    {
        $success = false;
        $type = 'warning';
        $message = _l('access_denied');

        $colisId = $this->input->post('colis_id');
        $bonlivraisonId = $this->input->post('bonlivraison_id');
        if (is_numeric($colisId) && is_numeric($bonlivraisonId)) {
            $result = $this->bon_livraison_model->add_colis_to_bon_livraison($bonlivraisonId, $colisId);
            if (is_numeric($result)) {
                $success = true;
                $type = 'success';
                $message = _l('added_successfuly', _l('colis_delivery_note_lowercase'));
            } else if (is_array($result) && array_key_exists('bon_livraison_confirmer', $result)) {
                $message = _l('delivery_note_already_confirmed');
            } else if (is_array($result) && array_key_exists('colis_already_exists_in_the_delivery_note', $result)) {
                $message = _l('colis_already_exists_in_the_delivery_note');
            } else if (is_array($result) && array_key_exists('colis_already_delivered_or_returned', $result)) {
                $message = _l('colis_already_delivered_or_returned');
            } else {
                $message = _l('problem_adding', _l('colis_delivery_note_lowercase'));
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Remove colis to bon ivraison
     */
    public function remove_colis_to_bon_livraison()
    {
        $success = false;
        $type = 'warning';
        $message = _l('access_denied');

        $colisBonLivraisonId = $this->input->post('colisbonlivraison_id');
        if (is_numeric($colisBonLivraisonId)) {
            $success = $this->bon_livraison_model->remove_colis_to_bon_livraison($colisBonLivraisonId);
            if ($success == true) {
                $type = 'success';
                $message = _l('deleted', _l('colis_delivery_note_lowercase'));
            } else {
                $message = _l('problem_deleting', _l('colis_delivery_note_lowercase'));
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
            redirect(point_relais_url('bons_livraison'));
        }

        //Récupération du bon de livraison
        $bonLivraison = $this->bon_livraison_model->get($id);
        //Récupération des colis du bon de livraison
        $bonLivraison->items = $this->bon_livraison_model->get_items_bon_livraison($id);
        //Vérification si le bon de livraison contient des colis
        if (count($bonLivraison->items) == 0) {
            set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
            redirect(point_relais_url('bons_livraison'));
        } else {
            //Get infos point relai
            $this->load->model('points_relais_model');
            $pointRelai = $this->points_relais_model->get($bonLivraison->point_relai_id);
            $bonLivraison->point_relai = $pointRelai;
            if ($bonLivraison->type == 1) {
                $bonLivraison->type = _l('output');
            } else if ($bonLivraison->type == 2) {
                $bonLivraison->type = _l('returned');
            }
            bon_livraison_pdf($bonLivraison);
        }
    }

    /**
     * Print etiquette
     */
    public function etiquette($id)
    {
        if (!is_numeric($id)) {
            redirect(point_relais_url('bons_livraison'));
        }

        //Récupération du bon de livraison
        $bonLivraison = $this->bon_livraison_model->get($id);
        //Récupération des colis du bon de livraison
        $bonLivraison->items = $this->bon_livraison_model->get_items_bon_livraison($id);
        //Vérification si le bon de livraison contient des colis
        if (count($bonLivraison->items) == 0) {
            set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
            redirect(point_relais_url('bons_livraison'));
        } else {
            //Vérification si le bon de livraison est confirmé
            if ($bonLivraison->status == 2) {
                etiquette_bon_livraison_pdf($bonLivraison);
            } else {
                set_alert('warning', _l('you_must_confirm_the_delivery_note_first'));
                redirect(point_relais_url('bons_livraison'));
            }
        }
    }

    /**
     * Init colis bon livraison
     */
    public function init_colis_bon_livraison()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolis.id',
                'code_barre',
                'tblexpediteurs.nom',
                'tblvilles.name',
                'crbt',
                'frais',
                'date_ramassage',
                'etat_id',
                'status_reel'
            );

            $where = array();
            if ($this->input->post('point_relai_id') && is_numeric($this->input->post('point_relai_id'))) {
                array_push($where, ' AND tblcolis.point_relai_id = ' . $this->input->post('point_relai_id'));
            }
            if ($this->input->post('bon_livraison_id') && is_numeric($this->input->post('bon_livraison_id'))) {
                array_push($where, 'AND tblcolis.id NOT IN (SELECT colis_id FROM tblcolisbonlivraison WHERE colis_id = tblcolis.id AND tblcolisbonlivraison.bonlivraison_id = ' . $this->input->post('bon_livraison_id') . ' )');
            }
            if ($this->input->post('type') && is_numeric($this->input->post('type')) && $this->input->post('type') == 1) {
                if (!empty(get_option('the_statuses_of_colis_displayed_in_the_delivery_note_output'))) {
                    array_push($where, 'AND tblcolis.status_reel IN (' . get_option('the_statuses_of_colis_displayed_in_the_delivery_note_output') . ')');
                } else {
                    array_push($where, 'AND tblcolis.status_reel NOT IN (2, 3, 12)');
                }
            } else if ($this->input->post('type') && is_numeric($this->input->post('type')) && $this->input->post('type') == 2) {
                if (!empty(get_option('the_statuses_of_colis_displayed_in_the_delivery_note_returned'))) {
                    array_push($where, 'AND tblcolis.status_reel IN (' . get_option('the_statuses_of_colis_displayed_in_the_delivery_note_returned') . ')');
                } else {
                    array_push($where, 'AND tblcolis.status_reel NOT IN (2, 3, 12)');
                }
            }

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville'
            );

            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, array());
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcolis.id') {
                        $_data = icon_btn('#', 'plus', 'btn-success colis_added', array('data-id' => $_data));
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<b id="column-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<b>' . ucwords($_data) . '</b>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt' || $aColumns[$i] == 'frais') {
                        $_data = number_format($_data, 2, ',', ' ');
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
     * Init historique colis bon livraison
     */
    public function init_historique_colis_bon_livraison()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolisbonlivraison.id as colisbonlivraison_id',
                'code_barre',
                'tblexpediteurs.nom',
                'tblvilles.name',
                'crbt',
                'frais',
                'date_ramassage',
                'etat_id',
                'status_reel'
            );

            $where = array();
            if ($this->input->post('bon_livraison_id') && is_numeric($this->input->post('bon_livraison_id'))) {
                array_push($where, ' AND tblcolisbonlivraison.bonlivraison_id = ' . $this->input->post('bon_livraison_id'));
            }

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tblcolisbonlivraison.colis_id',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville'
            );

            $sIndexColumn = "id";
            $sTable = 'tblcolisbonlivraison';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, array());
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
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
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-historique-barcode-' . $key, 'code_barre') . '<b id="column-historique-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<b>' . ucwords($_data) . '</b>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt' || $aColumns[$i] == 'frais') {
                        $_data = number_format($_data, 2, ',', ' ');
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }
}
