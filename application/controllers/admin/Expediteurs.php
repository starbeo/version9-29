<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expediteurs extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('expediteurs_model');

        if (get_permission_module('shipper') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all expediteurs
     */
    public function index()
    {
        $has_permission = has_permission('shipper', '', 'view');
        if (!has_permission('shipper', '', 'view') && !has_permission('shipper', '', 'view_own')) {
            access_denied('Shipper');
        }

        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblexpediteurs.id',
                'nom',
                'contact',
                'email',
                'telephone',
                'tblvilles.name',
                'tblexpediteurs.code_parrainage',
                'tblexpediteurs.affiliation_code',
                'tblexpediteurs.total_colis_parrainage',
                'tblexpediteurs.date_created',
                'tblexpediteurs.active'
            );

            $sIndexColumn = "id";
            $sTable = 'tblexpediteurs';

            $join = array('LEFT JOIN tblvilles ON tblvilles.id = tblexpediteurs.ville_id');

            $where = array();
            //If not admin show only own expediteurs
            if (!$has_permission) {
                array_push($where, 'AND tblexpediteurs.id_user = "' . get_staff_user_id() . '"');
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblexpediteurs.date_created LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblexpediteurs.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblexpediteurs.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where);
            $output = $result['output'];
            $rResult = $result['rResult'];

            $fraisParrainage = get_option('frais_parrainage');
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['tblexpediteurs.id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.total_colis_parrainage') {
                        $_data = '<p class="text-center">' . format_money($_data * $fraisParrainage) . '</p>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblexpediteurs.active') {
                        $checked = '';
                        if ($aRow['tblexpediteurs.active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['tblexpediteurs.id'] . '" data-switch-url="admin/expediteurs/change_expediteur_status" ' . $checked . '>';
                        // For exporting
                        $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                    }
                    $row[] = $_data;
                }

                $options = icon_btn('admin/expediteurs/expediteur/' . $aRow['tblexpediteurs.id'], 'pencil-square-o');
                $row[] = $options .= icon_btn('admin/expediteurs/delete/' . $aRow['tblexpediteurs.id'], 'remove', 'btn-danger btn-delete-confirm', array('data-toggle' => 'tooltip', 'title' => _l('expediteur_delete_tooltip')));

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('als_expediteur');
        $this->load->view('admin/expediteurs/manage', $data);
    }

    /**
     * Change expediteur status / active / inactive
     */
    public function change_expediteur_status($id, $status)
    {

        if (has_permission('shipper', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->expediteurs_model->change_expediteur_status($id, $status);
            }
        }
    }

    /**
     * Edit or add new expediteur
     */
    public function expediteur($id = '')
    {
        if (!has_permission('shipper', '', 'view') && !has_permission('shipper', '', 'view_own')) {
            access_denied('Shipper');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('shipper', '', 'create')) {
                    access_denied('Shipper');
                }
                $id = $this->expediteurs_model->add($this->input->post());
                if (is_numeric($id)) {
                    set_alert('success', _l('added_successfuly', _l('expediteur')));
                    redirect(admin_url('expediteurs/expediteur/' . $id));
                }
            } else {
                if (!has_permission('shipper', '', 'edit')) {
                    access_denied('Shipper');
                }
                $success = $this->expediteurs_model->update($this->input->post(), $id);
                if (is_array($success)) {
                    if ($success == true) {
                        set_alert('success', _l('updated_successfuly', _l('expediteur')));
                    }
                } else {
                    if ($success == true) {
                        set_alert('success', _l('updated_successfuly', _l('expediteur')));
                    }
                }
                redirect(admin_url('expediteurs/expediteur/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('expediteur_lowercase'));
            $data['class1'] = 'col-md-6';
        } else {
            $expediteur = $this->expediteurs_model->get($id, '');
            if (!$expediteur || (!has_permission('shipper', '', 'view') && ($expediteur->id_user != get_staff_user_id()))) {
                set_alert('warning', _l('not_found', _l('expediteur')));
                redirect(admin_url('expediteurs'));
            }

            //Get contract
            $this->load->model('contrats_model');
            $data['contrat'] = $this->contrats_model->get_contrat_by_client($id);

            $data['expediteur'] = $expediteur;
            $title = $expediteur->nom;
            $data['class1'] = 'col-md-4';
            $data['class2'] = 'col-md-8';
        }

        // Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();
        // Get groupes
        $this->load->model('groupes_model');
        $data['groupes'] = $this->groupes_model->get();
        // Get delivery mens
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        $data['staffs'] = $this->staff_model->get('', 1, 'staffid != 1 AND admin != 0');

        $data['title'] = $title;
        $this->load->view('admin/expediteurs/expediteur', $data);
    }

    /**
     * Delete department from database
     */
    public function delete($id)
    {
        if (!has_permission('shipper', '', 'delete')) {
            access_denied('Shipper');
        }

        $response = $this->expediteurs_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('expediteur_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('expediteur')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expediteur_lowercase')));
        }

        redirect(admin_url('expediteurs'));
    }

    /**
     * Init colis expediteur
     */
    public function init_colis_expediteur($expediteur_id)
    {
        $has_permission = has_permission('colis', '', 'view');
        if (!has_permission('colis', '', 'view') && !has_permission('colis', '', 'view_own')) {
            access_denied('Colis');
        }

        // ID entreprise 
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array();
            array_push($aColumns, 'tblcolis.id', 'tblcolis.code_barre', 'tblcolis.telephone', 'DATE_FORMAT(date_ramassage, "%d/%m/%Y")', 'status_id', 'etat_id');

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

            array_push($aColumns, 'tblvilles.name', 'tblcolis.crbt', 'tblcolis.frais');

            $join = array(
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur'
            );

            $where = array();
            array_push($where, 'AND id_expediteur="' . $expediteur_id . '"');
            if (is_livreur()) {
                $city = get_city_livreur();
                array_push($where, ' AND tblcolis.ville = ' . $city);
            }
            if (!$has_permission) {
                array_push($where, 'AND tblcolis.id_utilisateur = "' . get_staff_user_id() . '"');
            }

            if ($this->input->post('custom_view') && is_numeric($this->input->post('custom_view'))) {
                $view = $this->input->post('custom_view');
                $_where = '';
                if (is_numeric($view) && $view != 1 && $view != 2 && $view != 3) {
                    $_where .= ' AND tblcolis.status_reel = ' . $view;
                } else if (is_numeric($view) && ($view == 1 || $view == 2 || $view == 3)) {
                    $_where .= ' AND tblcolis.status_id = ' . $view;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            } else if ($this->input->post('etat')) {
                $etat = $this->input->post('etat');
                $_where = '';
                if (is_numeric($etat)) {
                    $_where = 'AND tblcolis.etat_id = ' . $etat;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            $i = 0;
            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.id_expediteur', 'tblcolis.livreur'));
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

                    if ($aColumns[$i] == 'tblcolis.code_barre') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_id') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_ramassage' || $aColumns[$i] == 'date_livraison') {
                        if ($_data != NULL) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcolis.frais' || $aColumns[$i] == 'tblcolis.crbt') {
                        $_data = '<p class="pright30" style="    text-align: right;">' . format_money($_data) . ' Dhs</p>';
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
     * List all colis en attente client
     */
    public function init_colis_en_attente_expediteur($clientid = false)
    {
        if (!has_permission('colis_en_attente', '', 'view')) {
            access_denied('Colis en attente');
        }

        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array();
            array_push($aColumns, 'tblcolisenattente.id', 'code_barre');
            if (!is_numeric($clientid)) {
                array_push($aColumns, 'tblexpediteurs.nom');
            }
            array_push($aColumns, 'tblcolisenattente.telephone', 'date_creation', 'status_id', 'tblvilles.name', 'tblcolisenattente.crbt');

            $sIndexColumn = "id";
            $sTable = 'tblcolisenattente';

            $join = array(
                'LEFT JOIN tblstatuscolis ON tblstatuscolis.id = tblcolisenattente.status_id',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolisenattente.ville',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolisenattente.id_expediteur'
            );

            $where = array();
            if (is_numeric($clientid)) {
                array_push($where, 'AND tblcolisenattente.id_expediteur = ' . $clientid);
            }

            if ($this->input->post('custom_view')) {
                $view = $this->input->post('custom_view');
                $_where = '';
                if (!empty($view)) {
                    if ($view == 'converted') {
                        $_where = 'AND tblcolisenattente.colis_id IS NOT NULL';
                    } else if ($view == 'not_converted') {
                        $_where = 'AND tblcolisenattente.colis_id IS NULL';
                    }
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolisenattente.id_expediteur', 'tblcolisenattente.colis_id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'code_barre') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'nom_complet') {
                        $_data = ucwords($_data);
                    } else if ($aColumns[$i] == 'status_id') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_creation') {
                        if ($_data != NULL) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
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
     * List all bons livraison client
     */
    public function init_bons_livraison_expediteur($clientid = false)
    {
        if (!has_permission('bon_livraison', '', 'view') && !has_permission('bon_livraison', '', 'view_own')) {
            access_denied('Bons Livraison');
        }

        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array();
            array_push($aColumns, 'tblbonlivraisoncustomer.id', 'tblbonlivraisoncustomer.nom', 'tblbonlivraisoncustomer.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblbonlivraisoncustomer';

            $join = array();

            $where = array();
            if (is_numeric($clientid)) {
                array_push($where, 'AND tblbonlivraisoncustomer.id_expediteur = ' . $clientid);
            }

            if ($this->input->post('custom_view')) {
                $view = $this->input->post('custom_view');
                $_where = '';

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array());
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tblbonlivraisoncustomer.nom') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblbonlivraisoncustomer.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('admin/bon_livraison/pdf_client/' . $aRow['tblbonlivraisoncustomer.id'], 'file-pdf-o', 'btn-success');
                $options .= icon_btn('admin/bon_livraison/etiquette_client/' . $aRow['tblbonlivraisoncustomer.id'], 'file-image-o', 'btn-info');
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * List all factures client
     */
    public function init_factures_expediteur($clientid = false)
    {
        $has_permission = has_permission('invoices', '', 'view');
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('Invoices');
        }

        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array();
            array_push($aColumns, 'tblfactures.id', 'tblfactures.nom', 'tblfactures.type', 'tblfactures.status', 'tblfactures.date_created', 'tblstaff.firstname'
            );

            $sIndexColumn = "id";
            $sTable = 'tblfactures';

            $join = array(
                'Left join tblstaff ON tblstaff.staffid = tblfactures.id_utilisateur ',
                'Left join tblstatuscolis ON tblstatuscolis.id = tblfactures.type ',
            );

            $where = array();
            if (!$has_permission) {
                array_push($where, 'AND tblfactures.id_utilisateur = "' . get_staff_user_id() . '"');
            }
            if (is_numeric($clientid)) {
                array_push($where, 'AND tblfactures.id_expediteur = ' . $clientid);
            }

            if ($this->input->post('custom_view')) {
                $view = $this->input->post('custom_view');
                $_where = '';
                if (is_numeric($view)) {
                    $_where = 'AND tblfactures.type = ' . $view;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblstaff.staffid', 'tblstaff.lastname'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tblfactures.nom') {
                        $_data = '<a href="' . admin_url('factures/facture/' . $aRow['tblfactures.id'] . '/' . $aRow['tblfactures.type']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblfactures.type') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblfactures.status') {
                        $_data = format_facture_status($_data);
                    } else if ($aColumns[$i] == 'tblfactures.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblstaff.firstname') {
                        $_data = staff_profile_image($aRow['staffid'], array('img mright5', 'img-responsive', 'staff-profile-image-small', 'pull-left')) . '<a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $_data . ' ' . $aRow['lastname'] . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = '';
                if (has_permission('invoices', '', 'edit')) {
                    $options .= icon_btn('admin/factures/facture/' . $aRow['tblfactures.id'] . '/' . $aRow['tblfactures.type'], 'pencil-square-o');
                }
                $options .= icon_btn('admin/factures/pdf/' . $aRow['tblfactures.id'], 'file-pdf-o', 'btn-success');
                if (has_permission('invoices', '', 'delete')) {
                    $options .= icon_btn('admin/factures/delete/' . $aRow['tblfactures.id'], 'remove', 'btn-danger btn-delete-confirm');
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * List all reclamtions client
     */
    public function init_reclamations_expediteur($clientid = false)
    {
        $has_permission = has_permission('claim_shipper', '', 'view');
        if (!has_permission('claim_shipper', '', 'view') && !has_permission('claim_shipper', '', 'view_own')) {
            access_denied('Réclamtions');
        }

        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblreclamations.id',
                'objet',
                'etat',
                'date_created',
                'tblstaff.firstname',
                'date_traitement'
            );

            $sIndexColumn = "id";
            $sTable = 'tblreclamations';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblreclamations.staff_id'
            );

            $where = array();
            if (!$has_permission) {
                array_push($where, 'AND tblreclamations.staffid = "' . get_staff_user_id() . '"');
            }
            if (is_numeric($clientid)) {
                array_push($where, 'AND tblreclamations.relation_id = ' . $clientid);
            }

            if ($this->input->post('custom_view')) {
                $view = $this->input->post('custom_view');
                $_where = '';
                if (is_numeric($view)) {
                    $_where = 'AND tblreclamations.etat = ' . $view;
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblstaff.lastname', 'tblstaff.staffid'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'etat') {
                        if ($aRow['etat'] == 0) {
                            $etat = 'Non Traité';
                            $_data = '<span class="label label-danger">' . $etat . '</span>';
                        } else if ($aRow['etat'] == 1) {
                            $etat = 'Traité';
                            $_data = '<span class="label label-success">' . $etat . '</span>';
                        }
                    } else if ($aColumns[$i] == 'date_created' || $aColumns[$i] == 'date_traitement') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblstaff.firstname') {
                        if (!is_null($_data)) {
                            $_data = staff_profile_image($aRow['staffid'], array('staff-profile-image-small mright5'));
                            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $aRow['tblstaff.firstname'] . ' ' . $aRow['lastname'] . '</a>';
                        }
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
     * List all reclamtions client 
     */
    public function init_activity_log_expediteur($clientid = false)
    {
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblactivitylogcustomer.id',
                'description',
                'date'
            );

            $sIndexColumn = "id";
            $sTable = 'tblactivitylogcustomer';

            $join = array();

            $where = array();
            if (is_numeric($clientid)) {
                array_push($where, 'AND tblactivitylogcustomer.clientid = ' . $clientid);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array());
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'date') {
                        $_data = _dt($_data);
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
     * Get all expediteurs
     */
    public function get_all_expediteurs()
    {
        echo json_encode($this->expediteurs_model->get());
    }

    /**
     * Get expediteur by id
     */
    public function get_expediteur_by_id($clientId, $villeId = '')
    {
        //Get Infos Client
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
     * Get ouverture colis
     */
    public function get_ouverture_colis($expediteur_id)
    {
        //Get Infos Client
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
     * Total fresh & crbt colis par defaut / ajax chart
     */
    public function default_fresh_crbt_colis_expediteur()
    {
        echo json_encode($this->expediteurs_model->default_fresh_crbt_colis_expediteur());
    }

    /**
     * Check if email already exists in list clients
     */
    public function client_email_exists()
    {
        // ID entreprise 
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $client_id = $this->input->post('client_id');
                if ($client_id !== 'undefined') {
                    $this->db->where('id', $client_id);
                    $_current_client = $this->db->get('tblexpediteurs')->row();
                    if ($_current_client && $_current_client->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }

                $this->db->where('email', $this->input->post('email'));
                $this->db->where('id_entreprise', $id_E);
                $total_rows = $this->db->count_all_results('tblexpediteurs');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
}
