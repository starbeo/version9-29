<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etat_colis_livrer extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('etat_colis_livrer_model');

        if (get_permission_module('etat_colis_livrer') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all etat colis livrer
     */
    public function index($status = false, $etat = false)
    {
        $has_permission = has_permission('etat_colis_livrer', '', 'view');
        if (!has_permission('etat_colis_livrer', '', 'view') && !has_permission('etat_colis_livrer', '', 'view_own')) {
            access_denied('Bon livraison');
        }

        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tbletatcolislivre.id',
                'tbletatcolislivre.nom'
            );
            // Check if option show point relai is actived
            if (get_permission_module('points_relais') == 1) {
                array_push($aColumns, 'tbletatcolislivre.type_livraison');
            }
            array_push($aColumns, 'tbletatcolislivre.total', 'tbletatcolislivre.total_received', 'tbletatcolislivre.commision', 'tbletatcolislivre.manque', 'tbletatcolislivre.id_livreur','tbletatcolislivre.justif','tbletatcolislivre.id_entreprise', 'tbletatcolislivre.status', 'tbletatcolislivre.etat', 'CONCAT(a.firstname, " ", a.lastname) as fullname_livreur', 'tbletatcolislivre.facture_etl', 'CONCAT(b.firstname, " ", b.lastname) as fullname_staff');

            $sIndexColumn = "id";
            $sTable = 'tbletatcolislivre';

            $join = array(
                'LEFT JOIN tblstaff as a ON a.staffid = tbletatcolislivre.id_livreur',
                'LEFT JOIN tblstaff as b ON b.staffid = tbletatcolislivre.id_utilisateur',
                'LEFT JOIN tblstaff as c ON c.staffid = tbletatcolislivre.user_point_relais'
            );

            $where = array();
            if (!$has_permission) {
                array_push($where, 'AND tbletatcolislivre.id_utilisateur = "' . get_staff_user_id() . '"');
            }
            if (is_livreur()) {
                $livreur_id = get_staff_user_id();
                array_push($where, ' AND tbletatcolislivre.id_livreur = ' . $livreur_id);
            }

            //Filtre
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                array_push($where, ' AND tbletatcolislivre.type_livraison = "' . $this->input->post('f-type-livraison') . '"');
            }
            if ($this->input->post('f-livreur') && is_numeric($this->input->post('f-livreur'))) {
                array_push($where, ' AND tbletatcolislivre.id_livreur = ' . $this->input->post('f-livreur'));
            }
            if ($this->input->post('f-user-point-relais') && is_numeric($this->input->post('f-user-point-relais'))) {
                array_push($where, ' AND tbletatcolislivre.user_point_relais = ' . $this->input->post('f-user-point-relais'));
            }
            if ($this->input->post('f-utilisateur') && is_numeric($this->input->post('f-utilisateur'))) {
                array_push($where, ' AND tbletatcolislivre.id_utilisateur = ' . $this->input->post('f-utilisateur'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tbletatcolislivre.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }

            //Get
            if (is_numeric($status)) {
                array_push($where, 'AND tbletatcolislivre.status = ' . $status);
            }
            if (is_numeric($etat)) {
                array_push($where, 'AND tbletatcolislivre.etat = ' . $etat);
            }
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tbletatcolislivre.date_created LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tbletatcolislivre.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tbletatcolislivre.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            //Additional columns
            $additionalColumns = array(
                '(SELECT COUNT(id) FROM tbletatcolislivreitems WHERE etat_id = tbletatcolislivre.id) as nbr_colis',
                'tbletatcolislivre.id_utilisateur',
                '(SELECT COUNT(id) FROM tbllivreurversements WHERE etat_colis_livre_id = tbletatcolislivre.id) as nbr_versements',
                'tbletatcolislivre.type_livraison',
                'tbletatcolislivre.user_point_relais',
                'CONCAT(c.firstname, " ", c.lastname) as fullname_poit_relais',


            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, $additionalColumns);
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

                    if ($aColumns[$i] == 'tbletatcolislivre.id') {
                        $_data = '<div class="checkbox checkbox-primary"><input id="checkbox-etat-' . $_data . '" value="' . $_data . '" name="ids[]" class="checkbox-etat" type="checkbox" /><label></label></div>';
                    } else if ($aColumns[$i] == 'tbletatcolislivre.nom') {
                        $_data = render_btn_copy('column-name-etat-' . $key, 'name_of_etat_colis_livrer') . '<a id="column-name-etat-' . $key . '" href="' . admin_url('etat_colis_livrer/etat/' . $aRow['tbletatcolislivre.id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbletatcolislivre.type_livraison') {
                        $_data = format_type_livraison_colis($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.total_received' || $aColumns[$i] == 'tbletatcolislivre.total' || $aColumns[$i] == 'tbletatcolislivre.commision') {
                        $_data = '<p class="pright30" style="text-align: right;"><span class="label label-default inline-block">' . format_money($_data) . '</span></p>';
                    } else if ($aColumns[$i] == 'tbletatcolislivre.manque') {
                        $label = '';
                        $color = '';
                        if ($_data == 0) {
                            $label = 'label-success';
                            $color = '#449d44';
                        } elseif ($_data > 0) {
                            $label = 'label-info';
                            $color = '#03a9f4';
                        } elseif ($_data < 0) {
                            $label = 'label-danger';
                            $color = 'red';
                        }
                        $_data = '<p style="text-align: right;" class="label ' . $label . '">' . format_money($_data) . ' Dhs</p>';
                    } else if ($aColumns[$i] == 'tbletatcolislivre.id_livreur') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    } else if ($aColumns[$i] == 'tbletatcolislivre.status') {
                        $_data = format_status_etat_colis_livrer($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.etat') {
                        $_data = format_etat_etat_colis_livrer($_data);
                    } else if ($aColumns[$i] == 'fullname_livreur') {
                        if ($aRow['type_livraison'] == 'a_domicile') {
                            $_data = render_icon_motorcycle() . '<a href="' . admin_url('staff/member/' . $aRow['tbletatcolislivre.id_livreur']) . '" target="_blank">' . $_data . '</a>';
                        } else {
                            $_data = render_icon_university() . '<a href="' . admin_url('staff/member/' . $aRow['user_point_relais']) . '" target="_blank">' . $aRow['fullname_poit_relais'] . '</a>';
                        }
                    }


                    else if ($aColumns[$i] == 'tbletatcolislivre.facture_etl') {
                        if ($aRow['tbletatcolislivre.facture_etl'] !=0)
    
//$_data = "rrr";
   $_data =    '<a href="' . admin_url('commission_livreur/facture/' . $aRow['tbletatcolislivre.facture_etl']) . '" class="btn btn-success btn-icon mbot5" target="_blank">CML-' . $aRow['tbletatcolislivre.facture_etl'] . '</a>';
else
    $_data ='';

                    }

 else if ($aColumns[$i] == 'tbletatcolislivre.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'fullname_staff') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $aRow['id_utilisateur']) . '">' . $_data . '</a>';
                    }else if ($aColumns[$i] == 'tbletatcolislivre.justif') {
                        $dt  =  $aRow['tbletatcolislivre.id'];
                        $this->load->model('etat_colis_livrer_model');
                    $dor   =  $this->etat_colis_livrer_model->getcolisrefuse($dt,2);
                          $_data =$dor;
                    }else if ($aColumns[$i] == 'tbletatcolislivre.id_entreprise') {
                        $dt  =  $aRow['tbletatcolislivre.id'];
                        $this->load->model('etat_colis_livrer_model');
                        $dor   =  $this->etat_colis_livrer_model->getcolisrefuse($dt,9);
                        $_data =$dor;
                    }




                    $row[] = $_data;
                }




   

                $options = '';
                if (has_permission('etat_colis_livrer', '', 'edit')) {
                    $options .= icon_btn('admin/etat_colis_livrer/etat/' . $aRow['tbletatcolislivre.id'], 'pencil-square-o', 'btn-default mbot5', array('title' => 'Modifier Etat Colis Livrer'));
                }
                if (has_permission('etat_colis_livrer', '', 'download') && $aRow['nbr_colis'] > 0) {
                    $options .= icon_btn('admin/etat_colis_livrer/pdf/' . $aRow['tbletatcolislivre.id'], 'file-pdf-o', 'btn-danger mbot5', array('title' => 'Imprimer PDF Etat Colis Livrer'));
                    //$options .= icon_btn('admin/etat_colis_livrer/excel/' . $aRow['tbletatcolislivre.id'], 'file-excel-o', 'btn-success mbot5', array('title' => 'Imprimer Excel Etat Colis Livrer'));
                }
                if ((has_permission('versements', '', 'view') || has_permission('versements', '', 'view_own')) && $aRow['nbr_versements'] > 0) {
                    $options .= icon_btn('#', 'eye', 'btn-primary mbot5', array('data-toggle' => 'modal', 'data-target' => '#historique_versements', 'data-etat-colis-livrer-id' => $aRow['tbletatcolislivre.id'], 'title' => 'Voir Historique Versements'));
                }
                if (has_permission('etat_colis_livrer', '', 'delete')) {
                    $options .= icon_btn('admin/etat_colis_livrer/delete/' . $aRow['tbletatcolislivre.id'], 'remove', 'btn-danger btn-delete-confirm mbot5', array('title' => 'Supprimer Etat Colis Livrer'));
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        //Get staff
        $data['staff'] = $this->staff_model->get('', 1, 'staffid != 1');
        //Get types livraison
        $this->load->model('colis_model');
        $data['types_livraison'] = $this->colis_model->get_types_livraison();
        // Check if option show point relai is actived
        $data['point_relais_users'] = array();
        if (get_permission_module('points_relais') == 1) {
            // Get users point relais
            $data['point_relais_users'] = $this->staff_model->get('', '', array('admin' => 4));
        }

        $data['title'] = _l('etat_colis_livrer');
        $this->load->view('admin/etat_colis_livrer/manage', $data);
    }

    /**
     * Etat colis livrer non réglé
     */
    public function non_regle()
    {
        $this->index(false, 1);
    }

    /**
     * Etat colis livrer réglé
     */
    public function regle()
    {
        $this->index(false, 2);
    }

    /**
     * Etat colis livrer en attente
     */
    public function en_attente()
    {
        $this->index(1);
    }

    /**
     * Etat colis livrer valider
     */
    public function valider()
    {
        $this->index(2);
    }

    /**
     * Edit or add new etat
     */
    public function etat($id = '')
    {
        if (!has_permission('etat_colis_livrer', '', 'view') && !has_permission('etat_colis_livrer', '', 'view_own')) {
            access_denied('Bon livraison');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('etat_colis_livrer', '', 'create')) {
                    access_denied('Etat Colis Livrer');
                }
                $id = $this->etat_colis_livrer_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('etat_colis_livrer')));
                    redirect(admin_url('etat_colis_livrer/etat/' . $id));
                }
            } else {
                if (!has_permission('etat_colis_livrer', '', 'edit')) {
                    access_denied('Etat Colis Livrer');
                }
                $success = $this->etat_colis_livrer_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('etat_colis_livrer')));
                }
                redirect(admin_url('etat_colis_livrer/etat/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('etat_colis_livrer_lowercase'));
            $data['class1'] = 'col-md-6';
        } else {
            $etat = $this->etat_colis_livrer_model->get($id);
            if (!$etat || (!has_permission('etat_colis_livrer', '', 'view') && ($etat->id_utilisateur != get_staff_user_id()))) {
                set_alert('warning', _l('not_found', _l('etat_colis_livrer')));
                redirect(admin_url('etat_colis_livrer'));
            }

            $data['etat'] = $etat;
            $title = $etat->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        //Get Livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        //Get types livraison
        $this->load->model('colis_model');
        $data['types_livraison'] = $this->colis_model->get_types_livraison();
        // Check if option show point relai is actived
        $data['point_relais_users'] = array();
        if (get_permission_module('points_relais') == 1) {
            // Get users point relais
            $data['point_relais_users'] = $this->staff_model->get('', '', array('admin' => 4));
        }

        $data['title'] = $title;
        $this->load->view('admin/etat_colis_livrer/etat', $data);
    }

    /**
     * Change status
     */
    public function change_status($status = '')
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_updating', _l('status'));
        if ($this->input->post() && is_numeric($status)) {
            if (!has_permission('etat_colis_livrer', '', 'edit')) {
                $type = 'danger';
                $message = _l('access_denied');
            } else {
                $ids = $this->input->post('ids');
                foreach ($ids as $id) {
                    if (is_numeric($id)) {
                        if ($status == 1) {
                            $etatEtatColisLivrer = 1;
                            $etatColis = 1;
                        } else {
                            $etatEtatColisLivrer = 2;
                            $etatColis = 2;
                        }
                        $result = $this->etat_colis_livrer_model->change_status($id, $etatEtatColisLivrer, $etatColis);
                        if ($result) {
                            $success = true;
                            $type = 'success';
                            $message = _l('updated_successfuly', _l('status'));
                        }
                    }
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Change etat
     */
    public function change_etat($etat = '')
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_updating', _l('state'));
        if ($this->input->post() && is_numeric($etat)) {
            if (!has_permission('etat_colis_livrer', '', 'edit')) {
                $type = 'danger';
                $message = _l('access_denied');
            } else {
                $ids = $this->input->post('ids');
                foreach ($ids as $id) {
                    if (is_numeric($id)) {
                        $result = $this->etat_colis_livrer_model->change_etat($id, $etat);
                        if ($result) {
                            $success = true;
                            $type = 'success';
                            $message = _l('updated_successfuly', _l('state'));
                        }
                    }
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Change etat
     */
    public function validate_etat($etatId = '')
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_validate', _l('etat_colis_livrer'));
        if (is_numeric($etatId)) {
            if (!has_permission('etat_colis_livrer', '', 'edit')) {
                $type = 'danger';
                $message = _l('access_denied');
            } else {
                $result = $this->etat_colis_livrer_model->change_etat($etatId, 2, 2);
                if ($result) {
                    $success = true;
                    $type = 'success';
                    $message = _l('validate_successfuly', _l('etat_colis_livrer'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Print PDF etat colis livrer
     */
    public function pdf($id)
    {
        if (!has_permission('etat_colis_livrer', '', 'download')) {
            access_denied('Etat Colis Livrer');
        }
        if (!$id) {
            redirect(admin_url('etat_colis_livrer'));
        }

        //Get etat colis livrer
        $etatColisLivrer = $this->etat_colis_livrer_model->get($id);
        $etatColisLivrer->items = $this->etat_colis_livrer_model->get_items_etat_colis_livrer($id);
        if (count($etatColisLivrer->items) == 0) {
            set_alert('warning', _l('etat_colis_livrer_does_not_contain_any_colis'));
            redirect(admin_url('etat_colis_livrer'));
        } else {
            $this->load->model('staff_model');
            if ($etatColisLivrer->type_livraison == 'a_domicile') {
                //Get infos livreur
                $livreur = $this->staff_model->get($etatColisLivrer->id_livreur);
                $etatColisLivrer->livreur = $livreur;
            } else {
                //Get infos user point relais
                $point_relai = $this->staff_model->get($etatColisLivrer->user_point_relais);
                $etatColisLivrer->point_relai = $point_relai;
            }
            etat_colis_livrer_pdf($etatColisLivrer);
        }
    }

    /**
     * Print Excel etat colis livrer
     */
    public function excel($id)
    {
        if (!has_permission('etat_colis_livrer', '', 'download')) {
            access_denied('Etat Colis Livrer');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('etat_colis_livrer'));
        }

        $etat = $this->etat_colis_livrer_model->get($id);
        $this->load->model('staff_model');
        $livreur = $this->staff_model->get($etat->id_livreur);
        $etat->items = $this->etat_colis_livrer_model->get_items_etat_colis_livrer($id);
        if (count($etat->items) == 0) {
            set_alert('warning', _l('etat_colis_livrer_does_not_contain_any_colis'));
            redirect(admin_url('etat_colis_livrer'));
        } else {
            $etat->nom_livreur = $livreur->firstname . ' ' . $livreur->lastname;
            etat_colis_livrer_excel($etat);
        }
    }

    /**
     * Add colis to etat colis livrer
     */
    public function add_colis_to_etat_colis_livrer()
    {
        if (!has_permission('etat_colis_livrer', '', 'create')) {
            echo json_encode(array('success' => false, 'type' => 'danger', 'message' => _l('access_denied')));
        } else {
            $colis_id = $this->input->post('colis_id');
            $etat_id = $this->input->post('etat_id');
            if (is_numeric($colis_id) && is_numeric($etat_id)) {
                $success = $this->etat_colis_livrer_model->add_colis_to_etat_colis_livrer($etat_id, $colis_id);
                if (is_array($success)) {
                    $message = _l('added_successfuly', _l('colis_to_etat_colis_livrer'));
                    echo json_encode(array('success' => true, 'type' => 'success', 'message' => $message, 'total' => $success['total'], 'commision' => $success['commision'],'refuseCommision'=>$success['refuse_commision']));
                }
            }
        }
    }

    /**
     * Remove colis to etat colis livrer
     */
    public function remove_colis_to_etat_colis_livrer()
    {
        if (!has_permission('etat_colis_livrer', '', 'edit')) {
            echo json_encode(array('success' => false, 'type' => 'danger', 'message' => _l('access_denied')));
        } else {
            $id = $this->input->post('colis_etat_colis_livrer_id');
            if (is_numeric($id)) {
                $success = $this->etat_colis_livrer_model->remove_colis_to_etat_colis_livrer($id);
                if (is_array($success)) {
                    $message = _l('deleted', _l('colis_to_etat_colis_livrer'));
                    echo json_encode(array('success' => true, 'type' => 'success', 'message' => $message, 'total' => $success['total'], 'commision' => $success['commision']));
                }
            }
        }
    }

    /**
     * Delete etat colis livrer from database
     */
    public function delete($id)
    {
        if (!has_permission('etat_colis_livrer', '', 'delete')) {
            access_denied('Etat Colis Livrer');
        }

        $response = $this->etat_colis_livrer_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('delivery_note')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('delivery_note_lowercase')));
        }

        redirect(admin_url('etat_colis_livrer'));
    }

    /**
     * Init items etat colis livrer
     */
    public function init_items_etat_colis_livrer()
    {
        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblcolis.id',
                'code_barre',
                'tblexpediteurs.nom',
                'crbt',
                'date_ramassage',
                'date_livraison',
                'etat_id',
                'status_reel',
                'frais',
            );

            $where = array();
            if ($this->input->post('type_livraison') && !empty($this->input->post('type_livraison'))) {
                array_push($where, ' AND tblcolis.type_livraison = "' . $this->input->post('type_livraison') . '"');
            }
            if ($this->input->post('id_livreur') && is_numeric($this->input->post('id_livreur'))) {
                array_push($where, ' AND tblcolis.livreur = ' . $this->input->post('id_livreur'));
            }
            if ($this->input->post('user_point_relais') && is_numeric($this->input->post('user_point_relais'))) {
                //Get points relais staff
                $pointsRelaisStaff = get_staff_points_relais(false, $this->input->post('user_point_relais'));
                array_push($where, ' AND tblcolis.point_relai_id IN ' . $pointsRelaisStaff);
            }

            array_push($where, 'AND tblcolis.etat_id = 1');
            array_push($where, 'AND (tblcolis.status_id = 2 OR tblcolis.status_reel = 9)');
            array_push($where, 'AND tblcolis.id NOT IN (SELECT colis_id FROM tbletatcolislivreitems WHERE colis_id = tblcolis.id)');

            $join = array('LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblexpediteurs.id as clientid'));
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

                    if ($aColumns[$i] == 'tblcolis.id') {
                        $_data = icon_btn('#', 'plus', 'btn-success colis_added', array('data-id' => $_data));
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<b id="column-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['clientid']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_livraison' || $aColumns[$i] == 'date_ramassage') {
                        if ($_data != NULL) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt') {
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
     * Init historique items etat colis livrer
     */
    public function init_historique_items_etat_colis_livrer()
    {
        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tbletatcolislivreitems.id as colisetatcolislivrer_id',
                'code_barre',
                'tblexpediteurs.nom',
                'crbt',
                'date_ramassage',
                'date_livraison',
                'tblcolis.etat_id',
                'tblcolis.status_reel',
                'frais',
                'tblcolis.ville',
                'tblcolis.status_reel'
            );

            $where = array();
            if ($this->input->post('etat_id') && !empty($this->input->post('etat_id'))) {
                array_push($where, ' AND tbletatcolislivreitems.etat_id = ' . $this->input->post('etat_id'));
            }

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tbletatcolislivreitems.colis_id',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur'
            );


            $sIndexColumn = "id";
            $sTable = 'tbletatcolislivreitems';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.id', 'tblexpediteurs.id as clientid', 'tbletatcolislivreitems.id as colisetatcolislivrer_id','tblcolis.livreur',));
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

                    if ($aColumns[$i] == 'colisetatcolislivrer_id') {
                        $_data = icon_btn('#', 'remove', 'btn-danger colis_remove', array('data-item-id' => $_data));
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-historique-barcode-' . $key, 'code_barre') . '<b id="column-historique-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['clientid']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'tblcolis.etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolis.status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_livraison' || $aColumns[$i] == 'date_ramassage') {
                        if ($_data != NULL) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt') {
                        $_data = number_format($_data, 2, '.', ',');
                    }
                    else if ($aColumns[$i] == 'tblcolis.ville') {
                        if ($aRow['tblcolis.status_reel'] ==9){
                            $this->load->model('commisions_model');
                            $refuseFrais  = $this->commisions_model->get_refuse_commision_livreur($aRow['livreur'], $aRow['tblcolis.ville']);
                            $_data = $refuseFrais;
                        }
                         else if ($aRow['tblcolis.status_reel'] ==2) {
                             $this->load->model('commisions_model');
                             $fraisLivreur  = $this->commisions_model->get_commision_livreur($aRow['livreur'], $aRow['tblcolis.ville']);
                             $_data = $fraisLivreur;

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
     * Export PDF
     */
    public function export_pdf()
    {
        if (!has_permission('etat_colis_livrer', '', 'export')) {
            access_denied('Etat Colis Livrer');
        }

        if (!$this->input->post()) {
            redirect(admin_url('etat_colis_livrer'));
        }

        $deliveryMen = $this->input->post('delivery_men');
        $fromDate = to_sql_date($this->input->post('date_start'));
        $toDate = to_sql_date($this->input->post('date_end'));

        $where = ' 1 = 1 ';
        if (is_numeric($deliveryMen)) {
            $where .= ' AND tbletatcolislivre.id_livreur = ' . $deliveryMen;
        }
        $date = '';
        if ($fromDate == $toDate || (!empty($fromDate) && empty($toDate))) {
            $where .= ' AND date_created like "' . $fromDate . '%"';
            $date = date(get_current_date_format(), strtotime($fromDate));
        } else {
            $where .= ' AND date_created BETWEEN "' . $fromDate . ' 00:00:00" AND "' . $toDate . ' 23:59:59"';
            $date = 'du ' . date(get_current_date_format(), strtotime($fromDate)) . ' au ' . date(get_current_date_format(), strtotime($toDate));
        }

        $results = $this->etat_colis_livrer_model->get('', $where);

        $this->load->model('staff_model');
        $etats = array();
        $cpt = 0;
        if (count($results) > 0) {
            foreach ($results as $key => $result) {
                $etats[$cpt] = $result;
                //Get livreur
                $livreur = $this->staff_model->get($result['id_livreur']);
                $etats[$cpt]['nom_livreur'] = '';
                if ($livreur) {
                    $etats[$cpt]['nom_livreur'] = $livreur->firstname . ' ' . $livreur->lastname;
                }
                //Get Items etat
                $items = $this->etat_colis_livrer_model->get_items_etat_colis_livrer($result['id']);
                $etats[$cpt]['items'] = array();
                if ($items) {
                    $etats[$cpt]['items'] = $items;
                }
                $cpt++;
            }
            //Generate Name PDF
            $name = 'List Etat Colis Livrer ' . $date;
            etat_colis_livrer_by_date_pdf($name, $etats);
        } else {
            set_alert('warning', _l('etat_colis_livrer_does_not_contain_any_colis'));
            redirect(admin_url('etat_colis_livrer'));
        }
    }

    /**
     * Export
     */
    public function export_excel()
    {
        if (!has_permission('etat_colis_livrer', '', 'export')) {
            access_denied('Etat Colis Livrer');
        }

        if (!$this->input->post()) {
            redirect(admin_url('etat_colis_livrer'));
        }

        $deliveryMen = $this->input->post('delivery_men');
        $fromDate = to_sql_date($this->input->post('date_start'));
        $toDate = to_sql_date($this->input->post('date_end'));

        $where = ' 1 = 1 ';
        if (is_numeric($deliveryMen)) {
            $where .= ' AND tbletatcolislivre.id_livreur = ' . $deliveryMen;
        }
        $date = '';
        if ($fromDate == $toDate && (!empty($fromDate) && empty($toDate))) {
            $where .= ' AND tbletatcolislivre.date_created like "' . $fromDate . '%" ';
            $date = date(get_current_date_format(), strtotime($fromDate));
        } else {
            $where .= ' AND tbletatcolislivre.date_created BETWEEN "' . $fromDate . ' 00:00:00" AND "' . $toDate . ' 23:59:59" ';
            $date = 'du ' . date(get_current_date_format(), strtotime($fromDate)) . ' au ' . date(get_current_date_format(), strtotime($toDate));
        }

        $etatsColisLivrer = $this->etat_colis_livrer_model->get_data_export($where);
        if (count($etatsColisLivrer) > 0) {
            //Generate Name PDF
            $filename = 'Liste Etat Colis Livrer ' . $date;
            etat_colis_livrer_excel($filename, $etatsColisLivrer);
        } else {
            set_alert('warning', _l('empty_result'));
            redirect(admin_url('etat_colis_livrer'));
        }
    }

    /**
     * Get list etat colis livrer
     */
    function get_list_etat_colis_livrer()
    {
        $listEtatColisLivrer = array();

        $typeLivraison = $this->input->post('type_livraison');
        $etatColisLivrerId = $this->input->post('etat_colis_livrer_id');
        if (!empty($typeLivraison)) {
            if ($typeLivraison == 'a_domicile') {
                $livreurId = $this->input->post('livreur_id');
                if (is_numeric($livreurId)) {
                    $where = 'id_livreur = ' . $livreurId;
                    if (is_numeric($etatColisLivrerId)) {
                        $where .= ' AND (etat = 1 OR etat = 2)';
                    } else {
                        $where .= ' AND etat = 1';
                    }
                }
            } else if ($typeLivraison == 'point_relai') {
                $userPointRelaisId = $this->input->post('livreur_id');
                if (is_numeric($userPointRelaisId)) {
                    $where = 'user_point_relais = ' . $userPointRelaisId;
                    if (is_numeric($etatColisLivrerId)) {
                        $where .= ' AND (etat = 1 OR etat = 2)';
                    } else {
                        $where .= ' AND etat = 1';
                    }
                }
            }

            $listEtatColisLivrer = $this->etat_colis_livrer_model->get('', $where);
        }


        echo json_encode($listEtatColisLivrer);
    }

    /**
     * Check etat colis livrer
     */
    function check_etat_colis_livrer()
    {
        $livreurId = $this->input->post('livreur_id');
        $etatColisLivrerId = $this->input->post('etat_colis_livrer_id');

        $exist = false;
        $rest = 0;
        if (is_numeric($livreurId) && is_numeric($etatColisLivrerId)) {
            $this->load->model('versements_model');
            $versement = $this->versements_model->get_versement_by_etat_colis_livrer_id($livreurId, $etatColisLivrerId);
            if ($versement) {
                $exist = true;
            } else {
                $etatColisLivrer = $this->etat_colis_livrer_model->get($etatColisLivrerId);
                if ($etatColisLivrer) {
                    $rest = $etatColisLivrer->manque;
                }
            }
        }

        echo json_encode(array('exist' => $exist, 'rest' => $rest));
    }
}


