<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff_model extends CRM_Model
{

    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete', 'download', 'export');

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get staff member/s
     * @param  mixed $id Optional - staff id
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $active = '', $where = array())
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_int($active)) {
            $this->db->where('active', $active);
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
            return $this->db->get('tblstaff')->row();
        }

        $this->db->order_by('CONCAT(firstname, " ", lastname) ASC');
        return $this->db->get('tblstaff')->result_array();
    }

    /**
     * Get livreur
     * @param  mixed $id Optional - staff id
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get_livreurs($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
     // $this->db->select('CONCAT_WS(" ", firstname," ",lastname) AS nomeliv');

        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
        }
        $this->db->where('id_entreprise', $id_E);
        $this->db->where('admin', 0);
        $this->db->order_by('CONCAT(firstname, " ", lastname) ASC');


        return $this->db->get('tblstaff')->result_array();
    }


    public function get_livreurs_fu($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
      $this->db->select('CONCAT_WS(" ", firstname," ",lastname) AS nomeliv');
        $this->db->select('staffid');
        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
        }
        $this->db->where('id_entreprise', $id_E);
        $this->db->where('admin', 0);
        $this->db->order_by('CONCAT(firstname, " ", lastname) ASC');


        return $this->db->get('tblstaff')->result_array();
    }
    /**
     * Add new staff member
     * @param array $data staff $_POST data
     */
    public function add($data)
    {
        if (isset($data['departments'])) {
            $data['department'] = NULL;
            $data['departments'] =   json_encode($data['departments']);
        }
        else {
            //         $data['rel_id'] = NULL;
            //     $data['rels_id'] = json_encode($data['rels_id']);

        }
        unset($data['custom_view']);
        unset($data['etat']);

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $permissions = array();
        if (isset($data['view'])) {
            $permissions['view'] = $data['view'];
            unset($data['view']);
        }
        if (isset($data['view_own'])) {
            $permissions['view_own'] = $data['view_own'];
            unset($data['view_own']);
        }
        if (isset($data['edit'])) {
            $permissions['edit'] = $data['edit'];
            unset($data['edit']);
        }
        if (isset($data['create'])) {
            $permissions['create'] = $data['create'];
            unset($data['create']);
        }
        if (isset($data['delete'])) {
            $permissions['delete'] = $data['delete'];
            unset($data['delete']);
        }
        if (isset($data['download'])) {
            $permissions['download'] = $data['download'];
            unset($data['download']);
        }
        if (isset($data['export'])) {
            $permissions['export'] = $data['export'];
            unset($data['export']);
        }

        if (!isset($data['point_relai_id']) || !is_numeric($data['point_relai_id'])) {
            unset($data['point_relai_id']);
        }

        if (empty($data['role'])) {
            unset($data['role']);
        }
        if (isset($data['administrator'])) {
            $data['admin'] = $data['administrator'];
            if ($data['admin'] == 1) {
                $data['role'] = NULL;
            }
            unset($data['administrator']);
        }

        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $data['password'] = $hasher->HashPassword($data['password']);

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['id_entreprise'] = $id_E;

        $this->db->insert('tblstaff', $data);
        $staffid = $this->db->insert_id();
        if ($staffid) {
            //Add Permission Module
            $_all_permissions = $this->roles_model->get_permissions();
            foreach ($_all_permissions as $permission) {
                $this->db->insert('tblstaffpermissions', array(
                    'permissionid' => $permission['permissionid'],
                    'staffid' => $staffid,
                    'can_view' => 0,
                    'can_view_own' => 0,
                    'can_edit' => 0,
                    'can_create' => 0,
                    'can_delete' => 0,
                    'can_download' => 0,
                    'can_export' => 0
                ));
            }
            if ($data['admin'] !== 1) {
                foreach ($this->perm_statements as $c) {
                    foreach ($permissions as $key => $p) {
                        if ($key == $c) {
                            foreach ($p as $perm) {
                                $this->db->where('staffid', $staffid);
                                $this->db->where('permissionid', $perm);
                                $this->db->update('tblstaffpermissions', array(
                                    'can_' . $c => 1
                                ));
                            }
                        }
                    }
                }
            } else {
                $this->db->where('staffid', $staffid);
                $this->db->delete('tblstaffpermissions');
            }

            logActivity('Nouveau Utilisateur Ajouté [ID: ' . $staffid . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
            return $staffid;
        }

        return false;
    }

    /**
     * Update staff member info
     * @param  array $data staff data
     * @param  mixed $id   staff id
     * @return boolean
     */
    public function update($data, $id)
    {
        if (isset($data['departments'])) {
            $data['department'] = NULL;
            $data['departments'] =   json_encode($data['departments']);
        }
        else {
   //         $data['rel_id'] = NULL;
       //     $data['rels_id'] = json_encode($data['rels_id']);

        }
        unset($data['custom_view']);
        unset($data['etat']);

        $affectedRows = 0;

        $permissions = array();
        if (isset($data['view'])) {
            $permissions['view'] = $data['view'];
            unset($data['view']);
        }
        if (isset($data['view_own'])) {
            $permissions['view_own'] = $data['view_own'];
            unset($data['view_own']);
        }
        if (isset($data['edit'])) {
            $permissions['edit'] = $data['edit'];
            unset($data['edit']);
        }
        if (isset($data['create'])) {
            $permissions['create'] = $data['create'];
            unset($data['create']);
        }
        if (isset($data['delete'])) {
            $permissions['delete'] = $data['delete'];
            unset($data['delete']);
        }
        if (isset($data['download'])) {
            $permissions['download'] = $data['download'];
            unset($data['download']);
        }
        if (isset($data['export'])) {
            $permissions['export'] = $data['export'];
            unset($data['export']);
        }

        if (!isset($data['point_relai_id']) || !is_numeric($data['point_relai_id'])) {
            unset($data['point_relai_id']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password'] = $hasher->HashPassword($data['password']);
        }

        if (strpos($this->uri->uri_string(), 'edit_profile') === false) {
            if (is_admin() || has_permission('staff', '', 'create')) {
                if (empty($data['role'])) {
                    unset($data['role']);
                }
                if (isset($data['administrator'])) {
                    $data['admin'] = $data['administrator'];
                    if ($data['admin'] == 1) {
                        $data['role'] = NULL;
                    }
                } else {
                    if ($id != get_staff_user_id()) {
                        if ($id == 1) {
                            set_alert('warning', _l('staff_cant_remove_main_admin'));
                            return false;
                        }
                    } else {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                }
            }
        }

        unset($data['administrator']);

        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if (strpos($this->uri->uri_string(), 'edit_profile') === false) {
            if ($this->update_permissions($permissions, $id)) {
                $affectedRows++;
            }

            if (isset($data['admin']) && $data['admin'] == 1) {
                $this->db->where('staffid', $id);
                $this->db->delete('tblstaffpermissions');
            }
        }

        if ($affectedRows > 0) {
            logActivity('Utilisateur Modifié [ID: ' . $id . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Change staff passwordn
     * @param  mixed $data   password data
     * @param  mixed $userid staff id
     * @return mixed
     */
    public function change_password($data, $userid)
    {
        $member = $this->get($userid);
        // CHeck if member is active
        if ($member->active == 0) {
            return array(
                array(
                    'memberinactive' => true
                )
            );
        }

        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        // Check new old password
        if (!$hasher->CheckPassword($data['oldpassword'], $member->password)) {
            return array(
                array(
                    'passwordnotmatch' => true
                )
            );
        }

        $data['newpasswordr'] = $hasher->HashPassword($data['newpasswordr']);

        $this->db->where('staffid', $userid);
        $this->db->update('tblstaff', array(
            'password' => $data['newpasswordr']
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Mot De Passe Utilisateur Changé [' . $userid . ']');
            return true;
        }
        return false;
    }

    /**
     * Change staff status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_staff_status($id, $status)
    {
        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', array(
            'active' => $status
        ));
        logActivity('Statut Utilisateur Changé [ID: ' . $id . ' - Statut(Actif/Inactif): ' . $status . ']');
    }

    public function update_permissions($permissions, $id)
    {
        $all_permissions = $this->roles_model->get_permissions();
        if (total_rows('tblstaffpermissions', array(
                'staffid' => $id
            )) == 0) {
            foreach ($all_permissions as $p) {
                $_ins = array();
                $_ins['staffid'] = $id;
                $_ins['permissionid'] = $p['permissionid'];
                $this->db->insert('tblstaffpermissions', $_ins);
            }
        } else if (total_rows('tblstaffpermissions', array(
                'staffid' => $id
            )) != count($all_permissions)) {
            foreach ($all_permissions as $p) {
                if (total_rows('tblstaffpermissions', array(
                        'staffid' => $id,
                        'permissionid' => $p['permissionid']
                    )) == 0) {
                    $_ins = array();
                    $_ins['staffid'] = $id;
                    $_ins['permissionid'] = $p['permissionid'];
                    $this->db->insert('tblstaffpermissions', $_ins);
                }
            }
        }
        $_permission_restore_affected_rows = 0;
        foreach ($all_permissions as $permission) {
            foreach ($this->perm_statements as $c) {
                $this->db->where('staffid', $id);
                $this->db->where('permissionid', $permission['permissionid']);
                $this->db->update('tblstaffpermissions', array(
                    'can_' . $c => 0
                ));
                if ($this->db->affected_rows() > 0) {
                    $_permission_restore_affected_rows++;
                }
            }
        }
        $_new_permissions_added_affected_rows = 0;
        foreach ($permissions as $key => $val) {
            foreach ($val as $p) {
                $this->db->where('staffid', $id);
                $this->db->where('permissionid', $p);
                $this->db->update('tblstaffpermissions', array(
                    'can_' . $key => 1
                ));
                if ($this->db->affected_rows() > 0) {
                    $_new_permissions_added_affected_rows++;
                }
            }
        }
        if ($_new_permissions_added_affected_rows != $_permission_restore_affected_rows) {
            return true;
        }
    }

    /**
     * Default Total fresh & crbt colis / chart
     * @return array chart data
     */
    public function default_fresh_crbt_colis_livreur()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $livreurid = $this->input->post('livreur');
        $months_report = $this->input->post('months_report');

        if ($months_report != '') {
            if (is_numeric($months_report)) {
                $custom_date_select = 'tblcolis.date_livraison  > DATE_SUB(now(), INTERVAL ' . $months_report . ' MONTH)';
            } else if ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from_1'));
                $to_date = to_sql_date($this->input->post('report_to_1'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'tblcolis.date_livraison  ="' . $from_date . '"';
                } else {
                    $custom_date_select = '(tblcolis.date_livraison  BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            } else if ($months_report == 'yesterday') {
                $custom_date_select = 'tblcolis.date_livraison = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            } else if ($months_report == 'this_week') {
                $custom_date_select = 'WEEK(tblcolis.date_livraison, 1) = WEEK(CURDATE(), 1)';
            } else if ($months_report == 'last_week') {
                $custom_date_select = 'WEEK(tblcolis.date_livraison, 1) = WEEK(CURDATE(), 1) - 1';
            }
        }

        // GET FRAIS COLIS
        $this->db->select('tblcolis.date_livraison as date, tblcolis.frais as frais');
        $this->db->from('tblcolis');
        $this->db->where('tblcolis.id_entreprise', $id_E);
        $this->db->where('tblcolis.status_id', 2);
        $this->db->where('tblcolis.frais <', 100);
        if (is_numeric($livreurid)) {
            $this->db->where('tblcolis.livreur', $livreurid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $frais_colis = $this->db->get()->result_array();

        // GET CRBT COLIS
        $this->db->select('tblcolis.date_livraison as date, tblcolis.crbt as crbt');
        $this->db->from('tblcolis');
        $this->db->where('tblcolis.id_entreprise', $id_E);
        $this->db->where('tblcolis.status_id', 2);
        if (is_numeric($livreurid)) {
            $this->db->where('tblcolis.livreur', $livreurid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $crbt_colis = $this->db->get()->result_array();

        // GET NBR COLIS LIVRE
        $this->db->select('tblcolis.date_livraison as date');
        $this->db->from('tblcolis');
        $this->db->where('tblcolis.id_entreprise', $id_E);
        $this->db->where('tblcolis.status_id', 2);
        if (is_numeric($livreurid)) {
            $this->db->where('tblcolis.livreur', $livreurid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $colis_livrer = $this->db->get()->result_array();

        // GET NBR COLIS RETOURNE
        $this->db->select('tblcolis.date_livraison as date');
        $this->db->from('tblcolis');
        $this->db->where('tblcolis.id_entreprise', $id_E);
        $this->db->where('tblcolis.status_id', 3);
        if (is_numeric($livreurid)) {
            $this->db->where('tblcolis.livreur', $livreurid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $colis_retourner = $this->db->get()->result_array();

        if ($months_report != '') {
            if (is_numeric($months_report)) {
                $custom_date_colis_inprogress = 'tblcolis.date_ramassage  > DATE_SUB(now(), INTERVAL ' . $months_report . ' MONTH)';
            } else if ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from_1'));
                $to_date = to_sql_date($this->input->post('report_to_1'));
                if ($from_date == $to_date) {
                    $custom_date_colis_inprogress = 'tblcolis.date_ramassage  ="' . $from_date . '"';
                } else {
                    $custom_date_colis_inprogress = '(tblcolis.date_ramassage  BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            } else if ($months_report == 'yesterday') {
                $custom_date_colis_inprogress = 'tblcolis.date_ramassage = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            } else if ($months_report == 'this_week') {
                $custom_date_colis_inprogress = 'WEEK(tblcolis.date_ramassage, 1) = WEEK(CURDATE(), 1)';
            } else if ($months_report == 'last_week') {
                $custom_date_colis_inprogress = 'WEEK(tblcolis.date_ramassage, 1) = WEEK(CURDATE(), 1) - 1';
            }
        }

        // GET NBR COLIS IN PROGRESS
        $this->db->select('tblcolis.date_ramassage as date');
        $this->db->from('tblcolis');
        $this->db->where('tblcolis.id_entreprise', $id_E);
        $this->db->where('tblcolis.status_id', 1);
        if (is_numeric($livreurid)) {
            $this->db->where('tblcolis.livreur', $livreurid);
        }
        if (!empty($custom_date_colis_inprogress)) {
            $this->db->where($custom_date_colis_inprogress);
        }
        $colis_in_progress = $this->db->get()->result_array();

        $data = array();
        $data['months'] = array();
        $data['temp_frais'] = array();
        $data['temp_crbt'] = array();
        $data['temp_livrer'] = array();
        $data['temp_retourner'] = array();
        $data['temp_in_progress'] = array();
        $data['total_frais'] = array();
        $data['total_crbt'] = array();
        $data['total_livrer'] = array();
        $data['total_retourner'] = array();
        $data['total_in_progress'] = array();
        $data['labels'] = array();

        $attr = 'm';
        $attr1 = 'F';
        if ($months_report == 'this_day' || $months_report == 'yesterday' || $months_report == 'this_week' || $months_report == 'last_week') {
            $attr = 'd';
            $attr1 = 'l';
        }

        foreach ($frais_colis as $c) {
            $month = date($attr, strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!' . $attr, $month);
            $month = $dateObj->format($attr1);
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        foreach ($crbt_colis as $c) {
            $month = date($attr, strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!' . $attr, $month);
            $month = $dateObj->format($attr1);
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        foreach ($colis_livrer as $c) {
            $month = date($attr, strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!' . $attr, $month);
            $month = $dateObj->format($attr1);
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        foreach ($colis_retourner as $c) {
            $month = date($attr, strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!' . $attr, $month);
            $month = $dateObj->format($attr1);
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        foreach ($colis_in_progress as $c) {
            $month = date($attr, strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!' . $attr, $month);
            $month = $dateObj->format($attr1);
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }

        // GET MONTH FRENCH
        $day_french = get_days_french();
        $month_french = get_month_french();
        foreach ($data['months'] as $month) {
            foreach ($frais_colis as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_frais'][$month][] = $c['frais'];
                }
            }
            if (isset($data['temp_frais'][$month])) {
                $total_frais_colis = array_sum($data['temp_frais'][$month]);
            } else {
                $total_frais_colis = 0;
            }

            foreach ($crbt_colis as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_crbt'][$month][] = $c['crbt'];
                }
            }
            if (isset($data['temp_crbt'][$month])) {
                $total_crbt_colis = array_sum($data['temp_crbt'][$month]);
            } else {
                $total_crbt_colis = 0;
            }

            foreach ($colis_livrer as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_livrer'][$month][] = 1;
                }
            }
            if (isset($data['temp_livrer'][$month])) {
                $total_colis_livrer = array_sum($data['temp_livrer'][$month]);
            } else {
                $total_colis_livrer = 0;
            }

            foreach ($colis_retourner as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_retourner'][$month][] = 1;
                }
            }
            if (isset($data['temp_retourner'][$month])) {
                $total_colis_retourner = array_sum($data['temp_retourner'][$month]);
            } else {
                $total_colis_retourner = 0;
            }

            foreach ($colis_in_progress as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_in_progress'][$month][] = 1;
                }
            }
            if (isset($data['temp_in_progress'][$month])) {
                $total_colis_in_progress = array_sum($data['temp_in_progress'][$month]);
            } else {
                $total_colis_in_progress = 0;
            }

            if ($attr == 'd') {
                foreach ($day_french as $key => $value) {
                    if ($key == $month) {
                        $month = $value;
                    }
                }
            } else {
                foreach ($month_french as $key => $value) {
                    if ($key == $month) {
                        $month = $value;
                    }
                }
            }

            array_push($data['labels'], $month);
            $data['total_frais'][] = $total_frais_colis;
            $data['total_crbt'][] = $total_crbt_colis;
            $data['total_livrer'][] = $total_colis_livrer;
            $data['total_retourner'][] = $total_colis_retourner;
            $data['total_in_progress'][] = $total_colis_in_progress;
        }

        $chart = array(
            'labels' => $data['labels'],
            'datasets' => array(
                array(
                    'label' => 'Frais',
                    'tension' => false,
                    'borderWidth' => 1,
                    'backgroundColor' => "#ff6f00",
                    'borderColor' => "#ff6f00",
                    'data' => $data['total_frais']
                ),
                array(
                    'label' => 'Crbt',
                    'tension' => false,
                    'borderWidth' => 1,
                    'backgroundColor' => "#03a9f4",
                    'borderColor' => "#03a9f4",
                    'data' => $data['total_crbt']
                ),
                array(
                    'label' => 'Livré',
                    'tension' => false,
                    'borderWidth' => 1,
                    'backgroundColor' => "#259b24",
                    'borderColor' => "#259b24",
                    'data' => $data['total_livrer']
                ),
                array(
                    'label' => 'Retourné',
                    'tension' => false,
                    'borderWidth' => 1,
                    'backgroundColor' => "#fc2d42",
                    'borderColor' => "#fc2d42",
                    'data' => $data['total_retourner']
                ),
                array(
                    'label' => 'En cours',
                    'tension' => false,
                    'borderWidth' => 1,
                    'backgroundColor' => "#cccccc",
                    'borderColor' => "#cccccc",
                    'data' => $data['total_in_progress']
                )
            )
        );

        return $chart;
    }

    /**
     * Add point relais
     * @return boolean
     */
    public function add_point_relais($data)
    {
        //Check if point relais already exist with same staff id
        $pointRelaisStaff = total_rows('tblpointrelaisstaff', array('point_relais_id' => $data['point_relais_id'], 'staff_id' => $data['staff_id']));
        if ($pointRelaisStaff > 0) {
            return array('already_exist' => true);
        }

        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert('tblpointrelaisstaff', $data);
        $insertId = $this->db->insert_id();
        if ($insertId) {
            logActivity('Nouveau point relais affecté à l\'utilisateur [ID:' . $insertId . ', Point relais ID : ' . $data['point_relais_id'] . ', Staff ID : ' . $data['staff_id'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Update point relais
     * @return boolean
     */
    public function update_point_relais($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('tblpointrelaisstaff', array('point_relais_id' => $data['point_relais_id'], 'staff_id' => $data['staff_id']));
        if ($this->db->affected_rows() > 0) {
            logActivity('Point relais modifié à l\'utilisateur [ID:' . $data['id'] . ', Point relais ID : ' . $data['point_relais_id'] . ', Staff ID : ' . $data['staff_id'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete point relais
     * @return boolean
     */
    public function delete_point_relais($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblpointrelaisstaff');
        if ($this->db->affected_rows() > 0) {
            logActivity('Point relais Supprimé à l\'utilisateur [ID:' . $id . ']');
            return true;
        }

        return false;
    }

    public function checkcomissionliv($key)
    {
        $this->db->where('livreur',$key);
        $query = $this->db->get('tbllivreurcommisions');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }

}

