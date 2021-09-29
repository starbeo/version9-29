<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demandes extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('demandes_model');
    }

    /**
     * Get list demandes
     */
    public function index($id = '', $status = false)
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tbldemandes.id',
                'tbldemandes.name',
                'tbldemandes.type',
                'tblexpediteurs.nom',
                'tbldemandes.department',
                'tbldepartementobjets.name',
                'tbldemandes.priorite',
                'tbldemandes.status',
                'tbldemandes.rating',
                'tbldemandes.datecreated'
            );

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tbldemandes.client_id',
                'LEFT JOIN tbldepartementobjets ON tbldepartementobjets.id = tbldemandes.object'
            );

            $where = array('AND tbldemandes.addedfrom = ' . get_staff_user_id());
            if (is_numeric($status)) {
                array_push($where, 'AND tbldemandes.status = ' . $status);
            }
            //Filtre
            if ($this->input->post('f-type') && !empty($this->input->post('f-type'))) {
                array_push($where, ' AND tbldemandes.type = "' . $this->input->post('f-type') . '"');
            }
            if ($this->input->post('f-objet') && is_numeric($this->input->post('f-objet'))) {
                array_push($where, ' AND tbldemandes.object = ' . $this->input->post('f-objet'));
            }
            if ($this->input->post('f-client') && is_numeric($this->input->post('f-client'))) {
                array_push($where, ' AND tbldemandes.client_id = ' . $this->input->post('f-client'));
            }
            if ($this->input->post('f-departement') && is_numeric($this->input->post('f-departement'))) {
                array_push($where, ' AND tbldemandes.department = ' . $this->input->post('f-departement'));
            }
            if ($this->input->post('f-priority') && is_numeric($this->input->post('f-priority'))) {
                array_push($where, ' AND tbldemandes.priorite = ' . $this->input->post('f-priority'));
            }
            if ($this->input->post('f-status') && is_numeric($this->input->post('f-status'))) {
                array_push($where, ' AND tbldemandes.status = ' . $this->input->post('f-status'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tbldemandes.datecreated LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }
            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tbldemandes.datecreated LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tbldemandes.datecreated, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tbldemandes.datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $sIndexColumn = "id";
            $sTable = 'tbldemandes';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, array('tbldemandes.addedfrom'));
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

                    if ($aColumns[$i] == 'tbldemandes.id') {
                        $_data = '<div class="checkbox checkbox-primary"><input id="checkbox-demande-' . $_data . '" value="' . $_data . '" name="ids[]" class="checkbox-demande" type="checkbox" /><label></label></div>';
                    } else if ($aColumns[$i] == 'tbldemandes.name') {
                        $_data = '<a href="#" onclick="init_demande(' . $aRow['tbldemandes.id'] . ');return false;">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbldemandes.type') {
                        $_data = format_type_demande($_data);
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tbldemandes.department') {
                        $_data = format_departement($_data);
                    } else if ($aColumns[$i] == 'tbldemandes.priorite') {
                        $_data = format_priorite_demande($_data);
                    } else if ($aColumns[$i] == 'tbldemandes.status') {
                        $_data = format_status_demande($_data);
                    } else if ($aColumns[$i] == 'tbldemandes.rating') {
                        $_data = rating_demande($_data);
                    } else if ($aColumns[$i] == 'tbldemandes.datecreated') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'eye', 'btn-info', array('onclick' => 'init_demande(' . $aRow['tbldemandes.id'] . ');return false;'));
                if (is_numeric($aRow['addedfrom']) && $aRow['addedfrom'] == get_staff_user_id() && $aRow['tbldemandes.status'] == 1) {
                    $options .= icon_btn('point_relais/demandes/demande/' . $aRow['tbldemandes.id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Demande'));
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['demandeid'] = '';
        if (is_numeric($id)) {
            $data['demandeid'] = $id;
        }

        //Get types
        $data['types'] = $this->demandes_model->get_types();
        // Get clients
        $this->load->model('expediteurs_model');
        $data['expediteurs'] = $this->expediteurs_model->get();
        // Get priorities
        $data['priorities'] = $this->demandes_model->get_priorities();
        // Get Objets
        $this->load->model('departements_model');
        $data['objets'] = $this->departements_model->get_objets();
        // Get departements
        $data['departements'] = $this->departements_model->get();
        // Get statuses
        $data['statuses'] = $this->demandes_model->get_statuses();

        $data['title'] = _l('requests');
        $this->load->view('point-relais/demandes/manage', $data);
    }

    /**
     * Get list demande en cours
     */
    public function en_cours()
    {
        $this->index('', 1);
    }

    /**
     * Get list demande cloturer
     */
    public function cloturer()
    {
        $this->index('', 4);
    }

    /**
     * Preview demande
     */
    public function preview($id)
    {
        if (is_numeric($id)) {
            $this->index($id);
        } else {
            redirect(point_relais_url('demandes'));
        }
    }

    /**
     * Get data demande Ajax
     */
    public function get_demande_data_ajax($id)
    {
        $demande = $this->demandes_model->get($id);
        if (!$demande || $demande->addedfrom != get_staff_user_id()) {
            echo _l('request_not_found');
            die;
        }

        $data['demande'] = $demande;
        $this->load->view('point-relais/demandes/demande_preview_template', $data);
    }

    /**
     * Edit or add new demande 
     */
    public function demande($id = '')
    {
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                $id = $this->demandes_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('request')));
                    redirect(point_relais_url('demandes'));
                }
            } else {
                $success = $this->demandes_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('request')));
                }
                redirect(point_relais_url('demandes/demande/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('request_lowercase'));
        } else {
            $demande = $this->demandes_model->get($id);
            if (!$demande || $demande->addedfrom != get_staff_user_id()) {
                set_alert('warning', _l('not_found', _l('request')));
                redirect(point_relais_url('demandes'));
            }

            $data['demande'] = $demande;
            $title = _l('edit', _l('request_lowercase'));
            //Get objets departement
            $this->load->model('departements_model');
            $data['objets'] = $this->departements_model->get_objets('', '(visibility = "all" OR visibility = "administration") AND type = "' . $demande->type . '"');
        }

        //Get clients
        $this->load->model('expediteurs_model');
        $data['clients'] = $this->expediteurs_model->get();
        //Get types
        $data['types'] = $this->demandes_model->get_types();
        //Get priorities
        $data['priorities'] = $this->demandes_model->get_priorities();

        $data['title'] = $title;
        $this->load->view('point-relais/demandes/demande', $data);
    }

    /**
     * Get object by type
     */
    function get_object_by_type()
    {
        $success = false;
        $objets = array();
        $type = $this->input->post('type');
        if ($type && !empty($type)) {
            $this->load->model('departements_model');
            $objets = $this->departements_model->get_objets('', '(visibility = "all" OR visibility = "administration") AND type = "' . $type . '"');
            if ($objets && count($objets) > 0) {
                $success = true;
            }
        }

        echo json_encode(array('success' => $success, 'objets' => $objets));
    }

    /**
     * Get department by object
     */
    function get_department_by_object()
    {
        $success = false;
        $departmentName = '';
        $objectId = $this->input->post('object_id');
        if ($objectId && is_numeric($objectId)) {
            $this->load->model('departements_model');
            $department = $this->departements_model->get_departement_by_objet($objectId);
            if ($department) {
                $success = true;
                $departmentName = $department->name;
            }
        }

        echo json_encode(array('success' => $success, 'department_name' => $departmentName));
    }

    /**
     * Get list relation object demande 
     */
    function get_relations_demande()
    {
        $list = array();

        $clientId = $this->input->post('client_id');
        $objectId = $this->input->post('object_id');
        if ($clientId && $objectId && is_numeric($clientId) && is_numeric($objectId)) {
            // Get objet
            $this->load->model('departements_model');
            $objet = $this->departements_model->get_objets($objectId);
            if ($objet) {
                if ($objet->bind == 1 && !empty($objet->bind_to)) {
                    //Get points relais staff
                    $pointsRelaisStaff = get_staff_points_relais();
                    if ($objet->bind_to == 'factures') {
                        $this->load->model('factures_model');
                        $list = $this->factures_model->get_factures_by_clientid('id_expediteur = ' . $clientId);
                    } else if ($objet->bind_to == 'colis') {
                        $this->load->model('colis_model');
                        $list = $this->colis_model->get_colis_by_clientid('etat_id != 3 AND point_relai_id IN ' . $pointsRelaisStaff . ' AND id_expediteur = ' . $clientId);
                    }
                }
            }
        }

        echo json_encode($list);
    }

    /**
     * Remove attached piece demande
     */
    public function remove_attached_piece_demande($id)
    {
        if (!is_numeric($id)) {
            redirect(point_relais_url('demandes'));
        }

        if (file_exists(DEMANDES_ATTACHED_PIECE_FOLDER . $id)) {
            delete_dir(DEMANDES_ATTACHED_PIECE_FOLDER . $id);
        }
        $this->db->where('id', $id);
        $this->db->update('tbldemandes', array('attached_piece' => NULL, 'attached_piece_type' => NULL));
        if ($this->db->affected_rows() > 0) {
            redirect(point_relais_url('demandes/demande/' . $id));
        }
    }

    /**
     * Add note demande
     */
    public function add_note()
    {
        $success = false;
        $type = "warning";
        $message = _l('problem_adding', _l('note'));
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if (is_numeric($data['demande_id'])) {
                $success = $this->demandes_model->add_note($data);
                if ($success) {
                    $type = "success";
                    $message = _l('added_successfuly', _l('note'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Get all discussions
     */
    public function discussions()
    {
        if ($this->input->post()) {
            $demandeId = $this->input->post('demande_id');

            $discussions = array();
            if (is_numeric($demandeId)) {
                $demande = $this->demandes_model->get($demandeId);
                if ($demande) {
                    $discussions = $this->demandes_model->get_discussions($demandeId);
                }
            }

            echo json_encode($discussions);
        }
    }

    /**
     * Add discussion demande
     */
    public function add_discussion()
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_adding', _l('discussion'));
        if ($this->input->post()) {
            $demandeId = $this->input->post('demande_id');
            $content = $this->input->post('content');
            $id = $this->demandes_model->add_discussion($demandeId, $content);
            if (is_numeric($id)) {
                $success = true;
                $type = 'success';
                $message = _l('added_successfuly', _l('discussion'));
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }
}
