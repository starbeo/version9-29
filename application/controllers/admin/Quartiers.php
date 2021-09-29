<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quartiers extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('quartiers_model');

        if (get_permission_module('quartiers') == 0) {
            redirect(admin_url('home'));
        }
    }
    /* List all quartiers */

    public function index()
    {
        if (!has_permission('quartiers', '', 'view')) {
            access_denied('Quartiers');
        }

        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblquartiers.name',
                'tblvilles.name',
                'affecter_livreur'
            );

            $sIndexColumn = "id";
            $sTable = 'tblquartiers';

            $join = array('LEFT JOIN tblvilles ON tblvilles.id = tblquartiers.ville_id');
            $where = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblquartiers.id', 'ville_id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tblquartiers.name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#quartier_modal" data-id="' . $aRow['id'] . '" data-ville=' . $aRow['ville_id'] . '>' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'affecter_livreur') {
                        $checked = '';
                        if ($aRow['affecter_livreur'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['id'] . '" data-switch-url="quartiers/delete_affectation_livreur" ' . $checked . '>';
                        // For exporting
                        $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#quartier_modal', 'data-id' => $aRow['id'], 'data-ville' => $aRow['ville_id']));
                $options .= icon_btn('admin/quartiers/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();

        $data['title'] = _l('als_colis');
        $this->load->view('admin/quartiers/manage', $data);
    }
    /* Edit or add new quartier */

    public function quartier($id = '')
    {
        if ($this->input->is_ajax_request() && $this->input->post()) {
            $type = 'warning';
            $success = 'access_denied';
            $message = _l('access_denied');
            if ($this->input->post('id') == "") {
                if (!has_permission('quartiers', '', 'create')) {
                    $type = 'danger';
                    $success = 'access_denied';
                    $message = _l('access_denied');
                } else {
                    $id = $this->quartiers_model->add($this->input->post());
                    $message = _l('problem_adding', _l('quartier'));
                    if ($id) {
                        $type = 'success';
                        $success = true;
                        $message = _l('added_successfuly', _l('quartier'));
                    }
                }
            } else {
                if (!has_permission('quartiers', '', 'edit')) {
                    $type = 'danger';
                    $success = 'access_denied';
                    $message = _l('access_denied');
                } else {
                    $success = $this->quartiers_model->update($this->input->post());
                    $message = _l('problem_updating', _l('quartier'));
                    if ($success) {
                        $type = 'success';
                        $message = _l('updated_successfuly', _l('quartier'));
                    }
                }
            }

            echo json_encode(array('type' => $type, 'success' => $success, 'message' => $message));
        }
    }
    /* Delete quartier from database */

    public function delete($id)
    {
        if (!has_permission('quartiers', '', 'delete')) {
            access_denied('Quartiers');
        }

        $response = $this->quartiers_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('quartier_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('quartier')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('quartier_lowercase')));
        }

        redirect(admin_url('quartiers'));
    }
    /* Get quartiers */

    function get_quartiers_by_villeid($ville_id)
    {
        $where = array('ville_id' => $ville_id);
        echo json_encode($this->quartiers_model->get('', $where));
    }
}
