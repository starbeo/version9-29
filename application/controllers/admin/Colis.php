<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis extends Admin_controller
{

    private $not_importable_colis_fields = array('id', 'etat_id', 'status_id', 'date_creation', 'commentaire', 'id_expediteur', 'colis_id', 'id_entreprise');

    function __construct()
    {
        parent::__construct();
        $this->load->model('colis_model');
        $this->load->model('demandes_model');
        if (get_permission_module('colis') == 0) {
           redirect(admin_url('home'));
        }
    }

    /**
     * List all colis
     */
    public function index($search = '')
    {
        $has_permission = has_permission('colis', '', 'view');
        if (!has_permission('colis', '', 'view') && !has_permission('colis', '', 'view_own')) {
            access_denied('Colis');
       }


        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblcolis.id', 'tblcolis.code_barre', 'tblcolis.num_commande', 'tblexpediteurs.nom');
            // Check if option show point relai is actived
            if (get_permission_module('points_relais') == 1) {
                array_push($aColumns, 'tblcolis.type_livraison');
            }
            array_push($aColumns, 'tblcolis.telephone', 'DATE_FORMAT(date_ramassage, "%d/%m/%Y")', 'status_reel', 'etat_id');

            $orderby = '';
            if ($this->input->post('custom_view')) {
                $view = $this->input->post('custom_view');
                if ($view == 11) {
                    array_push($aColumns, '(SELECT DATE_FORMAT(date_reporte, "%d/%m/%Y") FROM tblstatus WHERE tblstatus.code_barre = tblcolis.code_barre ORDER BY tblstatus.id DESC LIMIT 1) as date_reporte');
                    $orderby = 'ORDER BY date_reporte DESC';
                } else {
                    array_push($aColumns, 'date_livraison');
                }
            } else {
                array_push($aColumns, 'date_livraison');
            }

            array_push($aColumns, 'tblvilles.name', 'tblcolis.crbt');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';

            $join = array(
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur',
               'LEFT JOIN tblstatus ON tblstatus.code_barre = tblcolis.code_barre AND tblstatus.type = 11'

            );

            $where = array();
            if (is_livreur()) {
                $city = get_city_livreur();
                array_push($where, ' AND tblcolis.ville = ' . $city);
            }
            // Performance
            //array_push($where, ' AND tblcolis.date_ramassage > "2018-12-31"');
            // Filtre
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                array_push($where, ' AND tblcolis.type_livraison = "' . $this->input->post('f-type-livraison') . '"');
            }
            if ($this->input->post('f-livreur') && is_numeric($this->input->post('f-livreur'))) {
                array_push($where, ' AND tblcolis.livreur = ' . $this->input->post('f-livreur'));
            }
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                array_push($where, ' AND tblcolis.point_relai_id = ' . $this->input->post('f-point-relai'));
            }
            if ($this->input->post('f-clients') && is_numeric($this->input->post('f-clients'))) {
                array_push($where, ' AND tblcolis.id_expediteur = ' . $this->input->post('f-clients'));
            }
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                array_push($where, ' AND (tblcolis.status_id = ' . $this->input->post('f-statut') . ' OR tblcolis.status_reel = ' . $this->input->post('f-statut') . ')');
            }
            if ($this->input->post('f-etat') && is_numeric($this->input->post('f-etat'))) {
                array_push($where, ' AND tblcolis.etat_id = ' . $this->input->post('f-etat'));
            }
            if ($this->input->post('f-ville') && is_numeric($this->input->post('f-ville'))) {
                array_push($where, ' AND tblcolis.ville = ' . $this->input->post('f-ville'));
            }
            if ($this->input->post('f-date-ramassage-start') && is_date(to_sql_date($this->input->post('f-date-ramassage-start')))) {
                array_push($where, ' AND tblcolis.date_ramassage >= "' . to_sql_date($this->input->post('f-date-ramassage-start')) . '"');
            }
            if ($this->input->post('f-date-ramassage-end') && is_date(to_sql_date($this->input->post('f-date-ramassage-end')))) {
                array_push($where, ' AND tblcolis.date_ramassage <= "' . to_sql_date($this->input->post('f-date-ramassage-end')) . '"');
            }
            if ($this->input->post('f-date-livraison-start') && is_date(to_sql_date($this->input->post('f-date-livraison-start')))) {
                array_push($where, ' AND tblcolis.date_livraison >= "' . to_sql_date($this->input->post('f-date-livraison-start')) . '"');
            }
            if ($this->input->post('f-date-livraison-end') && is_date(to_sql_date($this->input->post('f-date-livraison-end')))) {
                array_push($where, ' AND tblcolis.date_livraison <= "' . to_sql_date($this->input->post('f-date-livraison-end')) . '"');
            }

            if ($this->input->post('bonlivraison')) {
                $bonlivraison = $this->input->post('bonlivraison');
                $_where = '';
                if ($bonlivraison == 'etat_paid_and_status_in_progress') {
                    $_where .= ' AND tblcolis.etat_id = 2 AND tblcolis.status_id = 1';
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            if ($this->input->post('custom_view') && is_numeric($this->input->post('custom_view'))) {
                $view = $this->input->post('custom_view');
                $_where = '';
                if (is_numeric($view)) {
                    $_where .= ' AND tblcolis.status_reel = ' . $view;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            } else {
                if ($this->input->get('status')) {
                    $status = $this->input->get('status');
                    if ($status == 1) {
                        array_push($where, ' AND tblcolis.status_reel != 9');
                    }
                    array_push($where, ' AND tblcolis.status_id = ' . $status);
                }
            }

            // Get
            if ($search && !empty($search)) {
                array_push($where, ' AND tblcolis.code_barre = "' . $search . '"');
            }
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        if ($this->input->get('status') && $this->input->get('status') == 1) {
                            array_push($where, ' AND tblcolis.date_ramassage = "' . date('Y-m-d') . '"');
                        } else if ($this->input->get('status') && ($this->input->get('status') == 2 || $this->input->get('status') == 3)) {
                            array_push($where, ' AND tblcolis.date_livraison = "' . date('Y-m-d') . '"');
                        }
                        break;
                    case 'week':
                        if ($this->input->get('status') && $this->input->get('status') == 1) {
                            array_push($where, ' AND WEEK(tblcolis.date_ramassage, 1) = WEEK(CURDATE(), 1)');
                        } else if ($this->input->get('status') && ($this->input->get('status') == 2 || $this->input->get('status') == 3)) {
                            array_push($where, ' AND WEEK(tblcolis.date_livraison, 1) = WEEK(CURDATE(), 1)');
                        }
                        break;
                    case 'month':
                        if ($this->input->get('status') && $this->input->get('status') == 1) {
                            array_push($where, ' AND tblcolis.date_ramassage > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        } else if ($this->input->get('status') && ($this->input->get('status') == 2 || $this->input->get('status') == 3)) {
                            array_push($where, ' AND tblcolis.date_livraison > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        }
                        break;
                }
            }

            if (!$has_permission) {
                array_push($where, 'AND tblcolis.id_utilisateur = "' . get_staff_user_id() . '"');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.anc_crbt', 'tblcolis.date_ramassage', 'tblcolis.date_retour', 'tblcolis.id_expediteur', 'tblcolis.livreur', 'tblcolis.num_facture','tblcolis.num_facture_re' ,'tblcolis.num_bonlivraison', 'tblcolis.num_etatcolislivrer', 'tblcolis.id_demande','tblstatus.date_reporte'), $orderby);
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcolis.code_barre') {
                        $checked = false;
                        if (!is_null($aRow['num_etatcolislivrer'])) {
                            $checked = true;
                        }
                        if (!is_null($aRow['num_facture'])) {
                            $checked = true;
                        }
                        if ($checked == false) {
                            $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<a id="column-barcode-' . $key . '" href="#" data-toggle="modal" data-target="#colis" data-id="' . $aRow['tblcolis.id'] . '" data-livreur="' . $aRow['livreur'] . '" data-expediteurid="' . $aRow['id_expediteur'] . '" >' . $_data . '</a>';
                        } else {
                            $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<b id="column-barcode-' . $key . '">' . $_data . '</b>';
                        }
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['id_expediteur']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'tblcolis.type_livraison') {
                        $_data = format_type_livraison_colis($_data);
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
                    } else if ($aColumns[$i] == 'date_livraison' || $aColumns[$i] == 'date_retour') {
                       if ($aRow['status_reel'] == 2) {
                            $statusColor='#259b24';
                            $strdata=  (string) $aRow['date_livraison'] ;
                            $_data = '<span class="label ' . $strdata. ' inline-block" style="background-color: ' . $statusColor . ' !important;">' .  $strdata  . '</span>';

                        } else if ($aRow['status_reel'] == 3) {
                            $statusColor = '#fc2d42';
                          $strdata=  (string) $aRow['date_retour'] ;
                            $_data = '<span class="label ' . $strdata. ' inline-block" style="background-color: ' . $statusColor . ' !important;">' .  $strdata  . '</span>';

                        } else if ($aRow['status_reel'] == 11) {
                            $statusColor = '#777777';
                            $strdata=  (string) $aRow['date_reporte'] ;
                            $_data = '<span class="label ' . $strdata. ' inline-block" style="background-color: ' . $statusColor . ' !important;">' .  $strdata  . '</span>';

                        } else {
                            $_data = NULL;
                        }
                        if (!is_null($_data)) {
                           // $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcolis.crbt') {
                        if ($aRow['anc_crbt'] > 0) {
                            $_data = $aRow['anc_crbt'];
                        }
                        $_data = '<p class="pright30" style="text-align: right;">' . format_money($_data) . '</p>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'eye', 'btn-primary mbot5', array('data-toggle' => 'modal', 'data-target' => '#historiques', 'data-coli-id' => $aRow['tblcolis.id'], 'data-barcode' => $aRow['tblcolis.code_barre']));
                if (is_admin()) {
                    $options .= '<a href="' . admin_url('colis/initialisation/' . $aRow['tblcolis.id']) . '" class="btn btn-success btn-icon mbot5 btn-initialisation-colis-confirm"><i class="fa fa-refresh"></i></a>';
                }
                $checked = false;
                if (!is_null($aRow['num_bonlivraison'])) {
                    $options .= '<a href="' . admin_url('bon_livraison/bon/' . $aRow['num_bonlivraison']) . '" class="btn btn-default btn-icon mbot5" target="_blank">BL-' . $aRow['num_bonlivraison'] . '</a>';
                }
                if (!is_null($aRow['num_etatcolislivrer'])) {
                    $checked = true;
                    $options .= '<a href="' . admin_url('etat_colis_livrer/etat/' . $aRow['num_etatcolislivrer']) . '" class="btn btn-info btn-icon mbot5" target="_blank">ECL-' . $aRow['num_etatcolislivrer'] . '</a>';
                }
                if (!is_null($aRow['num_facture'])) {
                    $checked = true;
                    $options .= '<a href="' . admin_url('factures/facture/' . $aRow['num_facture']) . '" class="btn btn-success btn-icon mbot5" target="_blank">FCT-' . $aRow['num_facture'] . '</a>';
                }
                if (!is_null($aRow['num_facture_re'])) {
                    $checked = true;
                    $options .= '<a href="' . admin_url('factures/facture/' . $aRow['num_facture_re']) . '" class="btn btn-danger btn-icon mbot5" target="_blank">FCT-' . $aRow['num_facture_re'] . '</a>';
                }

                if (!is_null($aRow['id_demande'])) {
                    $options .= '<a href="#" class="btn btn-primary btn-icon mbot5" data-toggle="modal" data-target="#demande" onclick="init_demande(' . $aRow['id_demande'] . ');return false;" data-id="'.$aRow['id_demande'].'" target="_blank">DMD-' . $aRow['id_demande'] . '</a>';
                }
                if ($checked == false) {
                    $options .= icon_btn('#', 'pencil-square-o', 'btn-default mbot5', array('data-toggle' => 'modal', 'data-target' => '#colis', 'data-id' => $aRow['tblcolis.id'], 'data-livreur' => $aRow['livreur'], 'data-expediteurid' => $aRow['id_expediteur']));
                    $options .= icon_btn('admin/colis/delete/' . $aRow['tblcolis.id'], 'remove', 'btn-danger mbot5 btn-delete-confirm');
                }
                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get Type Status
        $this->load->model('colis_model');
        $data['types'] = $this->colis_model->get_status_colis();
        //Get Locations
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->get();
        //Get Motif
        $this->load->model('status_model');
        $data['motifs'] = $this->status_model->get_motif_status();


        //Get livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        //Get clients
        $this->load->model('expediteurs_model');
        $data['expediteurs'] = $this->expediteurs_model->get();
        //Get quartiers
        $this->load->model('quartiers_model');
        $data['quartiers'] = $this->quartiers_model->get('');
        //Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();
        //Get statuses
        $this->load->model('statuses_colis_model');
        $data['statuses'] = $this->statuses_colis_model->get();
        //Get etats
        $this->load->model('etats_colis_model');
        $data['etats'] = $this->etats_colis_model->get();
        //Get types livraison
        $data['types_livraison'] = $this->colis_model->get_types_livraison();
        // Check if option show point relai is actived
        $data['points_relais'] = array();
        if (get_permission_module('points_relais') == 1) {
            // Get points relais
            $this->load->model('points_relais_model');
            $data['points_relais'] = $this->points_relais_model->get('', 1, array(), 'tblpointsrelais.id, CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as nom');
        }

        $data['title'] = _l('als_colis');
        $this->load->view('admin/colis/manage', $data);
    }

    /**
     * List all colis with search
     */
    public function search($search)
    {
        if(!empty($search)) {
            $this->index($search);
        } else {
            $this->index();
        }
    }

    /**
     * Initialisation colis
     */
    public function initialisation($colisId)
    {
        if (is_admin() && is_numeric($colisId)) {
            //check if colis exists in facture
            $existsColisInFacture = total_rows('tblcolisfacture', array('colis_id' => $colisId));
            if ($existsColisInFacture == 0) {
                //check if colis exists in list colis etat colis livrer
                $existsColisInEtat = total_rows('tbletatcolislivreitems', array('colis_id' => $colisId));
                if ($existsColisInEtat == 0) {
                    //Get colis by colis id
                    $colis = $this->colis_model->get($colisId);
                    if ($colis) {
                        $barcode = $colis->code_barre;
                        $crbt = $colis->crbt;
                        $frais = $colis->frais;
                        if ($colis->anc_crbt > 0) {
                            $crbt = $colis->anc_crbt;
                        }
   if ($colis->anc_frais > 0) {
                            $frais = $colis->anc_frais;
                        }


                        $idEntreprise = $colis->id_entreprise;
                        //Delete status by barcode
                        $this->colis_model->delete_all_status_colis($barcode);
                        //Remove colis of BL
                        $this->colis_model->remove_colis_of_bon_livraison($colisId, $barcode);
                        //Add default status "Ramassé"
                        $this->db->insert('tblstatus', array(
                            'code_barre' => $barcode,
                            'type' => 5,
                            'date_created' => date('Y-m-d H:i:s'),
                            'emplacement_id' => 9,
                            'id_utilisateur' => get_staff_user_id(),
                            'id_entreprise' => $idEntreprise
                        ));
                        //Update colis
                        $this->db->where('id', $colisId);
                        $this->db->update('tblcolis', array('etat_id' => 1, 'status_id' => 1, 'status_reel' => 5, 'crbt' => $crbt, 'anc_crbt' => 0, 'date_livraison' => NULL, 'num_etatcolislivrer' => NULL, 'num_bonlivraison' => NULL, 'num_facture' => NULL,'frais'=> $frais));
                    }
                } else {
                    set_alert('warning', _l('remove_the_order_from_the_etat_colis_livrer'));
                }
            } else {
                set_alert('warning', _l('remove_the_order_from_the_invoice'));
            }
        }

        redirect(admin_url('colis'));
    }

    /**
     * Edit or add new coli
     */
    public function coli($id = '')
    {
        $success = false;
        $type = 'warning';
        $message = '';
        if ($this->input->post()) {
            if ($this->input->post('id') == "") {
                if (!has_permission('colis', '', 'create')) {
                    $type = 'danger';
                    $message = _l('access_denied');
                } else {
                    $id = $this->colis_model->add($this->input->post());
                    $message = _l('problem_adding', _l('colis'));
                    if (is_numeric($id)) {
                        $success = true;
                        $type = 'success';
                        $message = _l('added_successfuly', _l('colis'));
                    }
                }
            } else {
                if (!has_permission('colis', '', 'edit')) {
                    $type = 'danger';
                    $message = _l('access_denied');
                } else {
                    $data = $this->input->post();
                    $id = $data['id'];
                    unset($data['id']);
                    $success = $this->colis_model->update($data, $id);
                    $message = _l('problem_updating', _l('colis'));
                    if ($success) {
                        $type = 'success';
                        $message = _l('updated_successfuly', _l('colis'));
                    }
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Delete coli
     */
    public function delete($id)
    {
        if (!has_permission('colis', '', 'delete')) {
            access_denied('Colis');
        }

        $response = $this->colis_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('colis_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('colis')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('colis_lowercase')));
        }

        redirect(admin_url('colis'));
    }

    /**
     * Get info coli
     */
    function get_info_colis($id)
    {
        echo json_encode($this->colis_model->get($id));
    }

    /**
     * List all status colis
     */
    public function list_status()
    {
    if (!is_admin()) {
        access_denied('Status Colis');
      }

        if ($this->input->is_ajax_request()) {
            $aColumns = array('tblstatuscolis.name', 'tblstatuscolis.color', 'tblstatuscolis.show_in_delivery_app');

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblstatuscolis';

            $join = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, array(), array('tblstatuscolis.id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblstatuscolis.color') {
                        $_data = '<i class="fa fa-bookmark fa-lg" style="color: ' . $_data . '"></i>';
                    } else if ($aColumns[$i] == 'tblstatuscolis.show_in_delivery_app') {
                        if ($_data == 1) {
                            $label = _l('yes');
                            $colorLabel = 'success';
                        } else {
                            $label = _l('no');
                            $colorLabel = 'danger';
                        }
                        $_data = '<span class="label label-' . $colorLabel . '">' . $label . '</span>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#status_colis_modal', 'data-id' => $aRow['id'], 'data-color' => $aRow['tblstatuscolis.color'], 'data-show-in-delivery-app' => $aRow['tblstatuscolis.show_in_delivery_app']));
                $row[] = $options .= icon_btn('admin/colis/delete_status_colis/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('status_colis');
        $this->load->view('admin/colis/manage_status_colis', $data);
    }

    /**
     * Edit or add new status coli
     */
    public function status()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->colis_model->add_status_colis($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfuly', _l('status_colis'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            } else {
                $success = $this->colis_model->update_status_colis($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfuly', _l('status_colis'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            }
        }
    }

    /**
     * Delete status coli
     */
    public function delete_status_colis($id)
    {
        if (!is_admin()) {
            access_denied('Status Colis');
        }
        if (!$id) {
            redirect(admin_url('colis/list_status'));
        }

        $response = $this->colis_model->delete_status_colis($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('status_colis_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('status_colis')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('status_colis_lowercase')));
        }

        redirect(admin_url('colis/list_status'));
    }

    /**
     * List all states coli
     */
    public function list_states()
    {
        if (!is_admin()) {
            access_denied('States Colis');
        }

        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('name');

            $sIndexColumn = "id";
            $sTable = 'tbletatcolis';

            $join = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, array(), array('id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#states_colis_modal', 'data-id' => $aRow['id']));
                $row[] = $options .= icon_btn('admin/colis/delete_states_colis/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('states_colis');
        $this->load->view('admin/colis/manage_states_colis', $data);
    }

    /**
     * Edit or add new state coli
     */
    public function states()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->colis_model->add_states_colis($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfuly', _l('states_colis'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            } else {
                $success = $this->colis_model->update_states_colis($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfuly', _l('states_colis'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            }
        }
    }

    /**
     * Delete state coli
     */
    public function delete_states_colis($id)
    {
        if (!is_admin()) {
            access_denied('States Colis');
        }
        if (!$id) {
            redirect(admin_url('colis/list_states'));
        }

        $response = $this->colis_model->delete_states_colis($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('states_colis_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('states_colis')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('states_colis_lowercase')));
        }

        redirect(admin_url('colis/list_states'));
    }

    /**
     * Check if numero commande already exists for this colis
     */
    public function check_num_commande_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the refernce is the same
                $colisId = $this->input->post('colis_id');
                if ($colisId != 'undefined') {
                    $this->db->where('id', $colisId);
                    $currentColisEnAttente = $this->db->get('tblcolisenattente')->row();
                    if ($currentColisEnAttente && $currentColisEnAttente->num_commande == $this->input->post('num_commande')) {
                        echo json_encode(true);
                        die();
                    }
                }

                $numCommandeExistsInTableColis = total_rows('tblcolis', array('num_commande' => $this->input->post('num_commande')));
                $numCommandeExistsInTableColisEnAttente = total_rows('tblcolisenattente', array('num_commande' => $this->input->post('num_commande')));
                if ($numCommandeExistsInTableColisEnAttente > 0 || $numCommandeExistsInTableColis > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * Check if telephone has +212
     */
    public function check_telephone()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $telephone = $this->input->post('telephone');
                if (strlen($telephone) == 10) {
                    if (!preg_match("/^[0-9]{10}$/", $telephone)) {
                        echo json_encode(false);
                    } else {
                        echo json_encode(true);
                    }
                } else {
                    echo json_encode(false);
                }
                die();
            }
        }
    }

    /**
     * Export colis
     */
    public function export()
    {
        ini_set('memory_limit', '1024M');
        //Get all colis
        $colis = $this->colis_model->get_colis_export();
        $columnHeader = "CODE ENVOI" . "\t" . "CLIENTS" . "\t" . "LIVREUR" . "\t" . "TELEPHONE" . "\t" . "CRBT" . "\t" . "STATUS" . "\t" . "VILLE" . "\t" . "DATE RAMASSAGE" . "\t" . "DATE LIVRAISON" . "\t" . "FRAIS" . "\t" . "ETAT COLIS" . "\t" . "BonLivraison" . "\t";
        
        $setData = '';
        foreach ($colis as $key => $c) {
            $rowData = '';
            foreach ($c as $key => $value) {
                $value = mb_convert_encoding($value, 'utf-16LE', 'utf-8');
                $rowData .= '"' . $value . '"' . "\t";
            }
            $setData .= trim($rowData) . "\n";
        }
        header('Content-type: text/html; charset=UTF-8');
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=MyColis-export" . date("-d-m-Y") . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo ucwords($columnHeader) . "\n" . $setData . "\n";
    }

    /**
     * Export colis facturé
     */
    public function export_colis_facture()
    {
        //Get all colis
        $colis = $this->colis_model->get_colis_facture_export();

        $columnHeader = "CODE ENVOI" . "\t" . "CLIENTS" . "\t" . "VILLE" . "\t" . "FACTURE" . "\t" . "ETAT COLIS" . "\t" . "STATUT COLIS" . "\t" . "STATUT FACTURE" . "\t" . "LIVREUR" . "\t" . "DATE RAMASSAGE" . "\t" . "DATE LIVRAISON" . "\t" . "FRAIS" . "\t" . "CRBT" . "\t";

        $setData = '';
        foreach ($colis as $key => $c) {
            $rowData = '';
            foreach ($c as $key => $value) {
                $value = mb_convert_encoding($value, 'utf-16LE', 'utf-8');
                $rowData .= '"' . $value . '"' . "\t";
            }
            $setData .= trim($rowData) . "\n";
        }
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=MyColis-export" . date("-d-m-Y") . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo ucwords($columnHeader) . "\n" . $setData . "\n";
    }

    /**
     * Export colis by filter
     */
    public function export_by_filter()
    {
        if ($this->input->post()) {
            // Filtre
            $where = ' 1 = 1 ';
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                $where .= ' AND tblcolis.type_livraison = "' . $this->input->post('f-type-livraison') . '"';
            }
            if ($this->input->post('f-livreur') && is_numeric($this->input->post('f-livreur'))) {
                $where .= ' AND tblcolis.livreur = ' . $this->input->post('f-livreur');
            }
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                $where .= ' AND tblcolis.point_relai_id = ' . $this->input->post('f-point-relai');
            }
            if ($this->input->post('f-clients') && is_numeric($this->input->post('f-clients'))) {
                $where .= ' AND tblcolis.id_expediteur = ' . $this->input->post('f-clients');
            }
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                $where .= ' AND (tblcolis.status_id = ' . $this->input->post('f-statut') . ' OR tblcolis.status_reel = ' . $this->input->post('f-statut') . ')';
            }
            if ($this->input->post('f-etat') && is_numeric($this->input->post('f-etat'))) {
                $where .= ' AND tblcolis.etat_id = ' . $this->input->post('f-etat');
            }
            if ($this->input->post('f-ville') && is_numeric($this->input->post('f-ville'))) {
                $where .= ' AND tblcolis.ville = ' . $this->input->post('f-ville');
            }
            if ($this->input->post('f-date-ramassage-start') && is_date(to_sql_date($this->input->post('f-date-ramassage-start')))) {
                $where .= ' AND tblcolis.date_ramassage >= "' . to_sql_date($this->input->post('f-date-ramassage-start')) . '"';
            }
            if ($this->input->post('f-date-ramassage-end') && is_date(to_sql_date($this->input->post('f-date-ramassage-end')))) {
                $where .= ' AND tblcolis.date_ramassage <= "' . to_sql_date($this->input->post('f-date-ramassage-end')) . '"';
            }
            if ($this->input->post('f-date-livraison-start') && is_date(to_sql_date($this->input->post('f-date-livraison-start')))) {
                $where .= ' AND tblcolis.date_livraison >= "' . to_sql_date($this->input->post('f-date-livraison-start')) . '"';
            }
            if ($this->input->post('f-date-livraison-end') && is_date(to_sql_date($this->input->post('f-date-livraison-end')))) {
                $where .= ' AND tblcolis.date_livraison <= "' . to_sql_date($this->input->post('f-date-livraison-end')) . '"';
            }
            $colisFacturer = false;
            if ($this->input->post('colis-facturer') && is_numeric($this->input->post('colis-facturer'))) {
                $colisFacturer = true;
                $where .= ' AND tblcolis.num_facture IS NOT NULL';
            }
            //Get list colis
            $colis = $this->colis_model->export_colis($where, $colisFacturer);
            if (count($colis) > 0) {
                //Generate excel
                $filename = 'Liste Colis ' . date(get_current_date_format(), strtotime(date('Y-m-d')));
                export_colis_excel($filename, $colis, $colisFacturer);
            } else {
                set_alert('warning', _l('empty_result'));
            }
        }

        redirect(admin_url('colis'));
    }

    /**
     * Total colis par defaut / ajax chart
     */
    public function default_total_colis()
    {
        echo json_encode($this->colis_model->default_total_colis());
    }

    /**
     * Total fresh & crbt colis par defaut / ajax chart
     */
    public function default_fresh_crbt_colis()
    {
        echo json_encode($this->colis_model->default_fresh_crbt_colis());
    }

    /**
     * Import
     */
    public function import()
    {

        if (!has_permission('import_colis', '', 'create')) {
            access_denied('colis');
        }

        require_once(APPPATH . 'third_party/Excel_reader/php-excel-reader/excel_reader2.php');
        require_once(APPPATH . 'third_party/Excel_reader/SpreadsheetReader.php');

        //Load model
        $this->load->model('expediteurs_model');
        //Variable declaration
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $errors = array();
        $data['btnimport'] = false;
        $total_imported = 0;
        if ($this->input->post()) {
            if (isset($_FILES['file_xls']['name']) && $_FILES['file_xls']['name'] != '') {
                // Get the temp file path
                $tmpFilePath = $_FILES['file_xls']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $newFilePath = TEMP_FOLDER . $_FILES['file_xls']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        //Get rows sheet excel
                        try {
                            $data_xls = new SpreadsheetReader($newFilePath);
                        } catch (Exception $e) {
                            die('Erreur lors du chargement du fichier "' . pathinfo($newFilePath, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                        };

                        $totalSheet = count($data_xls->sheets());

                        $rows = array();
                        // For Loop for all sheets 
                        for ($i = 0; $i < $totalSheet; $i++) {
                            $data_xls->ChangeSheet($i);
                            $cpt = 0;
                            $nbr = 0;
                            foreach ($data_xls as $Row) {
                                if ($cpt !== 0) {
                                    $destinataire = isset($Row[0]) ? $Row[0] : '';
                                    $adresse = isset($Row[1]) ? $Row[1] : '';
                                    $telephone = isset($Row[2]) ? $Row[2] : '';
                                    $ville = isset($Row[3]) ? $Row[3] : '';
                                    $crbt = isset($Row[4]) ? $Row[4] : '';
                                    $num_commande = isset($Row[5]) ? $Row[5] : '';

                                    $rows[$nbr][0] = $destinataire;
                                    $rows[$nbr][1] = $adresse;
                                    $rows[$nbr][2] = $telephone;
                                    $rows[$nbr][3] = $ville;
                                    $rows[$nbr][4] = $crbt;
                                    $rows[$nbr][5] = $num_commande;
                                }
                                $cpt++;
                                $nbr++;
                            }
                        }

                        if (count($rows) < 1) {
                            set_alert("warning", "Pas assez de lignes pour l'importation");
                            redirect(admin_url('colis/import'));
                        }

                        $db_temp_fields = array('nom_complet', 'adresse', 'telephone', 'ville', 'crbt', 'num_commande');
                        $db_fields = array();
                        foreach ($db_temp_fields as $field) {
                            if (in_array($field, $this->not_importable_colis_fields)) {
                                continue;
                            }
                            $db_fields[] = $field;
                        }

                        //Variable declaration
                        $cpt = 0;
                        $erreur_global = false;
                        $alias = get_option('alias_barcode');
                        $clientid = $this->input->post('clientid');
                        //Get client
                        $client = $this->expediteurs_model->get($clientid);
                        //Check if ouverture colis client is active
                        $ouvertureColis = 0;
                        if ($client && is_numeric($client->ouverture) && $client->ouverture == 1) {
                            $ouvertureColis = $client->ouverture;
                        }
                        foreach ($rows as $key => $row) {
                            //Do for db fields
                            $insert = array();
                            for ($i = 0; $i < count($db_fields); $i++) {
                                $erreur = false;

                                if (!isset($row[$i])) {
                                    continue;
                                }

                                if ($row[$i] !== '') {
                                    if ($db_fields[$i] == 'num_commande') {
                                        $numCommandeExistsInTableColis = total_rows('tblcolis', array('num_commande' => $row[$i], 'id_entreprise' => $id_E));
                                        $numCommandeExistsInTableColisEnAttente = total_rows('tblcolisenattente', array('num_commande' => $row[$i], 'id_entreprise' => $id_E));
                                        //Dont insert duplicate num commande
                                        if ($numCommandeExistsInTableColis > 0 || $numCommandeExistsInTableColisEnAttente > 0) {
                                            $erreur = true;
                                        }
                                    }

                                    if ($db_fields[$i] == 'telephone') {
                                        $row[$i] = correctionPhoneNumber($row[$i]);
                                        if (strlen($row[$i]) == 10) {
                                            if (!preg_match("/^[0-9]{10}$/", $row[$i])) {
                                                $erreur = true;
                                            }
                                        } else {
                                            $erreur = true;
                                        }
                                    }

                                    if ($db_fields[$i] == 'crbt') {
                                        if (!is_numeric($row[$i])) {
                                            $erreur = true;
                                        }
                                    }

                                    if ($db_fields[$i] == 'ville') {
                                        $this->db->where('id_entreprise', $id_E);
                                        $this->db->where('name', $row[$i]);
                                        $ville = $this->db->get('tblvilles')->row();
                                        if (is_null($ville)) {
                                            $erreur = true;
                                        } else {
                                            $row[$i] = $ville->id;
                                        }
                                    }

                                    $insert[$db_fields[$i]] = $row[$i];
                                } else {
                                    $erreur = true;
                                    $row[$i] = 'Colonne vide';
                                }

                                if ($erreur == true) {
                                    if (!isset($errors[$cpt]['ligne'])) {
                                        $errors[$cpt]['ligne'] = $key + 1;
                                    }
                                    if ($db_fields[$i] == 'nom_complet') {
                                        $errors[$cpt]['name'] = $row[$i];
                                    }
                                    if ($db_fields[$i] == 'adresse') {
                                        $errors[$cpt]['address'] = $row[$i];
                                    }
                                    if ($db_fields[$i] == 'telephone') {
                                        $errors[$cpt]['phone'] = $row[$i];
                                    }
                                    if ($db_fields[$i] == 'ville') {
                                        $errors[$cpt]['ville'] = $row[$i];
                                    }
                                    if ($db_fields[$i] == 'crbt') {
                                        $errors[$cpt]['crbt'] = $row[$i];
                                    }
                                    if ($db_fields[$i] == 'num_commande') {
                                        $errors[$cpt]['num_commande'] = $row[$i];
                                    }
                                    $erreur_global = true;
                                }
                            }

                            if ($erreur_global == true) {
                                $cpt++;
                            }

                            if ($this->input->post('import')) {
                                if (count($insert) > 0 && count($errors) == 0) {
                                    $total_imported++;
                                    //Data
                                    if (isset($insert['num_commande']) && !empty($insert['num_commande']) && _startsWith(strtoupper($insert['num_commande']), 'TA') && endsWith(strtoupper($insert['num_commande']), 'MA')) {
                                        $insert['code_barre'] = $insert['num_commande'];
                                    } else {
                                        $insert['code_barre'] = $alias . $clientid . 'MA' . get_nbr_coli_by_expediteur($clientid);
                                    }
                                    $insert['id_entreprise'] = $id_E;
                                    $insert['date_creation'] = date('Y-m-d');
                                    $insert['etat_id'] = 1;
                                    $insert['status_id'] = 12;
                                    $insert['id_expediteur'] = $clientid;
                                    $insert['ouverture'] = $ouvertureColis;
                                    $insert['importer'] = 1;

                                    $this->db->insert('tblcolisenattente', $insert);
                                }
                                $import_result = true;
                            }

                            if (count($errors) == 0) {
                                $data['btnimport'] = true;
                            }
                        }
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }

        $data['errors'] = $errors;
        if (isset($import_result) && $import_result == true) {
            //Delete File
            unlink($newFilePath);
            set_alert('success', _l('import_total_imported', $total_imported));
        }

        if (isset($clientid)) {
            $data['clientid'] = $clientid;
        }

        $data['clients'] = $this->expediteurs_model->get();
        $data['not_importable'] = $this->not_importable_colis_fields;
        $data['colis_db_fields'] = array('nom_complet', 'adresse', 'telephone', 'ville', 'crbt', 'num_commande');
        $data['title'] = _l('import');
        $this->load->view('admin/colis/import', $data);
    }

    /**
     * List all bons de livraison colis
     */
    public function historiques_bons_livraison()
    {
        $has_permission = has_permission('bon_livraison', '', 'view');
        if (!has_permission('bon_livraison', '', 'view') && !has_permission('bon_livraison', '', 'view_own')) {
            access_denied('Bon livraison');
        }

        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array();
            array_push($aColumns, 'tblbonlivraison.id', 'tblbonlivraison.nom', 'tblbonlivraison.type', 'tblbonlivraison.commentaire', 'a.firstname as livreur_firstname', 'tblbonlivraison.date_created', 'b.lastname as user_lastname');

            $sIndexColumn = "id";
            $sTable = 'tblbonlivraison';

            $join = array(
                'LEFT JOIN tblstaff as a ON a.staffid = tblbonlivraison.id_livreur',
                'LEFT JOIN tblstaff as b ON b.staffid = tblbonlivraison.id_utilisateur',
                'LEFT JOIN tblcolisbonlivraison ON tblcolisbonlivraison.bonlivraison_id = tblbonlivraison.id'
            );

            $where = array();
            if (!$has_permission) {
                array_push($where, 'AND tblbonlivraison.id_utilisateur = ' . get_staff_user_id());
            }

            //Filtre
            if ($this->input->post('f-coli-id') && is_numeric($this->input->post('f-coli-id'))) {
                array_push($where, ' AND tblcolisbonlivraison.colis_id = ' . $this->input->post('f-coli-id'));
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('a.staffid as livreur_staffid', 'a.lastname as livreur_lastname', 'b.staffid as user_staffid', 'b.firstname as user_firstname'), '', '', '', false);
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblbonlivraison.nom') {
                        $_data = '<a href="' . admin_url('bon_livraison/bon/' . $aRow['tblbonlivraison.id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.type') {
                        if ($_data == 1) {
                            $_data = '<span class="label label-info">' . _l('delivery_note_type_output') . '</span>';
                        } else if ($_data == 2) {
                            $_data = '<span class="label label-danger">' . _l('delivery_note_type_returned') . '</span>';
                        }
                    } else if ($aColumns[$i] == 'tblbonlivraison.commentaire') {
                        $_data = '<p style="text-align: center;">' . total_rows('tblcolisbonlivraison', array('bonlivraison_id' => $aRow['tblbonlivraison.id'])) . '</p>';
                    } else if ($aColumns[$i] == 'a.firstname as livreur_firstname') {
                        $firstname = $_data;
                        $_data = staff_profile_image($aRow['livreur_staffid'], array('staff-profile-image-small'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $aRow['livreur_staffid']) . '">' . $firstname . ' ' . $aRow['livreur_lastname'] . '</a>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.date_created') {
                        $_data = date('d/m/Y', strtotime($_data));
                    } else if ($aColumns[$i] == 'b.lastname as user_lastname') {
                        $lastname = $_data;
                        $_data = staff_profile_image($aRow['user_staffid'], array('staff-profile-image-small'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $aRow['user_staffid']) . '">' . $aRow['user_firstname'] . ' ' . $lastname . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = icon_btn('admin/bon_livraison/bon/' . $aRow['tblbonlivraison.id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Bon Livraison'));
                $options .= icon_btn('admin/bon_livraison/pdf/' . $aRow['tblbonlivraison.id'], 'file-pdf-o', 'btn-success', array('title' => 'Imprimer PDF Bon Livraison'));
                $options .= icon_btn('admin/bon_livraison/etiquette/' . $aRow['tblbonlivraison.id'], 'file-image-o', 'btn-info', array('title' => 'Imprimer Etiquette'));

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }






    public function historiques_coli_info()
    {
        $has_permission = has_permission('bon_livraison', '', 'view');
        if (!has_permission('bon_livraison', '', 'view') && !has_permission('bon_livraison', '', 'view_own')) {
            access_denied('Bon livraison');
        }
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array();
            array_push($aColumns, 'tblcolis.id', 'tblcolis.code_barre', 'tblcolis.num_commande', 'tblexpediteurs.nom','tblcolis.telephone','DATE_FORMAT(date_ramassage, "%d/%m/%Y")', 'status_reel', 'etat_id','tblcolis.ville', 'crbt','tblvilles.name');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $join = array(
            'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur'
            );
            $where = array();

            //Filtre
            if ($this->input->post('f-coli-id') && is_numeric($this->input->post('f-coli-id'))) {
                array_push($where, ' AND tblcolis.id = ' . $this->input->post('f-coli-id'));
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.anc_crbt'), '', '', '', false);
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    }
                    else if ($aColumns[$i] == 'tblcolis.ville') {
                        $_data = $aRow['tblvilles.name'];
                    }

                    $row[] = $_data;
                }
                $options = '';

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }


    }



    }


