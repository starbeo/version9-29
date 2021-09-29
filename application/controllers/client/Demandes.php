<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demandes extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('demandes_model');
    }

    /**
     * Get list demandes
     */
    public function index($id = false)
    {
        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tbldemandes.name',
                'tbldemandes.type',
                'tbldemandes.department',
                'tbldepartementobjets.name',
                'tbldemandes.priorite',
                'tbldemandes.status',
                'tbldemandes.rating',
                'tbldemandes.datecreated',
                'tbldemandes.message'

            );

            $join = array(
                'LEFT JOIN tbldepartementobjets ON tbldepartementobjets.id = tbldemandes.object',
                'LEFT JOIN tblcolis ON tblcolis.id = tbldemandes.rel_id'
            );

            $where = array('AND tbldemandes.client_id = ' . get_expediteur_user_id());
            //Filtre
            if ($this->input->post('f-priority') && is_numeric($this->input->post('f-priority'))) {
                array_push($where, ' AND tbldemandes.priorite = ' . $this->input->post('f-priority'));
            }
            if ($this->input->post('f-status') && is_numeric($this->input->post('f-status'))) {
                array_push($where, ' AND tbldemandes.status = ' . $this->input->post('f-status'));
            }
            if ($this->input->post('f-date-created-start') && is_date(to_sql_date($this->input->post('f-date-created-start')))) {
                array_push($where, ' AND tbldemandes.datecreated >= "' . to_sql_date($this->input->post('f-date-created-start')) . ' 00:00:00"');
            }
            if ($this->input->post('f-date-created-end') && is_date(to_sql_date($this->input->post('f-date-created-end')))) {
                array_push($where, ' AND tbldemandes.datecreated <= "' . to_sql_date($this->input->post('f-date-created-end')) . ' 23:59:59"');
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
            $result = data_tables_init_demands($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tbldemandes.id', 'tbldemandes.client_id', 'tbldemandes.addedfrom','tblcolis.code_barre'), 'tbldemandes.id', '', 'DESC');
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

                    if ($aColumns[$i] == 'tbldemandes.name') {
                        $_data = '<a href="#" onclick="init_demande(' . $aRow['id'] . ');return false;">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbldemandes.type') {
                        $_data = format_type_demande($_data);
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
                    else if ($aColumns[$i] == 'tbldemandes.message')
                    {
                        $_data = '<div style="display: none">'.$aRow['tbldemandes.message'].'</div>';
                    }


                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('javascript:void(0)', 'eye', 'btn-info', array('onclick' => 'init_demande(' . $aRow['id'] . ');return false;'));
                if ($aRow['tbldemandes.status'] == 1) {
                    $options .= icon_btn('client/demandes/demande/' . $aRow['id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Demande'));
                } else if ($aRow['tbldemandes.status'] == 4) {
                    $options .= icon_btn('client/demandes/demande/' . $aRow['id'], 'star', 'btn-success', array('title' => 'Donner une note'));
                }

                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['demandeid'] = '';
        if (is_numeric($id)) {
            if (owns_data("tbldemandes", get_expediteur_user_id(), '', 'client_id') == 1) {
                $data['demandeid'] = $id;
            }
        }

        // Get priorities
        $data['priorities'] = $this->demandes_model->get_priorities();
        // Get statuses
        $data['statuses'] = $this->demandes_model->get_statuses();

        $data['title'] = _l('requests');
        $this->load->view('client/demandes/manage', $data);
    }

    /**
     * Preview demande
     */
    public function preview($id)
    {
        if (is_numeric($id)) {
            $this->index($id);
        }

        redirect(client_url('demandes'));
    }

    /**
     * Get data demande Ajax
     */
    public function get_demande_data_ajax($id)
    {
        if (!is_numeric($id)) {
            die('Aucune demande trouvée');
        }

        // Get request
        $demande = $this->demandes_model->get($id);
        if (!$demande || ($demande && $demande->client_id != get_expediteur_user_id())) {
            echo _l('request_not_found');
            die;
        }

        $data['demande'] = $demande;
        $this->load->view('client/demandes/demande_preview_template', $data);
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
                    redirect(client_url('demandes'));
                }
            } else {
                $success = $this->demandes_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('request')));
                }
                redirect(client_url('demandes/demande/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('request_lowercase'));
        } else {
            $demande = $this->demandes_model->get($id);
            if (!$demande || ($demande && $demande->client_id != get_expediteur_user_id())) {
                set_alert('warning', _l('not_found', _l('request')));
                redirect(client_url('demandes'));
            }

            $title = _l('edit', _l('request_lowercase'));
            $data['demande'] = $demande;
            //Get objets departement
            $this->load->model('departements_model');
            $data['objets'] = $this->departements_model->get_objets('', '(visibility = "all" OR visibility = "client") AND type = "' . $demande->type . '"');
        }

        //Get types
        $data['types'] = $this->demandes_model->get_types();
        //Get priorities
        $data['priorities'] = $this->demandes_model->get_priorities();

        $data['title'] = $title;
        $this->load->view('client/demandes/demande', $data);
    }

    /**
     * Get object by type
     */
    function get_object_by_type()
    {
        $success = false;
        $objets = array();
        $type = 'demande';
        if ($type && !empty($type)) {
            $this->load->model('departements_model');
            $objets = $this->departements_model->get_objets('', '(visibility = "all" OR visibility = "administration") AND type = "' . $type . '"');
            $type = 'reclamation';
            $objets2 = $this->departements_model->get_objets('', '(visibility = "all" OR visibility = "administration") AND type = "' . $type . '"');
            $objects =    array_merge($objets,$objets2);
            if ($objets && count($objets) > 0) {
                $success = true;
            }
        }

        echo json_encode(array('success' => $success, 'objets' => $objects));
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

        $clientId = get_expediteur_user_id();
        $objectId = $this->input->post('object_id');
        if ($clientId && $objectId && is_numeric($clientId) && is_numeric($objectId)) {
            // Get objet
            $this->load->model('departements_model');
            $objet = $this->departements_model->get_objets($objectId);
            if ($objet) {
                if ($objet->bind == 1 && !empty($objet->bind_to)) {
                    if ($objet->bind_to == 'factures') {
                        $this->load->model('factures_model');
                        $list = $this->factures_model->get_factures_by_clientid('id_expediteur = ' . $clientId);
                    } else if ($objet->bind_to == 'colis') {
                        $this->load->model('colis_model');
                        $list = $this->colis_model->get_colis_by_clientid('etat_id != 3 AND id_expediteur = ' . $clientId);
                    }
                    else if ($objet->bind_to == 'demandes') {
                        $this->load->model('demandes_model');
                        $list = $this->demandes_model->get_demandes_by_clientid('client_id = ' . $clientId);
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
            redirect(client_url('demandes'));
        }

        if (file_exists(DEMANDES_ATTACHED_PIECE_FOLDER . $id)) {
            $this->db->where('id', $id);
            $this->db->where('client_id', get_expediteur_user_id());
            $this->db->update('tbldemandes', array('attached_piece' => NULL, 'attached_piece_type' => NULL));
            if ($this->db->affected_rows() > 0) {
                delete_dir(DEMANDES_ATTACHED_PIECE_FOLDER . $id);
            }
        }

        redirect(client_url('demandes/demande/' . $id));
    }

    /**
     * Add rating demande
     */
    public function add_rating_demande($id = '')
    {
        if ($this->input->post()) {
            $success = false;
            $type = 'warning';
            $message = _l('problem_adding', _l('note'));
            if (is_numeric($this->input->post('demande_id'))) {
                //Get demande
                $demande = $this->demandes_model->get($this->input->post('demande_id'));
                if ($demande && $demande->client_id == get_expediteur_user_id()) {
                    $success = $this->demandes_model->add_rating($this->input->post());
                    if ($success) {
                        $type = 'success';
                        $message = _l('added_successfuly', _l('note'));
                    }
                }
            }

            echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
        }
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
                if ($demande && $demande->client_id == get_expediteur_user_id()) {
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
            if (is_numeric($demandeId)) {
                $demande = $this->demandes_model->get($demandeId);
                if ($demande && $demande->client_id == get_expediteur_user_id()) {
                    $content = $this->input->post('content');
                    $id = $this->demandes_model->add_discussion($demandeId, $content);
                    if (is_numeric($id)) {
                        $success = true;
                        $type = 'success';
                        $message = _l('added_successfuly', _l('discussion'));
                    }
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }


    function get_object_by_priority()
    {
        $success = false;
        $objets = array();
        $type = $this->input->post('type');
        if ($type && !empty($type)) {
            switch ($type)  {
                case 1:
                case 2:
                case 21:
                case 23:
                case 5:
                case 7:
                case 3:
                case 4:
                    array_push($objets,3,'Haute') ;
                    break;
                case 24:
                case 25:
                case 18:
                case 6:
                case 8:
                case 26:
                case 12:
                    array_push($objets,2,'Moyenne') ;

                    break;
                default :
                    array_push( $objets,1,'Faible') ;

                    break;

            }
            if ($objets && count($objets) > 0) {
                $success = true;
            }
        }

        echo json_encode(array('success' => $success, 'objets' => $objets));
    }


}

