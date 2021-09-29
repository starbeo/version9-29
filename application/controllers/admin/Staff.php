<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');

        if (get_permission_module('staff') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all staff members
     */
    public function index($type = false)
    {
        $has_permission = has_permission('staff', '', 'view');
        if (!has_permission('staff', '', 'view') && !has_permission('staff', '', 'view_own')) {
            access_denied('Staff');
        }

        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'staffid',
                'firstname',
                'tblroles.name',
                'tblstaff.email',
                'phonenumber',
                'tblstaff.active'
            );

            $sIndexColumn = "staffid";
            $sTable = 'tblstaff';

            $join = array(
                'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            );

            $where = array();
            array_push($where, 'AND tblstaff.staffid != 1');
            //If not admin show only own estimates
            if (!$has_permission) {
                array_push($where, 'AND tblstaff.addedfrom = "' . get_staff_user_id() . '"');
            }
            if (is_numeric($type)) {
                array_push($where, 'AND tblstaff.admin = ' . $type);
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblstaff.datecreated LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblstaff.datecreated, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblstaff.datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('lastname', 'role', 'admin'));

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

                    if ($aColumns[$i] == 'firstname') {
                        if ($aRow['admin'] == 0) {
                            $icon = render_icon_motorcycle();
                        } else if ($aRow['admin'] == 4) {
                            $icon = render_icon_university();
                        } else {
                            $icon = render_icon_user();
                        }
                        $_data = $icon . ' <a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
                    } else if ($aColumns[$i] == 'tblroles.name') {
                        if (is_null($_data)) {
                            if ($aRow['admin'] == 1) {
                                $_data = '<b>Admin</b>';
                            } else if ($aRow['admin'] == 4) {
                                $_data = '<b>' . _l('point_relai') . '</b>';
                            } else {
                                $_data = '<b>-</b>';
                            }
                        } else {
                            $_data = '<a href="' . admin_url('roles/role/' . $aRow['role']) . '">' . $_data . '</b>';
                        }
                    } else if ($aColumns[$i] == 'email') {
                        $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblstaff.active') {
                        $checked = '';
                        if ($aRow['tblstaff.active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['staffid'] . '" data-switch-url="admin/staff/change_staff_status" ' . $checked . '>';
                        // For exporting
                        $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                    }

                    $row[] = $_data;
                }

                $row[] = icon_btn('admin/staff/member/' . $aRow['staffid'], 'pencil-square-o');
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get Type
        $data['type'] = $type;

        $data['title'] = _l('staff_members');
        $this->load->view('admin/staff/manage', $data);
    }

    /**
     * List all delivery men
     */
    public function administrateurs()
    {
        $this->index(1);
    }

    /**
     * List all delivery men
     */
    public function logistiques()
    {
        $this->index(2);
    }

    /**
     * List all others staff
     */
    public function autres()
    {
        $this->index(3);
    }

    /**
     * List all delivery men
     */
    public function livreurs()
    {
        $this->index(0);
    }

    /**
     * List all points relais
     */
    public function points_relais()
    {
        $this->index(4);
    }

    /**
     * Add new staff member or edit existing
     */
    public function member($id = false, $type = false)
    {
        if (!has_permission('staff', '', 'view') && !has_permission('staff', '', 'view_own')) {
            access_denied('Staff');
        }
        if (is_numeric($id) && $id == 1) {
            redirect(admin_url('staff'));
        }

        $this->load->model('roles_model');
        if ($this->input->post()) {
            if (!is_numeric($id)) {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                $id = $this->staff_model->add($this->input->post());
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfuly', _l('staff_member')));
                    redirect(admin_url('staff/member/' . $id));
                }
            } else {
                if (!has_permission('staff', '', 'edit')) {
                    access_denied('staff');
                }
                handle_staff_profile_image_upload($id);
                $success = $this->staff_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly'));
                }
                redirect(admin_url('staff/member/' . $id));
            }
        }

        if (!is_numeric($id)) {
            $title = _l('add_new', _l('staff_member_lowercase'));
        } else {
            $member = $this->staff_model->get($id);
            $data['member'] = $member;
            $title = _l('edit', _l('staff_member_lowercase')) . ' ' . $member->firstname . ' ' . $member->lastname;
            $data['staff_permissions'] = $this->roles_model->get_staff_permissions($id);
        }

        //Get type
        $data['type'] = $type;
        // Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();
        // Get roles
        $data['roles'] = $this->roles_model->get();
        // Get permissions
        $data['permissions'] = $this->roles_model->get_permissions();
        // Get departements
        $this->load->model('departements_model');
        $data['departements'] = $this->departements_model->get();
        // Check if option show point relai is actived
        if (get_permission_module('points_relais') == 1) {
            // Get points relais
            $this->load->model('points_relais_model');
            $data['points_relais'] = $this->points_relais_model->get('', 1, array(), 'tblpointsrelais.id, CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as nom');
        }

        $data['title'] = $title;
        $this->load->view('admin/staff/member', $data);
    }

    /**
     * View public profile. If id passed view profile by staff id else current user
     */
    public function profile($id = '')
    {
        if ($id == 1) {
            redirect(admin_url('staff'));
        }

        if (is_numeric($id)) {
            $data['staff_p'] = $this->staff_model->get($id);
        } else {
            $data['staff_p'] = $this->staff_model->get(get_staff_user_id());
        }

        $data['title'] = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
        // Notifications
        $total_notifications = total_rows('tblnotifications', array(
            'touserid' => get_staff_user_id()));
        $data['total_pages'] = ceil($total_notifications / $this->misc_model->notifications_limit);
        $this->load->view('admin/staff/myprofile', $data);
    }

    /**
     * Logged in staff notifications
     */
    public function notifications()
    {
        if ($this->input->post()) {
            $this->load->model('misc_model');
            echo json_encode($this->misc_model->get_all_user_notifications($this->input->post('page')));
            die;
        }
    }

    /**
     * When staff edit his profile
     */
    public function edit_profile()
    {
        if ($this->input->post()) {
            handle_staff_profile_image_upload();
            $success = $this->staff_model->update($this->input->post(), get_staff_user_id());
            if ($success) {
                set_alert('success', _l('staff_profile_updated'));
            }
            redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $data['member'] = $member;

        $data['title'] = $member->firstname . ' ' . $member->lastname;
        $this->load->view('admin/staff/profile', $data);
    }

    /**
     * When staff change his password
     */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $response = $this->staff_model->change_password($this->input->post(), get_staff_user_id());

            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorect'));
            } else {
                if ($response == true) {
                    set_alert('success', _l('staff_password_changed'));
                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }

            redirect(admin_url('staff/edit_profile'));
        }
    }

    /**
     * Change status to staff active or inactive / ajax
     */
    public function change_staff_status($id, $status)
    {
        if (has_permission('staff', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->staff_model->change_staff_status($id, $status);
            }
        }
    }

    /**
     * Remove staff profile image / ajax
     */
    public function remove_staff_profile_image($id = '')
    {
        $staff_id = get_staff_user_id();
        if (is_numeric($id) && (has_permission('staff', '', 'create') || has_permission('staff', '', 'edit'))) {
            $staff_id = $id;
        }

        $member = $this->staff_model->get($staff_id);
        if (file_exists(STAFF_PROFILE_IMAGES_FOLDER . $staff_id)) {
            delete_dir(STAFF_PROFILE_IMAGES_FOLDER . $staff_id);
        }
        $this->db->where('staffid', $staff_id);
        $this->db->update('tblstaff', array(
            'profile_image' => NULL
        ));
        if ($this->db->affected_rows() > 0) {
            redirect(admin_url('staff/edit_profile/' . $staff_id));
        }
    }

    /**
     * Get all livreurs
     */
    function get_livreurs($id = '')
    {
        echo json_encode($this->staff_model->get_livreurs($id));
    }

    /**
     * List all bons livraison livreur
     */
    public function init_points_relais($staffId = false)
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblpointsrelais.nom',
                'tblpointsrelais.adresse',
                'tblvilles.name',
                'tblpointrelaisstaff.addedfrom',
                'tblpointrelaisstaff.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblpointrelaisstaff';

            $join = array(
                'LEFT JOIN tblpointsrelais ON tblpointsrelais.id = tblpointrelaisstaff.point_relais_id',
                'LEFT JOIN tblvilles ON tblvilles.id = tblpointsrelais.ville',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblpointrelaisstaff.addedfrom'
            );

            $where = array();
            if (is_numeric($staffId)) {
                array_push($where, 'AND tblpointrelaisstaff.staff_id = ' . $staffId);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, array('tblpointrelaisstaff.id', 'tblpointsrelais.id as point_relais_id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_staff'));
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

                    if ($aColumns[$i] == 'tblpointsrelais.nom') {
                        $_data = '<a href="' . admin_url('points_relais/point_relai/' . $aRow['point_relais_id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblpointrelaisstaff.addedfrom') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['name_staff'] . '</a>';
                    } else if ($aColumns[$i] == 'tblpointrelaisstaff.date_created') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#point_relais_modal', 'data-id' => $aRow['id'], 'data-point-relais-id' => $aRow['point_relais_id']));
                $options .= icon_btn('#', 'remove', 'btn-danger btn-delete-confirm', array('onclick' => 'removePointRelais(' . $aRow['id'] . ')'));
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * Add or edit point relais
     */
    public function point_relais()
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_adding', _l('commision'));
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->staff_model->add_point_relais($data);
                if (is_array($success) && isset($success['already_exist'])) {
                    $message = _l('point_relais_already_exist');
                } else if ($success) {
                    $type = 'success';
                    $message = _l('added_successfuly', _l('point_relais'));
                }
            } else {
                $success = $this->staff_model->update_point_relais($data);
                if ($success) {
                    $type = 'success';
                    $message = _l('updated_successfuly', _l('point_relais'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Delete point relais
     */
    public function delete_point_relais($id)
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_deleting', _l('point_relais_lowercase'));
        if ($this->input->is_ajax_request() && is_numeric($id)) {
            $success = $this->staff_model->delete_point_relais($id);
            if ($success) {
                $type = 'success';
                $message = _l('deleted', _l('point_relais'));
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * List all bons livraison livreur
     */
    public function init_colis_en_attente_livreur($livreurId = false)
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblcolisenattente.code_barre',
                'tblcolisenattente.num_commande',
                'tblexpediteurs.nom',
                'tblcolisenattente.telephone',
                'tblcolisenattente.date_creation',
                'tblcolisenattente.status_id',
                'tblvilles.name',
                'tblcolisenattente.crbt'
            );

            $sIndexColumn = "id";
            $sTable = 'tblcolisenattente';

            $join = array(
                'LEFT JOIN tblstatuscolis ON tblstatuscolis.id = tblcolisenattente.status_id',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolisenattente.ville',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolisenattente.id_expediteur'
            );

            $where = array();
            if (is_numeric($livreurId)) {
                array_push($where, 'AND tblcolisenattente.id_livreur = ' . $livreurId);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolisenattente.id_expediteur', 'tblcolisenattente.colis_id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'tblcolisenattente.code_barre') {
                        $_data = '<b>' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['id_expediteur']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'nom_complet') {
                        $_data = ucwords($_data);
                    } else if ($aColumns[$i] == 'tblcolisenattente.status_id') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolisenattente.date_creation') {
                        if (!is_null($_data)) {
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
     * Init colis livreur
     */
    public function init_colis_livreur($livreur_id)
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
            array_push($where, ' AND tblcolis.livreur = "' . $livreur_id . '"');
            if (is_livreur()) {
                $city = get_city_livreur();
                array_push($where, ' AND tblcolis.ville = ' . $city);
            }
            if (!$has_permission) {
                array_push($where, ' AND tblcolis.id_utilisateur = "' . get_staff_user_id() . '"');
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
                    $_where = ' AND tblcolis.etat_id = ' . $etat;
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
     * List all bons livraison livreur
     */
    public function init_bons_livraison_livreur($livreurId = false)
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblbonlivraison.nom',
                'tblbonlivraison.type',
                'tblbonlivraison.status',
                'tblbonlivraison.commentaire',
                'tblbonlivraison.date_created',
                'tblbonlivraison.id_utilisateur'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblbonlivraison';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblbonlivraison.id_utilisateur'
            );

            $where = array();
            if (is_numeric($livreurId)) {
                array_push($where, 'AND tblbonlivraison.id_livreur = ' . $livreurId);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblbonlivraison.id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur', '(SELECT count(id) FROM tblcolisbonlivraison WHERE bonlivraison_id = tblbonlivraison.id) as nbr_colis'));
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
                        $_data = '<a href="' . admin_url('bon_livraison/bon/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.type') {
                        if ($_data == 1) {
                            $_data = '<span class="label label-info">' . _l('delivery_note_type_output') . '</span>';
                        } else if ($_data == 2) {
                            $_data = '<span class="label label-danger">' . _l('delivery_note_type_returned') . '</span>';
                        }
                    } else if ($aColumns[$i] == 'tblbonlivraison.status') {
                        $_data = format_status_bon_livraison($_data);
                    } else if ($aColumns[$i] == 'tblbonlivraison.commentaire') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.date_created') {
                        $_data = date('d/m/Y', strtotime($_data));
                    } else if ($aColumns[$i] == 'tblbonlivraison.id_utilisateur') {
                        $staffId = $_data;
                        $_data = staff_profile_image($staffId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $staffId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
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
     * List all etat colis livrer livreur
     */
    public function init_etat_colis_livrer_livreur($livreurId = false)
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tbletatcolislivre.nom',
                'tbletatcolislivre.total_received',
                'tbletatcolislivre.total',
                'tbletatcolislivre.manque',
                'tbletatcolislivre.commision',
                'tbletatcolislivre.justif',
                '(SELECT COUNT(id) FROM tbllivreurversements WHERE etat_colis_livre_id = tbletatcolislivre.id) as nbr_versements',
                'tbletatcolislivre.status',
                'tbletatcolislivre.etat',
                'tbletatcolislivre.date_created',
                'tbletatcolislivre.id_utilisateur'
            );

            $sIndexColumn = "id";
            $sTable = 'tbletatcolislivre';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tbletatcolislivre.id_utilisateur'
            );

            $where = array();
            if (is_numeric($livreurId)) {
                array_push($where, ' AND tbletatcolislivre.id_livreur = ' . $livreurId);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tbletatcolislivre.id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur', '(SELECT COUNT(id) FROM tbletatcolislivreitems WHERE etat_id = tbletatcolislivre.id) as nbr_colis'));
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

                    if ($aColumns[$i] == 'tbletatcolislivre.nom') {
                        $_data = '<a href="' . admin_url('etat_colis_livrer/etat/' . $aRow['id']) . '">' . $_data . '</a>';
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
                    } else if ($aColumns[$i] == 'tbletatcolislivre.justif') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    } else if ($aColumns[$i] == 'nbr_versements') {
                        if ($aRow['nbr_versements'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_versements'] . '</span></p>';
                    } else if ($aColumns[$i] == 'tbletatcolislivre.status') {
                        $_data = format_status_etat_colis_livrer($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.etat') {
                        $_data = format_etat_etat_colis_livrer($_data);
                    } else if ($aColumns[$i] == 'tbletatcolislivre.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tbletatcolislivre.id_utilisateur') {
                        $staffId = $_data;
                        $_data = staff_profile_image($staffId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $staffId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
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
     * List all factures livreur
     */
    public function init_factures_livreur($livreurId = false)
    {
        $has_permission = has_permission('invoices', '', 'view');
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('Invoices');
        }
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblfactures.nom',
                'tblexpediteurs.nom',
                'tblfactures.total_crbt',
                'tblfactures.total_frais',
                'tblfactures.total_net',
                'tblfactures.commentaire',
                'tblfactures.type',
                'tblfactures.status',
                'tblfactures.date_created',
                'tblfactures.id_utilisateur'
            );

            $sIndexColumn = "id";
            $sTable = 'tblfactures';

            $join = array(
                'Left join tblexpediteurs ON tblexpediteurs.id = tblfactures.id_expediteur ',
                'Left join tblstaff ON tblstaff.staffid = tblfactures.id_utilisateur '
            );

            $where = array();
            if (is_numeric($livreurId)) {
                array_push($where, 'AND tblfactures.id_livreur = ' . $livreurId);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblfactures.id', 'tblexpediteurs.id as expediteur_id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur', '(SELECT count(id) FROM tblcolisfacture WHERE facture_id = tblfactures.id) as nbr_colis'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                        $_data = $aRow[$aColumns[$i]];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblfactures.nom') {
                        $_data = '<a href="' . admin_url('factures/facture/' . $aRow['id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['expediteur_id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblfactures.total_crbt' || $aColumns[$i] == 'tblfactures.total_frais' || $aColumns[$i] == 'tblfactures.total_net') {
                        $_data = '<p class="pright30" style="text-align: right;"><span class="label label-default inline-block">' . format_money($_data) . '</span></p>';
                    } else if ($aColumns[$i] == 'tblfactures.commentaire') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    } else if ($aColumns[$i] == 'tblfactures.type') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblfactures.status') {
                        $_data = format_facture_status($_data);
                    } else if ($aColumns[$i] == 'tblfactures.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblfactures.id_utilisateur') {
                        $staffId = $_data;
                        $_data = staff_profile_image($staffId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $staffId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
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
     * List all reclamtions staff
     */
    public function init_activity_log_staff($staffid = false)
    {
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'description',
                'date'
            );

            $sIndexColumn = "id";
            $sTable = 'tblactivitylog';

            $join = array();

            $where = array();
            if (is_numeric($staffid)) {
                array_push($where, 'AND tblactivitylog.staffid = ' . $staffid);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array());
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'date') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
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
     * Total fresh & crbt colis par defaut / ajax chart
     */
    public function default_fresh_crbt_colis_livreur()
    {
        echo json_encode($this->staff_model->default_fresh_crbt_colis_livreur());
    }


   public function checkcomission ()
    {
        $type = $this->input->post('type');
        if ($type && !empty($type)) {
         $success = $this->staff_model->checkcomissionliv($type);
        }

        echo json_encode(array('success' => $success));
    }




}

