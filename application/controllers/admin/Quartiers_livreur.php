<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quartiers_livreur extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('quartiers_livreur_model');

        if (get_permission_module('quartiers_livreur') == 0) {
            redirect(admin_url('home'));
        }
    }
    /* List all quartiers */

    public function index()
    {
        if (!has_permission('quartiers_livreur', '', 'view')) {
            access_denied('Quartiers Livreur');
        }

        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tbllivreurquartiers.id',
                'name',
                'firstname'
            );

            $sIndexColumn = "id";
            $sTable = 'tbllivreurquartiers';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tbllivreurquartiers.livreur_id',
                'LEFT JOIN tblquartiers ON tblquartiers.id = tbllivreurquartiers.quartier_id'
            );
            $where = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('quartier_id', 'livreur_id', 'staffid', 'lastname'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#quartier_livreur_modal" data-id=' . $aRow['tbllivreurquartiers.id'] . ' data-quartier-id=' . $aRow['quartier_id'] . ' data-livreur-id=' . $aRow['livreur_id'] . '>' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'firstname') {
                        $_data = render_icon_motorcycle() . '<a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#quartier_livreur_modal', 'data-id' => $aRow['tbllivreurquartiers.id'], 'data-quartier-id' => $aRow['quartier_id'], 'data-livreur-id' => $aRow['livreur_id']));
                $options .= icon_btn('admin/quartiers_livreur/delete/' . $aRow['tbllivreurquartiers.id'], 'remove', 'btn-danger btn-delete-confirm');

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();

        $this->load->model('quartiers_model');
        $data['quartiers'] = $this->quartiers_model->get();

        $data['title'] = _l('als_quartiers_livreur');
        $this->load->view('admin/quartiers_livreur/manage', $data);
    }
    /* Edit or add new quartier livreur */

    public function quartier_livreur($id = '')
    {
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->post()) {
            if ($this->input->post('id') == "") {
                if (!has_permission('quartiers_livreur', '', 'create')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $result = $this->quartiers_livreur_model->add($this->input->post());
                    if (is_numeric($result)) {
                        $success = true;
                        $message = _l('added_successfuly', _l('quartier_livreur'));
                    } else if (is_array($result)) {
                        $success = 'warning';
                        $message = _l('quartier_livreur_already_exists');
                    } else {
                        $success = 'warning';
                        $message = _l('problem_updating', _l('quartier_livreur'));
                    }
                    echo json_encode(array('success' => $success, 'message' => $message));
                }
            } else {
                if (!has_permission('quartiers_livreur', '', 'edit')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $data = $this->input->post();
                    $result = $this->quartiers_livreur_model->update($data);
                    if (is_numeric($result)) {
                        $success = true;
                        $message = _l('updated_successfuly', _l('quartier_livreur'));
                    } else if (is_array($result)) {
                        $success = 'warning';
                        $message = _l('quartier_livreur_already_exists');
                    } else {
                        $success = 'warning';
                        $message = _l('problem_updating', _l('quartier_livreur'));
                    }
                    echo json_encode(array('success' => $success, 'message' => $message));
                }
            }
            die;
        }
    }
    /* Delete quartier livreur from database */

    public function delete($id)
    {
        if (!has_permission('quartiers_livreur', '', 'delete')) {
            access_denied('Quartiers Livreur');
        }

        $response = $this->quartiers_livreur_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('quartier_livreur')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('quartier_livreur_lowercase')));
        }

        redirect(admin_url('quartiers_livreur'));
    }
    /* Get livreurs by quartier id */

    function get_livreurs_by_quartier($id)
    {
        echo json_encode($this->quartiers_livreur_model->get_livreurs_by_quartier($id));
    }
    /* Get livreur by quartier id */

    function get_livreur_by_quartier($id)
    {
        echo json_encode($this->quartiers_livreur_model->get_livreur_by_quartier($id));
    }
}
