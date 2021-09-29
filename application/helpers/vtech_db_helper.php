<?php

/**
 * Check if field is used in table
 * @param  string  $field column
 * @param  string  $table table name to check
 * @param  integer  $id   ID used
 * @return boolean
 */
function is_reference_in_table($field, $table, $id)
{
    $CI = & get_instance();
    $CI->db->where($field, $id);
    $row = $CI->db->get($table)->row();

    if ($row) {
        return true;
    }

    return false;
}

/**
 * Add option in table
 * @since  Version 1.0.1
 * @param string $name  option name
 * @param string $value option value
 */
function add_option($name, $value = '', $id_E)
{

    $CI = & get_instance();

    $exists = total_rows('tbloptions', array(
        'name' => $name, 'id_entreprise' => $id_E
    ));

    if ($exists == 0) {
        $CI->db->insert('tbloptions', array(
            'name' => $name,
            'value' => $value,
            'id_entreprise' => $id_E
        ));
        $insert_id = $CI->db->insert_id();
        if ($insert_id) {
            return true;
        }
        return false;
    }

    return false;
}

/**
 * Get option value
 * @param  string $name Option name
 * @return mixed
 */
function get_option($name)
{
    $CI = & get_instance();
    $CI->load->library('vtech_base');
    return $CI->vtech_base->get_option($name);
}


function getcolisrefusepdf($id = '',$status_reel)
{
    $CI = & get_instance();

        if (is_numeric($id)) {
            $CI->db->where('tbletatcolislivreitems.etat_id', $id);
            $query = $CI->db->get('tbletatcolislivreitems');
            $dt = 0;
            foreach ($query->result() as $row)
            {
                $id_coli = $row->colis_id;
                $array = array('tblcolis.id' => $id_coli);
                $CI->db->where($array);
                $data = $CI->db->get('tblcolis')->row();
                if ( $data->status_reel  == $status_reel)
                    $dt ++;

            }
            return $dt;

        }
        return 0;



    }


/**
 * Get option value from database
 * @param  string $name Option name
 * @return mixed
 */
function update_option($name, $value)
{
    $CI = & get_instance();
    $CI->db->where('name', $name);
    $CI->db->update('tbloptions', array('value' => $value));
    if ($CI->db->affected_rows() > 0) {
        return true;
    }

    return false;
}

/**
 * Delete option
 * @since  Version 1.0.4
 * @param  mixed $id option id
 * @return boolean
 */
function delete_option($id)
{

    $CI = & get_instance();
    $id_E = $CI->session->userdata('staff_user_id_entreprise');
    $CI->db->where('id', $id);
    $CI->db->where('id_entreprise', $id_E);
    $CI->db->delete('tbloptions');

    if ($CI->db->affected_rows() > 0) {
        return true;
    }

    return false;
}

/**
 * Get client option value
 * @param  string $name Option name
 * @return mixed
 */
function get_option_client($name)
{
    $CI = & get_instance();

    if (is_expediteur_logged_in() && total_rows('tbloptionsclient', array('name' => $name, 'client_id' => get_expediteur_user_id())) > 0) {
        $clientId = get_expediteur_user_id();
    } else {
        $clientId = 0;
    }
    $CI->db->where('client_id', $clientId);
    $CI->db->where('name', $name);
    $option = $CI->db->get('tbloptionsclient')->row();
    if ($option) {
        return $option->value;
    }
}

/**
 * Get option value from database
 * @param  string $name Option name
 * @return mixed
 */
function update_option_client($name, $value)
{
    $CI = & get_instance();

    if (is_expediteur_logged_in()) {
        $clientId = get_expediteur_user_id();
        if (total_rows('tbloptionsclient', array('name' => $name, 'client_id' => get_expediteur_user_id())) > 0) {
            $CI->db->where('client_id', $clientId);
            $CI->db->where('name', $name);
            $CI->db->update('tbloptionsclient', array('value' => $value));
            if ($CI->db->affected_rows() > 0) {
                return true;
            }
        } else {
            $id_E = $CI->session->userdata('staff_user_id_entreprise');
            $dataAdded['id_entreprise'] = $id_E;
            $dataAdded['client_id'] = $clientId;
            $dataAdded['name'] = $name;
            $dataAdded['value'] = $value;
            $CI->db->insert('tbloptionsclient', $dataAdded);
            $insertId = $CI->db->insert_id();
            if ($insertId) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Get client option value
 * @param  string $name Option name
 * @return mixed
 */
function get_permission_module($module = '')
{
    $CI = & get_instance();

    if (!empty($module)) {
        $CI->db->where('module', $module);
        $permission = $CI->db->get('tblpermissionsmodules')->row();
        if ($permission) {
            return $permission->display;
        }
    }

    return 0;
}

/**
 * Get staff
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff($userId = '')
{
    if (empty($userId)) {
        $userId = get_staff_user_id();
    }

    $CI = & get_instance();
    $CI->db->where('staffid', $userId);
    $staff = $CI->db->get('tblstaff')->row();
    $result = null;
    if ($staff) {
        $result = $staff;
    }

    return $result;
}

/**
 * Get staff full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $_userid = get_staff_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }

    $CI = & get_instance();
    $CI->db->where('staffid', $_userid);
    $staff = $CI->db->select('firstname,lastname')->from('tblstaff')->get()->row();
    return $staff->firstname . ' ' . $staff->lastname;
}

/**
 * Get staff full name
 * @param  string $clientId Optional
 * @return string Firstname and Lastname
 */
function get_client_full_name($clientId = '')
{
    if (!is_numeric($clientId)) {
        $clientId = get_expediteur_user_id();
    }

    $CI = & get_instance();
    $CI->db->where('id', $clientId);
    $client = $CI->db->select('nom')->from('tblexpediteurs')->get()->row();
    $clientName = '';
    if ($client) {
        $clientName = $client->nom;
    }

    return $clientName;
}

/**
 * Get staff departement
 * @param  string $userid Optional
 * @return string Departement
 */

//here
function get_staff_departement($userid = '')
{
    if (empty($userid)) {
        $userid = get_staff_user_id();
    }

    $CI = & get_instance();
    $CI->db->where('staffid', $userid);
    $staff = $CI->db->select('department')->from('tblstaff')->get()->row();
    $department = 0;
    if ($staff && !is_null($staff->department)) {
        $department = $staff->department;
    }
    return $department;
}

function get_staff_departements($userid = '')
{
    if (empty($userid)) {
        $userid = get_staff_user_id();
    }

    $CI = & get_instance();
    $CI->db->where('staffid', $userid);
    $staff = $CI->db->select('departments')->from('tblstaff')->get()->row();
    $departments = 0;
    if ($staff && !is_null($staff->departments)) {
        $departments = $staff->departments;
        $departments = json_decode($departments);
       $departments = implode(',', $departments);
    }
    return $departments;
}

function get_deli_cities($userid = '')
{
    if (empty($userid)) {
        //$userid = get_staff_user_id();

   return [];
    }

    $CI = & get_instance();
    //$CI->db->where('livreur', $userid);
    //$staff = $CI->db->select('city')->from('tbllivreurcommisions')->row_array();
    $departments = 0;
   // if ($staff && !is_null($staff)) {
     //   $departments = $staff->departments;
      //  $departments = json_decode($departments);
       // $departments = implode(',', $departments);
    //}
    $CI->db->where('livreur', $userid);
    $result = $CI->db->get('tbllivreurcommisions');
    $arr =  $result->result_array();
  //  return $staff;
$cities = array();
    foreach ($arr as $row) {
        array_push($cities,$row['city']);
    }

    return  $cities;
}



/**
 * Log Activity for everything
 * @param  string $description Activity Description
 * @param  integer $staffid    Who done this activity
 */
function logActivity($description, $staffid = NULL, $cron = false)
{
    $CI = & get_instance();

    $log = array(
        'description' => $description,
        'date' => date('Y-m-d H:i:s')
    );

    if ($cron == false) {
        if (!is_null($staffid) && is_numeric($staffid)) {
            $log['staffid'] = $staffid;
        } else {
            if (is_staff_logged_in()) {
                $log['staffid'] = get_staff_user_id();
            } else if (is_expediteur_logged_in()) {
                $clientId = get_expediteur_user_id();
                $CI->db->select('id_entreprise');
                $CI->db->from('tblexpediteurs');
                $CI->db->where('id', $clientId);
                $log['id_entreprise'] = $CI->db->get()->row()->id_entreprise;
            } else {
                $log['staffid'] = NULL;
            }
        }
    } else {
        $log['staffid'] = _l('activity_log_when_cron_job');
    }

    if (is_numeric($log['staffid'])) {
        $CI->db->select('id_entreprise');
        $CI->db->from('tblstaff');
        $CI->db->where('staffid', $log['staffid']);
        $log['id_entreprise'] = $CI->db->get()->row()->id_entreprise;
    }

    $CI->db->insert('tblactivitylog', $log);
}

/**
 * Log Activity for customer
 * @param  string $description Activity Description
 * @param  integer $staffid    Who done this activity
 */
function logActivityCustomer($description, $clientid = NULL)
{
    $CI = & get_instance();

    $log = array(
        'description' => $description,
        'date' => date('Y-m-d H:i:s'),
        'id_entreprise' => 0
    );

    if ($clientid != NULL && is_numeric($clientid)) {
        $log['clientid'] = $clientid;
    } else {
        if (is_expediteur_logged_in()) {
            $log['clientid'] = get_expediteur_user_id();
        } else {
            $log['clientid'] = NULL;
        }
    }

    $CI->db->insert('tblactivitylogcustomer', $log);
}

function add_main_menu_item($options = array(), $parent = '')
{
    $default_options = array('name', 'permission', 'icon', 'url', 'id', 'custom');
    $data = array();
    for ($i = 0; $i < count($default_options); $i++) {
        if (isset($options[$default_options[$i]])) {
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }

    $menu = get_option('aside_menu_active');
    $menu = json_decode($menu);
    // check if the id exists
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }

    $total_exists = 0;
    foreach ($menu->aside_menu_active as $item) {
        if ($item->id == $data['id']) {
            $total_exists++;
        }
    }

    if ($total_exists > 0) {
        $data['id'] = $data['id'] . '-' . ($total_exists + 1);
    }

    if ($parent == '') {
        array_push($menu->aside_menu_active, $data);
    } else {
        $i = 0;
        foreach ($menu->aside_menu_active as $item) {
            if ($item->id == $parent) {
                if (!isset($item->children)) {
                    $menu->aside_menu_active[$i]->children = array();
                    $menu->aside_menu_active[$i]->children[] = $data;
                    break;
                } else {
                    $menu->aside_menu_active[$i]->children[] = $data;
                    break;
                }
            }
            $i++;
        }
    }
    if (update_option('aside_menu_active', json_encode($menu))) {
        return true;
    }

    return false;
}

function add_setup_menu_item($options = array(), $parent = '')
{
    $default_options = array('name', 'permission', 'icon', 'url', 'id', 'custom');
    $data = array();
    for ($i = 0; $i < count($default_options); $i++) {
        if (isset($options[$default_options[$i]])) {
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }

    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }

    $menu = get_option('setup_menu_active');

    $menu = json_decode($menu);

    // check if the id exists
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }

    $total_exists = 0;
    foreach ($menu->setup_menu_active as $item) {
        if ($item->id == $data['id']) {
            $total_exists++;
        }
    }

    if ($total_exists > 0) {
        $data['id'] = $data['id'] . '-' . ($total_exists + 1);
    }

    if ($parent == '') {
        array_push($menu->setup_menu_active, $data);
    } else {
        $i = 0;
        foreach ($menu->setup_menu_active as $item) {
            if ($item->id == $parent) {
                if (!isset($item->children)) {
                    $menu->setup_menu_active[$i]->children = array();
                    $menu->setup_menu_active[$i]->children[] = $data;

                    break;
                } else {
                    $menu->setup_menu_active[$i]->children[] = $data;
                    break;
                }
            }
            $i++;
        }
    }

    if (update_option('setup_menu_active', json_encode($menu))) {
        return true;
    }

    return false;
}

/**
 * Add user notifications
 * @param array $values array of values [description,fromuserid,touserid,fromcompany,isread]
 */
function add_notification($values)
{
    $CI = & get_instance();

    $idEntreprise = get_entreprise_id();
    if(is_null($idEntreprise)) {
        $idEntreprise = 0;
    }
    $data['id_entreprise'] = $idEntreprise;
    $data['date'] = date('Y-m-d H:i:s');

    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }

    if (is_staff_logged_in()) {
        $data['fromuserid'] = get_staff_user_id();
    }

    if (isset($data['fromcompany']) && isset($data['fromuserid'])) {
        unset($data['fromuserid']);
    }

    $CI->db->insert('tblnotifications', $data);
}

/**
 * Get all countries stored in database
 * @return array
 */
function get_all_countries()
{
    $CI = & get_instance();
    return $CI->db->get('tblcountries')->result_array();
}

/**
 * Get country short name by passed id
 * @param  mixed $id county id
 * @return mixed
 */
function get_country_short_name($id)
{

    $CI = &get_instance();
    $CI->db->where('country_id', $id);
    $country = $CI->db->get('tblcountries')->row();

    if ($country) {
        return $country->iso2;
    }

    return '';
}

/**
 * Count total rows on table based on params
 * @param  string $table Table from where to count
 * @param  array  $where
 * @return mixed  Total rows
 */
function total_rows($table, $where = array())
{
    $CI = & get_instance();

    if (is_array($where)) {
        if (sizeof($where) > 0) {
            $CI->db->where($where);
        }
    } else if (strlen($where) > 0) {
        $CI->db->where($where);
    }

    return $CI->db->count_all_results($table);
}

/**
 * Sum frais colis rows on table based on params
 * @return mixed  Total frais
 */
function total_frais_colis()
{
    $CI = & get_instance();
    $CI->db->select('sum(frais) as frais');
    $CI->db->from('tblcolis');
    $CI->db->where('tblcolis.status_id', 2);
    $CI->db->where('tblcolis.date_livraison', date('Y-m-d'));

    $frais = $CI->db->get()->row()->frais;
    if (is_null($frais)) {
        $frais = 0;
    }

    return number_format($frais, 2, ',', ' ');
}

/**
 * Sum price colis rows on table based on params
 * @return mixed  Total frais
 */
function total_price_colis()
{
    $CI = & get_instance();
    $CI->db->select('sum(crbt) as price');
    $CI->db->from('tblcolis');
    $CI->db->where('tblcolis.status_id', 2);
    $CI->db->where('tblcolis.date_livraison', date('Y-m-d'));

    $total = $CI->db->get()->row()->price;
    if (is_null($total)) {
        $total = 0;
    }

    return number_format($total, 2, ',', ' ');
}

/**
 * Sum price colis rows on table based on params
 * @return mixed  Total frais
 */
function total_parrainage()
{
    $CI = & get_instance();
    $CI->db->select('sum(total_colis_parrainage) as nbr_colis');
    $CI->db->from('tblexpediteurs');

    $total = $CI->db->get()->row()->nbr_colis;
    if (is_null($total)) {
        $total = 0;
    } else {
        $total = $total * floatval(get_option('frais_parrainage'));
    }

    return number_format($total, 2, ',', ' ');
}

/**
 * Sum total from table
 * @param  string $table table name
 * @param  array  $attr  attributes
 * @return mixed
 */
function sum_from_table($table, $attr = array(), $where_string = '')
{
    if (!isset($attr['field'])) {
        show_error('sum_from_table(); function expect field to be passed.');
    }
    $where = '';
    if (isset($attr['where']) && is_array($attr['where'])) {
        $i = 0;
        foreach ($attr['where'] as $key => $val) {
            if ($i == 0) {
                $where .= ' WHERE ' . $key . '="' . $val . '"';
            } else {
                $where .= ' AND ' . $key . '="' . $val . '"';
            }
            $i++;
        }
    } else {
        if (!empty($where_string)) {
            $where .= ' WHERE ' . $where_string;
        }
    }
    $CI = & get_instance();
    $result = $CI->db->query('SELECT sum(' . $attr['field'] . ') as total FROM ' . $table . '' . $where . '')->row();
    if (is_null($result->total)) {
        $result->total = 0;
    }

    return $result->total;
}

/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $orderby
 * @return array
 */
function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = array(), $id_E = '', $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '', $orderby_type = '', $countTotal = true)
{
    $CI = & get_instance();
    $__post = $CI->input->post();
    /*
     * Paging
     */
    //$sLimit = "";
    $sLimit = "LIMIT 0, 8000";

    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }

    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);

                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order') && (!$CI->input->post('custom_sort_by') && !$CI->input->post('custom_view'))) {

        $sOrder = "ORDER BY ";
        if ($orderby != '' && empty($__post['order'][0]['column'])) {
            $sOrder .= $orderby;
        } else {
            if ($sTable == 'tblcolis' && $aColumns[intval($__post['order'][0]['column'])] == 'DATE_FORMAT(date_ramassage, "%d/%m/%Y")') {
                $sOrder .= 'date_ramassage';
            } else {
                $sOrder .= $aColumns[intval($__post['order'][0]['column'])];
            }
        }

        $__order_column = $sOrder;
        if (strpos($__order_column, 'as') !== false) {
            $sOrder = strbefore($__order_column, ' as');
        }

        if (empty($orderby_type) && !empty($__post['order'][0]['dir'])) {
            $_order = strtoupper($__post['order'][0]['dir']);
            if ($_order == 'ASC') {
                $sOrder .= ' ASC';
            } else {
                $sOrder .= ' DESC';
            }
        } else {
            $sOrder .= ' ' . $orderby_type;
        }
        $sOrder .= ', ';

        $sOrder = substr_replace($sOrder, "", -2);
        if ($sOrder == "ORDER BY") {
            $sOrder = "";
        }

        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        }
    } else if ($CI->input->post('custom_sort_by')) {
        $sort = $CI->input->post('custom_sort_by');
        if ($sort == 'priority') {
            $sOrder = "ORDER BY CASE Priority
            WHEN 'Urgent' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
            END";
        } else {
            $sOrder = 'ORDER BY ' . $sort . ' DESC';
        }
    } else {
        $sOrder = $orderby;
        if (empty($sOrder)) {
            $sOrder = 'ORDER BY ' . $sTable . '.' . $sIndexColumn . ' DESC ';
        }
    }

    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as ');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $__post['search']['value'] = str_replace(',', '', $__post['search']['value']);
                $sWhere .= $__search_column . ' LIKE "%' . $__post['search']['value'] . '%" OR ';
            }
        }
        /* if(count($additionalSelect) > 0) {
           foreach ($additionalSelect as $searchAdditionalField) {
           $sWhere .= $searchAdditionalField . " LIKE '%" . $__post['search']['value'] . "%' OR ";
           }
           } */

        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $_search = $__post['columns'][$i]['search']['value'];
                if ($_search != '') {

                    $valid_date = (bool) strtotime($_search);

                    if ($valid_date) {
                        $_search = to_sql_date($_search);
                    }

                    $sWhere .= $aColumns[$i] . ' LIKE "%' . $_search . '%" OR ';

                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= $searchAdditionalField . ' LIKE "%' . $_search . '%" OR ';
                        }
                    }
                    $searchFound++;
                }
            }
        }

        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }
    /*
     * SQL queries
     * Get data to display
     */

    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);
    if ($sWhere == '') {
        if (_startsWith($where, 'AND')) {
            $where = substr($where, 3);
            $where = 'AND' . $where;
        }
    } else {
        if (_startsWith($sWhere, 'WHERE') && ($where != '' && _startsWith($where, 'WHERE'))) {
            $where = substr($where, 5);
            $where = 'AND' . $where;
        }
    }

    $NewWhere = " AND ";
    $whereexists = strpos($sWhere, "WHERE");
    if ($whereexists !== false) {

    } else {
        $NewWhere = "WHERE ";
    }

    if ($id_E != 0) {
        $alia_table = $sTable . ".id_entreprise = $id_E ";
    } elseif ($id_E == 0) {
        $alia_table = ' 1 = 1 ';
    }

    $sQuery = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere $NewWhere $alia_table  
    " . $where . "  
    $groupBy
    $sOrder
    $sLimit
    ";
    //var_dump($sQuery); exit();

    $rResult = $CI->db->query($sQuery)->result_array();
    /* Data set length after filtering */
    $sQuery = "
    SELECT FOUND_ROWS()
    ";

    $_query = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];

    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3) . ' AND ';
    } else {
        $where = 'WHERE ';
    }

    /* Total data set length */
    if ($countTotal == true) {
        $sQuery = " SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
                    FROM $sTable " . implode(' ', $join) . ' ' . $where . $alia_table;

        $_query = $CI->db->query($sQuery)->result_array();
        $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    } else {
        $iTotal = 0;
    }
    /*
     * Output
     */
    $output = array(
        "draw" => isset($__post['draw']) ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );


    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}
function data_tables_init12($aColumns, $sIndexColumn, $sTable, $join = array(), $id_E = '', $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '', $orderby_type = '', $countTotal = true)
{
    $CI = & get_instance();
    $__post = $CI->input->post();
    /*
     * Paging
     */
    //$sLimit = "";
    $sLimit = "LIMIT 0, 40";

    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }

    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);

                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order') && (!$CI->input->post('custom_sort_by') && !$CI->input->post('custom_view'))) {

        $sOrder = "ORDER BY ";
        if ($orderby != '' && empty($__post['order'][0]['column'])) {
            $sOrder .= $orderby;
        } else {
            if ($sTable == 'tblcolis' && $aColumns[intval($__post['order'][0]['column'])] == 'DATE_FORMAT(date_ramassage, "%d/%m/%Y")') {
                $sOrder .= 'date_ramassage';
            } else {
                $sOrder .= $aColumns[intval($__post['order'][0]['column'])];
            }
        }

        $__order_column = $sOrder;
        if (strpos($__order_column, 'as') !== false) {
            $sOrder = strbefore($__order_column, ' as');
        }

        if (empty($orderby_type) && !empty($__post['order'][0]['dir'])) {
            $_order = strtoupper($__post['order'][0]['dir']);
            if ($_order == 'ASC') {
                $sOrder .= ' ASC';
            } else {
                $sOrder .= ' DESC';
            }
        } else {
            $sOrder .= ' ' . $orderby_type;
        }
        $sOrder .= ', ';

        $sOrder = substr_replace($sOrder, "", -2);
        if ($sOrder == "ORDER BY") {
            $sOrder = "";
        }

        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        }
    } else if ($CI->input->post('custom_sort_by')) {
        $sort = $CI->input->post('custom_sort_by');
        if ($sort == 'priority') {
            $sOrder = "ORDER BY CASE Priority
            WHEN 'Urgent' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
            END";
        } else {
            $sOrder = 'ORDER BY ' . $sort . ' DESC';
        }
    } else {
        $sOrder = $orderby;
        if (empty($sOrder)) {
            $sOrder = 'ORDER BY ' . $sTable . '.' . $sIndexColumn . ' DESC ';
        }
    }

    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as ');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $__post['search']['value'] = str_replace(',', '', $__post['search']['value']);
                $sWhere .= $__search_column . ' LIKE "%' . $__post['search']['value'] . '%" OR ';
            }
        }
        /* if(count($additionalSelect) > 0) {
           foreach ($additionalSelect as $searchAdditionalField) {
           $sWhere .= $searchAdditionalField . " LIKE '%" . $__post['search']['value'] . "%' OR ";
           }
           } */

        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $_search = $__post['columns'][$i]['search']['value'];
                if ($_search != '') {

                    $valid_date = (bool) strtotime($_search);

                    if ($valid_date) {
                        $_search = to_sql_date($_search);
                    }

                    $sWhere .= $aColumns[$i] . ' LIKE "%' . $_search . '%" OR ';

                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= $searchAdditionalField . ' LIKE "%' . $_search . '%" OR ';
                        }
                    }
                    $searchFound++;
                }
            }
        }

        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }
    /*
     * SQL queries
     * Get data to display
     */

    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);
    if ($sWhere == '') {
        if (_startsWith($where, 'AND')) {
            $where = substr($where, 3);
            $where = 'AND' . $where;
        }
    } else {
        if (_startsWith($sWhere, 'WHERE') && ($where != '' && _startsWith($where, 'WHERE'))) {
            $where = substr($where, 5);
            $where = 'AND' . $where;
        }
    }

    $NewWhere = " AND ";
    $whereexists = strpos($sWhere, "WHERE");
    if ($whereexists !== false) {

    } else {
        $NewWhere = "WHERE ";
    }

    if ($id_E != 0) {
        $alia_table = $sTable . ".id_entreprise = $id_E ";
    } elseif ($id_E == 0) {
        $alia_table = ' 1 = 1 ';
    }

    $sQuery = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere $NewWhere $alia_table  
    " . $where . "  
    $groupBy
    $sOrder
    $sLimit
    ";
    //var_dump($sQuery); exit();

    $rResult = $CI->db->query($sQuery)->result_array();
    /* Data set length after filtering */
    $sQuery = "
    SELECT FOUND_ROWS()
    ";

    $_query = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];

    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3) . ' AND ';
    } else {
        $where = 'WHERE ';
    }

    /* Total data set length */
    if ($countTotal == true) {
        $sQuery = " SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
                    FROM $sTable " . implode(' ', $join) . ' ' . $where . $alia_table;

        $_query = $CI->db->query($sQuery)->result_array();
        $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    } else {
        $iTotal = 0;
    }
    /*
     * Output
     */
    $output = array(
        "draw" => isset($__post['draw']) ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );


    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}



function data_tables_init_demands($aColumns, $sIndexColumn, $sTable, $join = array(), $id_E = '', $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '', $orderby_type = '', $countTotal = true)
{
    $CI = & get_instance();
    $__post = $CI->input->post();
    /*
     * Paging
     */
    //$sLimit = "";
    $sLimit = "LIMIT 0, 8000";

    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }

    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);

                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order') && (!$CI->input->post('custom_sort_by') && !$CI->input->post('custom_view'))) {

        $sOrder = "ORDER BY ";
        if ($orderby != '' && empty($__post['order'][0]['column'])) {
            $sOrder .= $orderby;
        } else {
            if ($sTable == 'tblcolis' && $aColumns[intval($__post['order'][0]['column'])] == 'DATE_FORMAT(date_ramassage, "%d/%m/%Y")') {
                $sOrder .= 'date_ramassage';
            } else {
                $sOrder .= $aColumns[intval($__post['order'][0]['column'])];
            }
        }

        $__order_column = $sOrder;
        if (strpos($__order_column, 'as') !== false) {
            $sOrder = strbefore($__order_column, ' as');
        }

        if (empty($orderby_type) && !empty($__post['order'][0]['dir'])) {
            $_order = strtoupper($__post['order'][0]['dir']);
            if ($_order == 'ASC') {
                $sOrder .= ' ASC';
            } else {
                $sOrder .= ' DESC';
            }
        } else {
            $sOrder .= ' ' . $orderby_type;
        }
        $sOrder .= ', ';

        $sOrder = substr_replace($sOrder, "", -2);
        if ($sOrder == "ORDER BY") {
            $sOrder = "";
        }

        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        }
    } else if ($CI->input->post('custom_sort_by')) {
        $sort = $CI->input->post('custom_sort_by');
        if ($sort == 'priority') {
            $sOrder = "ORDER BY CASE Priority
            WHEN 'Urgent' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
            END";
        } else {
            $sOrder = 'ORDER BY ' . $sort . ' DESC';
        }
    } else {
        $sOrder = $orderby;
        if (empty($sOrder)) {
            $sOrder = 'ORDER BY ' . $sTable . '.' . $sIndexColumn . ' DESC ';
        }
    }

    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $result = preg_match("#^PWC(.*)$#i", $__post['search']['value']);
if ($result)
{
    $__post['search']['value'] = search_coli_on_demand($__post['search']['value']);
}
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as ');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $__post['search']['value'] = str_replace(',', '', $__post['search']['value']);
                $sWhere .= $__search_column . ' LIKE "%' . $__post['search']['value'] . '%" OR ';
            }
        }
      if(count($additionalSelect) > 0) {
          foreach ($additionalSelect as $searchAdditionalField) {
          $sWhere .= $searchAdditionalField . " LIKE '%" . $__post['search']['value'] . "%' OR ";
          }
          }

        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $_search = $__post['columns'][$i]['search']['value'];
                if ($_search != '') {

                    $valid_date = (bool) strtotime($_search);

                    if ($valid_date) {
                        $_search = to_sql_date($_search);
                    }

                    $sWhere .= $aColumns[$i] . ' LIKE "%' . $_search . '%" OR ';

                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= $searchAdditionalField . ' LIKE "%' . $_search . '%" OR ';
                        }
                    }
                    $searchFound++;
                }
            }
        }

        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }
    /*
     * SQL queries
     * Get data to display
     */

    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);
    if ($sWhere == '') {
        if (_startsWith($where, 'AND')) {
            $where = substr($where, 3);
            $where = 'AND' . $where;
        }
    } else {
        if (_startsWith($sWhere, 'WHERE') && ($where != '' && _startsWith($where, 'WHERE'))) {
            $where = substr($where, 5);
            $where = 'AND' . $where;
        }
    }

    $NewWhere = " AND ";
    $whereexists = strpos($sWhere, "WHERE");
    if ($whereexists !== false) {

    } else {
        $NewWhere = "WHERE ";
    }

    if ($id_E != 0) {
        $alia_table = $sTable . ".id_entreprise = $id_E ";
    } elseif ($id_E == 0) {
        $alia_table = ' 1 = 1 ';
    }

    $sQuery = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere $NewWhere $alia_table  
    " . $where . "  
    $groupBy
    $sOrder
    $sLimit
    ";
    //var_dump($sQuery); exit();

    $rResult = $CI->db->query($sQuery)->result_array();
    /* Data set length after filtering */
    $sQuery = "
    SELECT FOUND_ROWS()
    ";

    $_query = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];

    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3) . ' AND ';
    } else {
        $where = 'WHERE ';
    }

    /* Total data set length */
    if ($countTotal == true) {
        $sQuery = " SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
                    FROM $sTable " . implode(' ', $join) . ' ' . $where . $alia_table;

        $_query = $CI->db->query($sQuery)->result_array();
        $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    } else {
        $iTotal = 0;
    }
    /*
     * Output
     */
    $output = array(
        "draw" => isset($__post['draw']) ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );


    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}

/**
 * Prefix field name with table ex. table.column
 * @param  string $table
 * @param  string $alias
 * @param  string $field field to check
 * @return string
 */
function prefixed_table_fields_wildcard($table, $alias, $field)
{
    $CI = & get_instance();

    $columns = $CI->db->query("SHOW COLUMNS FROM $table")->result_array();

    $field_names = array();
    foreach ($columns as $column) {
        $field_names[] = $column["Field"];
    }

    $prefixed = array();
    foreach ($field_names as $field_name) {
        if ($field == $field_name) {
            $prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
        }
    }

    return implode(", ", $prefixed);
}

/**
 * Get expediteur default language
 * @param  mixed $clientid
 * @return mixed
 */
function get_expediteur_default_language($expediteurid = '')
{

    if (!is_numeric($expediteurid)) {
        $expediteurid = get_expediteur_user_id();
    }

    $CI = &get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblexpediteurs');
    $CI->db->where('id', $expediteurid);
    $expediteur = $CI->db->get()->row();
    if ($expediteur) {
        return $expediteur->default_language;
    }
    return '';
}

/**
 * Get staff default language
 * @param  mixed $staffId
 * @return mixed
 */
function get_staff_default_language($staffId = '')
{
    if (!is_numeric($staffId)) {
        $staffId = get_staff_user_id();
    }

    $CI = &get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblstaff');
    $CI->db->where('staffid', $staffId);
    $staff = $CI->db->get()->row();
    if ($staff) {
        return $staff->default_language;
    }

    return '';
}

/**
 * Get client default language
 * @param  mixed $clientId
 * @return mixed
 */
function get_client_default_language($clientId = '')
{
    if (!is_numeric($clientId)) {
        $clientId = get_expediteur_user_id();
    }

    $CI = &get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblexpediteurs');
    $CI->db->where('id', $clientId);
    $client = $CI->db->get()->row();
    if ($client) {
        return $client->default_language;
    }

    return '';
}

/**
 * Function that will parse filters for datatables and will return based on a couple conditions.
 * The returned result will be pushed inside the $where variable in the table SQL
 * @param  array $filter
 * @return string
 */
function prepare_dt_filter($filter)
{
    $filter = implode(' ', $filter);
    if (_startsWith($filter, 'AND')) {
        $filter = substr($filter, 3);
    } else if (_startsWith($filter, 'OR')) {
        $filter = substr($filter, 2);
    }
    return $filter;
}

/**
 * Prefix all columns from table with the table name
 * Used for select statements
 * @param  string $table table name
 * @return array
 */
function prefixed_table_fields_array($table)
{
    $CI = & get_instance();
    $fields = $CI->db->list_fields($table);
    $i = 0;
    foreach ($fields as $f) {
        $fields[$i] = $table . '.' . $f;
        $i++;
    }
    return $fields;
}

/**
 * Sum total from table
 * @param  string $table table name
 * @param  array  $attr  attributes
 * @return mixed
 */
function new_sum_from_table($table, $attr = array())
{
    if (!isset($attr['field'])) {
        show_error('sum_from_table(); function expect field to be passed.');
    }

    $CI = & get_instance();
    if (isset($attr['where']) && is_array($attr['where'])) {
        $i = 0;
        foreach ($attr['where'] as $key => $val) {
            if (is_numeric($key)) {
                $CI->db->where($val);
                unset($attr['where'][$key]);
            }
            $i++;
        }
        $CI->db->where($attr['where']);
    }
    $CI->db->select_sum($attr['field']);
    $CI->db->from($table);
    $result = $CI->db->get()->row();
    return $result->{$attr['field']};
}

/**
 * Get client id by code barre colis
 * @param  mixed $code barre
 * @return mixed client id
 */
function get_client_id($code_barre)
{
    $CI = & get_instance();
    $CI->db->select('id_expediteur')->from('tblcolis')->where('code_barre', $code_barre);
    return $CI->db->get()->row()->id_expediteur;
}

/**
 * Get client frais refusé by client id
 * @param  mixed $client id
 * @return decimale
 */
function get_client_frais_refuse($clientId)
{
    $fraisRefuse = 0;
    if (is_numeric($clientId)) {
        $CI = & get_instance();
        $CI->db->where('id', $clientId);
        $client = $CI->db->get('tblexpediteurs')->row();
        if ($client) {
            $fraisRefuse = $client->frais_refuse;
        }
    }

    return floatval(number_format($fraisRefuse, 2, '.', ''));
}

/**
 * Get status name by status id
 * @param  mixed $id status id
 * @return mixed status name
 */
function get_status_name($id)
{
    $CI = & get_instance();
    $CI->db->select('name')->from('tblstatuscolis')->where('id', $id);
    return $CI->db->get()->row()->name;
}

/**
 * Get status name by status id
 * @param  mixed $id status id
 * @return mixed status name
 */
function get_status_color($id)
{
    $CI = & get_instance();
    $CI->db->select('color')->from('tblstatuscolis')->where('id', $id);
    return $CI->db->get()->row()->color;
}

/**
 * Get status location by status id
 * @param  mixed $id status id
 * @return mixed location name
 */
function get_location_name($id)
{
    $CI = & get_instance();
    $CI->db->select('name')->from('tbllocations')->where('id', $id);
    return $CI->db->get()->row()->name;
}

/**
 * Add customer notifications
 * @param array $values array of values [code_barre, description, toclientid, isread, addedfrom]
 */
function add_notification_customer($values)
{
    $CI = & get_instance();

    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }

    $data['addedfrom'] = get_staff_user_id();
    $data['date'] = date('Y-m-d H:i:s');
    $data['id_entreprise'] = get_entreprise_id();
    $CI->db->insert('tblnotificationscustomer', $data);
}

/**
 * Get prefix client by client id
 * @param  mixed $id client id
 * @return mixed prefix
 */
function get_prefix_client($id = '')
{
    if (!$id && is_expediteur_logged_in()) {
        $id = get_expediteur_user_id();
    }

    $CI = & get_instance();
    $CI->db->select('prefix')->from('tblexpediteurs')->where('id', $id);
    $client = $CI->db->get()->row();

    $prefix = '';
    if (!is_null($client)) {
        $prefix = $client->prefix;
    }

    return $prefix;
}

/**
 * Add admin notifications
 * @param array $values array of values [description,fromclientid,toadmin,isread]
 */
function add_notification_to_admin($values)
{
    $CI = & get_instance();

    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }

    if (isset($values['fromclientid']) && is_numeric($values['fromclientid'])) {
        $data['fromclientid'] = $values['fromclientid'];
    } else {
        $data['fromclientid'] = get_expediteur_user_id();
        if (!is_numeric($data['fromclientid'])) {
            $data['fromclientid'] = 0;
        }
    }

    $data['id_entreprise'] = 0;
    $data['toadmin'] = 1;
    $data['date'] = date('Y-m-d H:i:s');

    $CI->db->insert('tblnotificationsadmin', $data);
}

/**
 * Add number of authentication
 * @param array $values array of values [clientid,address_ip]
 */
function add_number_of_authentication($values)
{
    $CI = & get_instance();

    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }

    $data['date_created'] = date('Y-m-d H:i:s');

    $CI->db->insert('tblnumberofauthentication', $data);
}

/**
 * Update online Staff
 * @param  integer  $id   ID used
 */
function update_online_staff($id, $online = 1)
{
    $CI = & get_instance();
    $CI->db->where('staffid', $id);
    $CI->db->update('tblstaff', array('online' => $online));
}

/**
 * Update online Client
 * @param  integer  $id   ID used
 */
function update_online_client($id, $online = 1)
{
    $CI = & get_instance();
    $CI->db->where('id', $id);
    $CI->db->update('tblexpediteurs', array('online' => $online));
}

/**
 * Get status colis
 * @param  boolean
 */
function get_status_colis_before_status_returned($barcode)
{
    $CI = & get_instance();
    $CI->db->where('code_barre', $barcode);
    $CI->db->order_by('id', 'desc');
    $CI->db->limit(2);
    $statuses = $CI->db->get('tblstatus')->result_array();
    if ($statuses) {
        foreach ($statuses as $key => $status) {
            if ($key == 1 && ($status['type'] == 9 || $status['type'] == 13)) {
                return $status['type'];
            }
        }
    }

    return false;
}

/**
 * Delete status returned colis
 * @param  boolean
 */
function remove_last_status_returned_affected_to_colis($barcode)
{
    $CI = & get_instance();
    $CI->db->where('code_barre', $barcode);
    $CI->db->order_by('id', 'desc');
    $CI->db->limit(1);
    $statuses = $CI->db->get('tblstatus')->result_array();
    if ($statuses) {
        foreach ($statuses as $key => $status) {
            if ($key == 0 && $status['type'] == 3) {
                $CI->db->where('code_barre', $barcode);
                $CI->db->where('type', 3);
                $CI->db->delete('tblstatus');

                return true;
            }
        }
    }

    return false;
}

/**
 * Get expediteur default language
 * @param  mixed $clientid
 * @return mixed
 */
function get_expediteur_nbr_colis($expediteurid = '')
{

    if (!is_numeric($expediteurid)) {
        return date('dmYhis');
    }

    $CI = &get_instance();
    $CI->db->select('nbr_colis');
    $CI->db->from('tblexpediteurs');
    $CI->db->where('id', $expediteurid);
    $expediteur = $CI->db->get()->row();
    if ($expediteur) {
        return $expediteur->nbr_colis;
    }

    return date('dmYhis');
}

/**
 * Get expediteur default language
 * @param  mixed $clientid
 * @return mixed
 */
function update_expediteur_nbr_colis($expediteurId, $lastIdColi)
{

    if (is_numeric($expediteurId) && is_numeric($lastIdColi)) {
        $CI = &get_instance();
        $CI->db->where('id', $expediteurId);
        $CI->db->update('tblexpediteurs', array('nbr_colis' => $lastIdColi));
        if ($CI->db->affected_rows() > 0) {
            return true;
        }
    }

    return false;
}

function get_nbr_coli_by_expediteur($idExpediteur)
{
    $CI = & get_instance();
    //Generate last id next coli
    if (is_null(get_expediteur_nbr_colis($idExpediteur))) {
        $CI->load->model('colis_model');
        $lastIdColi = (int) $CI->colis_model->get_count_coli_by_expediteur($idExpediteur) + 1;
    } else {
        $lastIdColi = (int) get_expediteur_nbr_colis($idExpediteur) + 1;
    }

    //Update expediteur last colis id
    $success = update_expediteur_nbr_colis($idExpediteur, $lastIdColi);

    return $lastIdColi;
}

function get_solde_sms($smsPremium = false)
{
    if ($smsPremium) {
        $token = get_option('sms_premium_token_api');
    } else {
        $token = get_option('sms_token_api');
    }

    if (!empty($token)) {
        $data['token'] = $token;
        // Envoie Data
        $url = get_option('sms_solde_base_url_api');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . '?token=' . $token);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        $_result = json_decode($result);
        if ($_result && $_result->success && $_result->success == 1) {
            return $_result->solde;
        }
    }

    return '';
}

function send_sms_to_recipient($phoneNumber, $message, $smsPremium = false)
{
    if (get_option('sms_active') == 1) {
        if ($smsPremium) {
            $token = get_option('sms_premium_token_api');
        } else {
            $token = get_option('sms_token_api');
        }

        //Data
        if (!empty($token) && !empty($phoneNumber) && !empty($message)) {
            if ($smsPremium) {
                $data['shortcode'] = 'SEM';
            }
            $data['token'] = $token;
            $data['tel'] = $phoneNumber;
            $data['message'] = $message;
            // Envoie Data
            $url = get_option('sms_base_url_api');
            $dataString = http_build_query($data);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curl);
            curl_close($curl);

            $_result = json_decode($result);
            if ($_result && $_result->success && $_result->success == 1) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Get colis
 * @param  string $colisId Optional
 * @return string Firstname and Lastname
 */
function get_colis($colisId, $select = '')
{
    if (is_numeric($colisId)) {
        $CI = & get_instance();
        $CI->db->where('id', $colisId);
        $colis = $CI->db->get('tblcolis')->row();
        if ($colis) {
            if (!empty($select)) {
                return $colis->$select;
            } else {
                return $colis;
            }
        }
    }

    return null;
}

/**
 * Get facture
 * @param  string $factureId Optional
 * @return string Firstname and Lastname
 */
function get_facture($factureId, $select = '')
{
    if (is_numeric($factureId)) {
        $CI = & get_instance();
        $CI->db->where('id', $factureId);
        $facture = $CI->db->get('tblfactures')->row();
        if ($facture) {
            if (!empty($select)) {
                return $facture->$select;
            } else {
                return $facture;
            }
        }
    }

    return null;
}

function get_demande($demandId, $select = '')
{
    if (is_numeric($demandId)) {
        $CI = & get_instance();
        $CI->db->where('id', $demandId);
        $demand = $CI->db->get('tbldemandes')->row();
        if ($demand) {
            if (!empty($select)) {
                return $demand->$select;
            } else {
                return $demand;
            }
        }
    }

    return null;
}



/**
 * Log Activity SMS
 * @param  string $sms Contenu Sms
 * @param  integer $staffid    Who done this activity
 */
function logActivitySms($barcode, $statusId, $sms, $sent = 0)
{
    $CI = & get_instance();

    $dataLog = array(
        'code_barre' => $barcode,
        'status_id' => $statusId,
        'sms' => $sms,
        'sent' => $sent,
        'date' => date('Y-m-d H:i:s'),
        'id_entreprise' => get_entreprise_id()
    );

    $CI->db->insert('tblsmsactivitylog', $dataLog);
}

