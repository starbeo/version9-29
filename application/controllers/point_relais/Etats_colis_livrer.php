<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_colis_livrer extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('etat_colis_livrer_model');
    }

    /**
     * List all etat colis livrer
     */
    public function index($status = false, $etat = false)
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tbletatcolislivre.id',
                'tbletatcolislivre.nom',
                'tbletatcolislivre.total',
                'tbletatcolislivre.total_received',
                'tbletatcolislivre.manque',
                '(SELECT COUNT(id) FROM tbletatcolislivreitems WHERE etat_id = tbletatcolislivre.id) as nbr_colis',
                'tbletatcolislivre.status',
                'tbletatcolislivre.etat',
                'tbletatcolislivre.date_created',
                'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_staff'
            );

            $sIndexColumn = "id";
            $sTable = 'tbletatcolislivre';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tbletatcolislivre.id_utilisateur'
            );

            $where = array(' AND tbletatcolislivre.user_point_relais = ' . get_staff_user_id());
            //Filtre
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
            $additionalColumns = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, $additionalColumns);
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

                    if ($aColumns[$i] == 'tbletatcolislivre.nom') {
                        $_data = render_btn_copy('column-name-etat-' . $key, 'name_of_etat_colis_livrer') . '<a id="column-name-etat-' . $key . '" href="' . point_relais_url('etats_colis_livrer/etat/' . $aRow['tbletatcolislivre.id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'point_relai') {
                        $_data = render_icon_university() . '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tbletatcolislivre.total_received' || $aColumns[$i] == 'tbletatcolislivre.total') {
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
                    } else if ($aColumns[$i] == 'nbr_colis') {
                        $_data = render_nombre_colis($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.status') {
                        $_data = format_status_etat_colis_livrer($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.etat') {
                        $_data = format_etat_etat_colis_livrer($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'fullname_staff') {
                        $_data = render_icon_user() . '<b>' . $_data . '</b>';
                    }
                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('point_relais/etats_colis_livrer/etat/' . $aRow['tbletatcolislivre.id'], 'pencil-square-o', 'btn-default mbot5', array('title' => 'Modifier Etat Colis Livrer'));
                if ($aRow['nbr_colis'] > 0) {
                    $options .= icon_btn('point_relais/etats_colis_livrer/pdf/' . $aRow['tbletatcolislivre.id'], 'file-pdf-o', 'btn-danger mbot5', array('title' => 'Imprimer PDF Etat Colis Livrer'));
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('etats_colis_livrer');
        $this->load->view('point-relais/etats-colis-livrer/manage', $data);
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
        if ($this->input->post()) {
            if (is_numeric($id)) {
                $success = $this->etat_colis_livrer_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('etat_colis_livrer')));
                }
                redirect(point_relais_url('etats_colis_livrer/etat/' . $id));
            }
        }

        if (!is_numeric($id)) {
            $lastEtatColisLivrer = $this->etat_colis_livrer_model->get_last_etat_colis_livrer();
            if (!is_null($lastEtatColisLivrer) && total_rows('tbletatcolislivreitems', array('etat_id' => $lastEtatColisLivrer->id)) == 0) {
                $success = $this->etat_colis_livrer_model->update(array(), $lastEtatColisLivrer->id);
                if ($success) {
                    redirect(point_relais_url('etats_colis_livrer/etat/' . $lastEtatColisLivrer->id));
                } else {
                    redirect(point_relais_url('etats_colis_livrer'));
                }
            } else {
                $data['user_point_relais'] = get_staff_user_id();
                $id = $this->etat_colis_livrer_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('etat_colis_livrer')));
                    redirect(point_relais_url('etats_colis_livrer/etat/' . $id));
                }
            }
        } else {
            $etat = $this->etat_colis_livrer_model->get($id);
            if (!$etat || $etat->user_point_relais != get_staff_user_id()) {
                set_alert('warning', _l('not_found', _l('etat_colis_livrer')));
                redirect(point_relais_url('etats_colis_livrer'));
            }

            $data['etat'] = $etat;
            $title = $etat->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        // Get users point relais
        $this->load->model('staff_model');
        $data['point_relais_users'] = $this->staff_model->get('', '', array('staffid' => get_staff_user_id(), 'admin' => 4));

        $data['title'] = $title;
        $this->load->view('point-relais/etats-colis-livrer/etat', $data);
    }

    /**
     * Print PDF etat colis livrer
     */
    public function pdf($id)
    {
        if (!is_numeric($id)) {
            redirect(point_relais_url('etat_colis_livrer'));
        }

        //Get etat colis livrer
        $etatColisLivrer = $this->etat_colis_livrer_model->get($id);
        if (!$etatColisLivrer || $etatColisLivrer->user_point_relais != get_staff_user_id()) {
            set_alert('warning', _l('not_found', _l('etat_colis_livrer')));
            redirect(point_relais_url('etats_colis_livrer'));
        }
        $etatColisLivrer->items = $this->etat_colis_livrer_model->get_items_etat_colis_livrer($id);
        if (count($etatColisLivrer->items) == 0) {
            set_alert('warning', _l('etat_colis_livrer_does_not_contain_any_colis'));
            redirect(point_relais_url('etat_colis_livrer'));
        } else {
            if ($etatColisLivrer->type_livraison == 'point_relai') {
                //Get infos user point relais
                $this->load->model('staff_model');
                $point_relai = $this->staff_model->get($etatColisLivrer->user_point_relais);
                $etatColisLivrer->point_relai = $point_relai;
            }
            etat_colis_livrer_pdf($etatColisLivrer);
        }
    }

    /**
     * Add colis to etat colis livrer
     */
    public function add_colis_to_etat_colis_livrer()
    {
        $colisId = $this->input->post('colis_id');
        $etatId = $this->input->post('etat_id');
        if (is_numeric($colisId) && is_numeric($etatId)) {
            $success = $this->etat_colis_livrer_model->add_colis_to_etat_colis_livrer($etatId, $colisId);
            if (is_array($success) && array_key_exists('id', $success)) {
                $message = _l('added_successfuly', _l('colis_to_etat_colis_livrer'));
                echo json_encode(array('success' => true, 'type' => 'success', 'message' => $message, 'total' => $success['total'], 'commision' => $success['commision']));
            } else if (is_array($result) && array_key_exists('colis_already_exists_in_the_etat_colis_livrer', $success)) {
                $message = _l('colis_already_exists_in_the_etat_colis_livrer');
                echo json_encode(array('success' => false, 'type' => 'warning', 'message' => $message));
            }
        }
    }

    /**
     * Remove colis to etat colis livrer
     */
    public function remove_colis_to_etat_colis_livrer()
    {
        $id = $this->input->post('colis_etat_colis_livrer_id');
        if (is_numeric($id)) {
            $success = $this->etat_colis_livrer_model->remove_colis_to_etat_colis_livrer($id);
            if (is_array($success)) {
                $message = _l('deleted', _l('colis_to_etat_colis_livrer'));
                echo json_encode(array('success' => true, 'type' => 'success', 'message' => $message, 'total' => $success['total'], 'commision' => $success['commision']));
            } else {
                $message = _l('problem_deleting', _l('colis_to_etat_colis_livrer'));
                echo json_encode(array('success' => false, 'type' => 'warning', 'message' => $message));
            }
        }
    }

    /**
     * Init items etat colis livrer
     */
    public function init_items_etat_colis_livrer()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolis.id',
                'code_barre',
                'tblexpediteurs.nom',
                'crbt',
                'frais',
                'date_ramassage',
                'date_livraison',
                'etat_id',
                'status_reel'
            );

            $where = array();
            array_push($where, 'AND tblcolis.etat_id = 1');
            array_push($where, 'AND (tblcolis.status_reel IN (2, 9))');
            //Filtre
            if ($this->input->post('user_point_relais') && is_numeric($this->input->post('user_point_relais'))) {
                //Get points relais staff
                $pointsRelaisStaff = get_staff_points_relais(false, get_staff_user_id());
                array_push($where, ' AND tblcolis.point_relai_id IN ' . $pointsRelaisStaff);
            }
            if ($this->input->post('etat_id') && is_numeric($this->input->post('etat_id'))) {
                array_push($where, 'AND tblcolis.id NOT IN (SELECT colis_id FROM tbletatcolislivreitems WHERE colis_id = tblcolis.id AND tbletatcolislivreitems.etat_id = ' . $this->input->post('etat_id') . ')');
            }

            $join = array('LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur');

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
                    } else if ($aColumns[$i] == 'crbt' || $aColumns[$i] == 'frais') {
                        $_data = number_format($_data, 2, '.', ',');
                    } else if ($aColumns[$i] == 'date_livraison' || $aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
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
            $aColumns = array(
                'tbletatcolislivreitems.id as colisetatcolislivrer_id',
                'code_barre',
                'tblexpediteurs.nom',
                'crbt',
                'frais',
                'date_ramassage',
                'date_livraison',
                'tblcolis.etat_id',
                'tblcolis.status_reel'
            );

            $where = array();
            if ($this->input->post('etat_id') && is_numeric($this->input->post('etat_id'))) {
                array_push($where, ' AND tbletatcolislivreitems.etat_id = ' . $this->input->post('etat_id'));
            }

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tbletatcolislivreitems.colis_id',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur'
            );

            $sIndexColumn = "id";
            $sTable = 'tbletatcolislivreitems';
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

                    if ($aColumns[$i] == 'colisetatcolislivrer_id') {
                        $_data = icon_btn('#', 'remove', 'btn-danger colis_remove', array('data-item-id' => $_data));
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-historique-barcode-' . $key, 'code_barre') . '<b id="column-historique-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<b>' . ucwords($_data) . '</b>';
                    } else if ($aColumns[$i] == 'crbt' || $aColumns[$i] == 'frais') {
                        $_data = number_format($_data, 2, '.', ',');
                    } else if ($aColumns[$i] == 'date_livraison' || $aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcolis.etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolis.status_reel') {
                        $_data = format_status_colis($_data);
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
