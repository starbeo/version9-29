<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departements extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('departements_model');
        
        if(get_permission_module('demandes') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all departements
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('departements');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array('tbldepartements.name', 'tbldepartements.color', 'tbldepartements.addedfrom', 'tbldepartements.date_created');

            $sIndexColumn = "id";
            $sTable = 'tbldepartements';

            $join = array('LEFT JOIN tblstaff ON tblstaff.staffid = tbldepartements.addedfrom');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, '', array(), array('id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur'), 'tbldepartements.name', '', 'ASC');
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

                    if ($aColumns[$i] == 'tbldepartements.name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#departement_modal" data-id="' . $aRow['id'] . '" data-color="' . $aRow['tbldepartements.color'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbldepartements.color') {
                        $_data = '<i class="fa fa-bookmark fa-lg" style="color: ' . $_data . '"></i>';
                    } else if ($aColumns[$i] == 'tbldepartements.addedfrom') {
                        $utilisateurId = $_data;
                        $_data = staff_profile_image($utilisateurId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $utilisateurId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
                    } else if ($aColumns[$i] == 'tbldepartements.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#departement_modal', 'data-id' => $aRow['id'], 'data-color' => $aRow['tbldepartements.color']));
                $options .= icon_btn('admin/departements/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('departements');
        $this->load->view('admin/departements/manage', $data);
    }

    /**
     * Add new departement or edit existing
     */
    public function departement()
    {
        $success = false;
        $type = 'warning';
        $message = _l('access_denied');
        if ($this->input->is_ajax_request() && $this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);
            if (!is_numeric($id)) {
                $success = $this->departements_model->add($data);
                $message = _l('problem_adding', _l('departement'));
                if ($success) {
                    $type = 'success';
                    $message = _l('added_successfuly', _l('departement'));
                }
            } else {
                $success = $this->departements_model->update($data, $id);
                $message = _l('problem_updating', _l('departement'));
                if ($success) {
                    $type = 'success';
                    $message = _l('updated_successfuly', _l('departement'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Delete departement
     */
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('departements');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('departements'));
        }

        $response = $this->departements_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('departement_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('departement')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('departement_lowercase')));
        }

        redirect(admin_url('departements'));
    }

    /**
     * List all objets departement
     */
    public function objets()
    {
        if (!is_admin()) {
            access_denied('objets');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array('tbldepartementobjets.type', 'tbldepartementobjets.departement_id', 'tbldepartementobjets.name', 'tbldepartementobjets.addedfrom', 'tbldepartementobjets.date_created');

            $sIndexColumn = "id";
            $sTable = 'tbldepartementobjets';

            $join = array('LEFT JOIN tblstaff ON tblstaff.staffid = tbldepartementobjets.addedfrom');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, '', array(), array('id', 'tbldepartementobjets.visibility', 'tbldepartementobjets.bind', 'tbldepartementobjets.bind_to', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur'));
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

                    if ($aColumns[$i] == 'tbldepartementobjets.type') {
                        $_data = format_type_demande($_data);
                    } else if ($aColumns[$i] == 'tbldepartementobjets.departement_id') {
                        $_data = format_departement($_data);
                    } else if ($aColumns[$i] == 'tbldepartementobjets.name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#objet_departement_modal" data-id="' . $aRow['id'] . '" data-type="' . $aRow['tbldepartementobjets.type'] . '" data-departement-id="' . $aRow['tbldepartementobjets.departement_id'] . '" data-visibility="' . $aRow['visibility'] . '" data-bind="' . $aRow['bind'] . '" data-bind-to="' . $aRow['bind_to'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbldepartementobjets.addedfrom') {
                        $utilisateurId = $_data;
                        $_data = staff_profile_image($utilisateurId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $utilisateurId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
                    } else if ($aColumns[$i] == 'tbldepartementobjets.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#objet_departement_modal', 'data-id' => $aRow['id'], 'data-type' => $aRow['tbldepartementobjets.type'], 'data-departement-id' => $aRow['tbldepartementobjets.departement_id'], 'data-visibility' => $aRow['visibility'], 'data-bind' => $aRow['bind'], 'data-bind-to' => $aRow['bind_to']));
                $options .= icon_btn('admin/departements/delete_objet/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get types demande
        $this->load->model('demandes_model');
        $data['types'] = $this->demandes_model->get_types();
        // Get visibilities
        $data['visibilities'] = $this->departements_model->get_visibilities();
        // Get objets departement
        $data['departements'] = $this->departements_model->get();
        // Get Modules
        $data['modules'] = $this->departements_model->get_modules();
        //Get Available merge fields
        $this->load->model('emails_model');
        $data['available_merge_fields'] = $this->emails_model->get_available_merge_fields();

        $data['title'] = _l('objets');
        $data['ckeditor_assets'] = true;
        $this->load->view('admin/departements/objets/manage', $data);
    }

    /**
     * Add new objet or edit existing
     */
    public function objet()
    {
        $success = false;
        $type = 'warning';
        $message = _l('access_denied');
        if ($this->input->is_ajax_request() && $this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);
            if (!is_numeric($id)) {
                $success = $this->departements_model->add_objet($data);
                $message = _l('problem_adding', _l('departement'));
                if ($success) {
                    $type = 'success';
                    $message = _l('added_successfuly', _l('departement'));
                }
            } else {
                $success = $this->departements_model->update_objet($data, $id);
                $message = _l('problem_updating', _l('departement'));
                if ($success) {
                    $type = 'success';
                    $message = _l('updated_successfuly', _l('departement'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Get objet
     */
    function get_objet($id)
    {
        echo json_encode($this->departements_model->get_objets($id));
    }

    /**
     * Delete objet
     */
    public function delete_objet($id)
    {
        if (!is_admin()) {
            access_denied('objets');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('departements/objets'));
        }

        $response = $this->departements_model->delete_objet($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('objet_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('objet')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('objet_lowercase')));
        }

        redirect(admin_url('departements/objets'));
    }
}
