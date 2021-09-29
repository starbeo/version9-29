<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Versements extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('versements_model');

        if (get_permission_module('versements') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all versements
     */
    public function livreurs()
    {
        $has_permission = has_permission('versements', '', 'view');
        if (!has_permission('versements', '', 'view') && !has_permission('versements', '', 'view_own')) {
            access_denied('versements');
        }

        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tbllivreurversements.id', 'tbllivreurversements.name');
            // Check if option show point relai is actived
            if (get_permission_module('points_relais') == 1) {
                array_push($aColumns, 'tbllivreurversements.type_livraison');
            }
            array_push($aColumns, 'tbletatcolislivre.nom', 'tbllivreurversements.total', 'tbllivreurversements.reference_transaction', 'tbllivreurversements.livreur_id', 'tbllivreurversements.date_created', 'tbllivreurversements.last_update_date', 'tbllivreurversements.addedfrom');

            $sIndexColumn = "id";
            $sTable = 'tbllivreurversements';

            $join = array(
                "LEFT JOIN tblstaff as a ON a.staffid = tbllivreurversements.livreur_id AND tbllivreurversements.type_livraison = 'a_domicile'",
                "LEFT JOIN tblstaff as b ON b.staffid = tbllivreurversements.addedfrom",
                "LEFT JOIN tblstaff as c ON c.staffid = tbllivreurversements.livreur_id AND tbllivreurversements.type_livraison = 'point_relai'",
                "LEFT JOIN tbletatcolislivre ON tbletatcolislivre.id = tbllivreurversements.etat_colis_livre_id"
            );

            $where = array();
            //If not admin show only own estimates
            if (!$has_permission) {
                array_push($where, 'AND tbllivreurversements.addedfrom = "' . get_staff_user_id() . '"');
            }

            if (is_livreur()) {
                $livreur_id = get_staff_user_id();
                array_push($where, ' AND tbllivreurversements.livreur_id = ' . $livreur_id);
            }

            //Filtre
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                array_push($where, ' AND tbllivreurversements.type_livraison = "' . $this->input->post('f-type-livraison') . '"');
            }
            if ($this->input->post('f-livreur') && is_numeric($this->input->post('f-livreur'))) {
                array_push($where, ' AND tbllivreurversements.livreur_id = ' . $this->input->post('f-livreur'));
            }
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                array_push($where, ' AND tbllivreurversements.livreur_id = ' . $this->input->post('f-point-relai'));
            }
            if ($this->input->post('f-utilisateur') && is_numeric($this->input->post('f-utilisateur'))) {
                array_push($where, ' AND tbllivreurversements.addedfrom = ' . $this->input->post('f-utilisateur'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tbllivreurversements.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }
            //Affichage historiques versements etat colis livrer
            if ($this->input->post('f-etat-colis-livrer') && is_numeric($this->input->post('f-etat-colis-livrer'))) {
                array_push($where, ' AND tbllivreurversements.etat_colis_livre_id = ' . $this->input->post('f-etat-colis-livrer'));
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tbllivreurversements.type_livraison', 'tbllivreurversements.etat_colis_livre_id', 'CONCAT(a.firstname, " ", a.lastname) as name_livreur', 'CONCAT(c.firstname, " ", c.lastname) as name_user_point_relai', 'CONCAT(b.firstname, " ", b.lastname) as name_utilisateur'));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tbllivreurversements.name') {
                        $_data = '<a href="javascript:void(0)" data-toggle="modal" data-target="#versement_modal" data-id="' . $aRow['tbllivreurversements.id'] . '" data-type-livraison="' . $aRow['type_livraison'] . '" data-livreur-id="' . $aRow['tbllivreurversements.livreur_id'] . '" data-etat-colis-livrer-id="' . $aRow['etat_colis_livre_id'] . '" data-total="' . $aRow['tbllivreurversements.total'] . '" >' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbllivreurversements.type_livraison' || $aColumns[$i] == 'type_livraison') {
                        $_data = format_type_livraison_colis($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.nom') {
                        $_data = '<a href="' . admin_url('etat_colis_livrer/etat/' . $aRow['etat_colis_livre_id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbllivreurversements.total') {
                        $_data = number_format($_data, 2, '.', ' ');
                    } else if ($aColumns[$i] == 'tbllivreurversements.livreur_id') {
                        if ($aRow['tbllivreurversements.type_livraison'] == 'a_domicile' || $aRow['type_livraison'] == 'a_domicile') {
                            $_data = render_icon_motorcycle() . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['name_livreur'] . '</a>';
                        } else {
                            $_data = render_icon_university() . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['name_user_point_relai'] . '</a>';
                        }
                    } else if ($aColumns[$i] == 'tbllivreurversements.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tbllivreurversements.last_update_date') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_time_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tbllivreurversements.addedfrom') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = '';
                //Affichage historiques versements etat colis livrer
                if (!$this->input->post('f-etat-colis-livrer')) {
                    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#versement_modal', 'data-id' => $aRow['tbllivreurversements.id'], 'data-type-livraison' => $aRow['type_livraison'], 'data-livreur-id' => $aRow['tbllivreurversements.livreur_id'], 'data-etat-colis-livrer-id' => $aRow['etat_colis_livre_id'], 'data-total' => $aRow['tbllivreurversements.total']));
                    $options .= icon_btn('admin/versements/delete/' . $aRow['tbllivreurversements.id'], 'remove', 'btn-danger btn-delete-confirm');
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get types livraison
        $this->load->model('colis_model');
        $data['types_livraison'] = $this->colis_model->get_types_livraison();
        //Get Delivery mens & Staffs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        $data['staff'] = $this->staff_model->get('', 1, 'staffid != 1');
        // Check if option show point relai is actived
        $data['point_relais_users'] = array();
        if (get_permission_module('points_relais') == 1) {
            // Get users point relais
            $data['point_relais_users'] = $this->staff_model->get('', '', array('admin' => 4));
        }

        $data['title'] = _l('als_versements');
        $this->load->view('admin/versements/manage', $data);
    }

    /**
     * Edit or add new versement
     */
    public function versement($id = '')
    {
        if ($this->input->post()) {
            if ($this->input->post('id') == "") {
                if (!has_permission('versements', '', 'create')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $id = $this->versements_model->add($this->input->post());
                    if (is_numeric($id)) {
                        $success = true;
                        $message = _l('added_successfuly', _l('versement'));
                        echo json_encode(array('success' => $success, 'message' => $message));
                    }
                }
            } else {
                if (!has_permission('versements', '', 'edit')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $data = $this->input->post();
                    $success = $this->versements_model->update($data);
                    if ($success) {
                        $message = _l('updated_successfuly', _l('versement'));
                        echo json_encode(array('success' => $success, 'message' => $message));
                    }
                }
            }
            die;
        }
    }

    /**
     * Delete versements from database
     */
    public function delete($id)
    {
        if (!has_permission('versements', '', 'delete')) {
            access_denied('versements');
        }

        $response = $this->versements_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('versement')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('versement_lowercase')));
        }

        redirect(admin_url('versements/livreurs'));
    }
}
