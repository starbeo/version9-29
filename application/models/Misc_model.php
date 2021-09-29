<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Misc_model extends CRM_Model
{

    public $notifications_limit = 15;

    function __construct()
    {
        parent::__construct();
    }

    public function get_taxes_dropdown_template_other_expenses($name, $taxid)
    {
        $this->load->model('taxes_model');
        $taxes = $this->taxes_model->get();
        $select = '<select class="selectpicker display-block tax-other-expenses" data-width="100%" name="' . $name . '">';
        $_no_tax_selected = '';
        if (!$taxid) {
            $_no_tax_selected = 'selected';
        }
        $select .= '<option value="0" ' . $_no_tax_selected . ' data-taxrate="0">' . _l('no_tax') . '</option>';
        foreach ($taxes as $tax) {
            $selected = '';
            if ($taxid == $tax['id']) {
                $selected = 'selected';
            }
            $select .= '<option value="' . $tax['id'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';
        return $select;
    }

    /**
     * Get current user notifications
     * @param  boolean $read include and readed notifications
     * @return array
     */
    public function get_user_notifications($read = 1)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $total = 40;
        $total_unread = total_rows('tblnotifications', array(
            'isread' => $read,
            'touserid' => get_staff_user_id(),
            'id_entreprise' => $id_E
        ));
        if (is_numeric($read)) {
            $this->db->where('isread', $read);
        }
        if ($total_unread > $total) {
            $_diff = $total_unread - $total;
            $total = $_diff + $total;
        }

        $this->db->where('id_entreprise', $id_E);
        $this->db->where('touserid', get_staff_user_id());
        $this->db->limit($total);
        $this->db->order_by('date', 'desc');
        return $this->db->get('tblnotifications')->result_array();
    }

    /**
     * Get current admin notifications client
     * @param  boolean $read include and readed notifications
     * @return array
     */
    public function get_notifications_client($read = 1)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if (!is_numeric($read)) {
            $read = 0;
        }
        $this->db->where('isread', $read);
        $this->db->where('id_entreprise', $id_E);
        $this->db->order_by('date', 'desc');
        $this->db->limit(100);
        $notifications = $this->db->get('tblnotificationsadmin')->result_array();
        if (count($notifications) == 0) {
            $this->db->where('id_entreprise', $id_E);
            $this->db->order_by('date', 'desc');
            $this->db->limit(50);
            $notifications = $this->db->get('tblnotificationsadmin')->result_array();
        }

        return $notifications;
    }

    /**
     * Get current notifications staff
     * @param  boolean $read include and readed notifications
     * @return array
     */
    public function get_notifications_staff($read = 1)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if (!is_numeric($read)) {
            $read = 0;
        }
        $this->db->where('isread', $read);
        $this->db->where('id_entreprise', $id_E);
        $this->db->order_by('date', 'desc');
        $this->db->limit(100);
        $notifications = $this->db->get('tblnotifications')->result_array();

        if (count($notifications) == 0) {
            $this->db->where('id_entreprise', $id_E);
            $this->db->order_by('date', 'desc');
            $this->db->limit(50);
            $notifications = $this->db->get('tblnotifications')->result_array();
        }

        return $notifications;
    }

    /**
     * Get current user all notifications
     * @param  mixed $page page number / ajax request
     * @return array
     */
    public function get_all_user_notifications($page)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');


        $offset = ($page * $this->notifications_limit);
        $this->db->limit($this->notifications_limit, $offset);
        $this->db->where('touserid', get_staff_user_id());

        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        $this->db->order_by('date', 'desc');
        $notifications = $this->db->get('tblnotifications')->result_array();
        $i = 0;
        foreach ($notifications as $notification) {
            if ($notification['fromcompany'] == NULL) {
                $notifications[$i]['profile_image'] = '<a href="' . admin_url('staff/profile/' . $notification['fromuserid']) . '">' . staff_profile_image($notification['fromuserid'], array(
                        'staff-profile-image-small',
                        'img-circle',
                        'pull-left'
                    )) . '</a>';
            } else {
                $notifications[$i]['profile_image'] = '';
            }
            $notifications[$i]['date'] = time_ago($notification['date']);
            $i++;
        }

        return $notifications;
    }

    /**
     * Get current client all notifications
     * @param  mixed $page page number / ajax request
     * @return array
     */
    public function get_all_notifications_client($clientId, $page)
    {
        $offset = ($page * $this->notifications_limit);

        $this->db->where('toclientid', $clientId);
        $this->db->order_by('date', 'desc');
        $this->db->limit($this->notifications_limit, $offset);
        $notifications = $this->db->get('tblnotificationscustomer')->result_array();
        $i = 0;
        foreach ($notifications as $notification) {
            $notifications[$i]['link'] = str_replace("expediteurs/colis", "client/colis/index", $notification['link']);
            $notifications[$i]['date'] = time_ago($notification['date']);
            $i++;
        }

        return $notifications;
    }

    /**
     * Set notification clients read when user open notification dropdown
     * @return boolean
     */
    public function set_notifications_clients_read()
    {
        $this->db->where('toadmin', get_staff_user_id());
        $this->db->update('tblnotificationsadmin', array('isread' => 1));
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Set notification staffs read when user open notification dropdown
     * @return boolean
     */
    public function set_notifications_staffs_read()
    {
        $this->db->where('touserid', get_staff_user_id());
        $this->db->update('tblnotifications', array('isread' => 1));
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Set notification read when user open notification dropdown
     * @return boolean
     */
    public function set_notifications_client_read()
    {
        $this->db->where('toclientid', get_expediteur_user_id());
        $this->db->update('tblnotificationscustomer', array('isread' => 1));
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Perform search on top header
     * @param  string $q search
     * @return array    search results
     */
    public function perform_search($q)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $result = array();
        $limit = get_option('limit_top_search_bar_results_to');

        // Staff
        $query_staff = $this->db->query('SELECT * FROM `tblstaff` WHERE `id_entreprise` = ' . $id_E . ' AND  ( `firstname` LIKE "%' . $q . '%" OR  `lastname` LIKE "%' . $q . '%" OR  `phonenumber` LIKE "%' . $q . '%" OR  `email` LIKE "%' . $q . '%" ) LIMIT ' . $limit);

        $result['staff'] = $query_staff->result_array();

        // Expediteurs
        $query_expediteurs = $this->db->query('SELECT * FROM `tblexpediteurs` WHERE `id_entreprise` = ' . $id_E . ' AND  ( `nom` LIKE "%' . $q . '%" OR  `email` LIKE "%' . $q . '%" OR  `telephone` LIKE "%' . $q . '%" OR  `adresse` LIKE "%' . $q . '%" OR  `ville` LIKE "%' . $q . '%" ) LIMIT ' . $limit);

        $result['expediteurs'] = $query_expediteurs->result_array();

        // Colis
        $query_colis = $this->db->query('SELECT * FROM `tblcolis` WHERE `id_entreprise` = ' . $id_E . ' AND  ( `code_barre` LIKE "%' . $q . '%" OR  `nom_complet` LIKE "%' . $q . '%" OR  `crbt` LIKE "%' . $q . '%" OR  `telephone` LIKE "%' . $q . '%" OR  `ville` LIKE "%' . $q . '%" OR  `date_ramassage` LIKE "%' . $q . '%" OR  `date_livraison` LIKE "%' . $q . '%" ) LIMIT ' . $limit);

        $result['colis'] = $query_colis->result_array();

        return $result;
    }
    
    /**
     * Perform search on top header
     * @param  string $q search
     * @return array    search results
     */
    public function perform_search_point_relais($q)
    {
        $result = array();
        $limit = get_option('limit_top_search_bar_results_to');

        //Get points relais staff
        $pointsRelaisStaff = get_staff_points_relais();
        
        // Colis
        $queryColis = $this->db->query('SELECT * FROM `tblcolis` WHERE point_relai_id IN ' . $pointsRelaisStaff . ' AND `id_entreprise` = ' . get_entreprise_id() . ' AND  ( `code_barre` LIKE "%' . $q . '%" OR  `nom_complet` LIKE "%' . $q . '%" OR  `crbt` LIKE "%' . $q . '%" OR  `telephone` LIKE "%' . $q . '%" OR  `ville` LIKE "%' . $q . '%" OR  `date_ramassage` LIKE "%' . $q . '%" OR  `date_livraison` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['colis'] = $queryColis->result_array();

        // Bons livraison
        $queryBonsLivraison = $this->db->query('SELECT * FROM `tblbonlivraison` WHERE point_relai_id IN ' . $pointsRelaisStaff . ' AND `id_entreprise` = ' . get_entreprise_id() . ' AND  ( `nom` LIKE "%' . $q . '%" OR `date_created` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['bons_livraison'] = $queryBonsLivraison->result_array();

        // Etats colis livrer
        $queryEtatsColisLivrer = $this->db->query('SELECT * FROM `tbletatcolislivre` WHERE point_relai_id IN ' . $pointsRelaisStaff . ' AND `id_entreprise` = ' . get_entreprise_id() . ' AND  ( `nom` LIKE "%' . $q . '%" OR `date_created` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['etats_colis_livrer'] = $queryEtatsColisLivrer->result_array();

        // Demandes
        $queryDemandes = $this->db->query('SELECT * FROM `tbldemandes` WHERE addedfrom = ' . get_staff_user_id() . ' AND `id_entreprise` = ' . get_entreprise_id() . ' AND  ( `name` LIKE "%' . $q . '%" OR `datecreated` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['demandes'] = $queryDemandes->result_array();

        
        return $result;
    }
    
    /**
     * Perform search client on top header
     * @param  string $q search
     * @return array    search results
     */
    public function perform_search_client($q)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $result = array();
        $limit = get_option_client('limit_top_search_bar_results_to');

        // Colis
        $queryColis = $this->db->query('SELECT * FROM `tblcolis` WHERE id_expediteur = ' . get_expediteur_user_id() . ' AND `id_entreprise` = ' . $id_E . ' AND  ( `code_barre` LIKE "%' . $q . '%" OR `num_commande` LIKE "%' . $q . '%" OR `nom_complet` LIKE "%' . $q . '%" OR `crbt` LIKE "%' . $q . '%" OR `telephone` LIKE "%' . $q . '%" OR `date_ramassage` LIKE "%' . $q . '%" OR `date_livraison` LIKE "%' . $q . '%" OR `date_retour` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['colis'] = $queryColis->result_array();

        // Colis en attente
        $queryColisEnAttente = $this->db->query('SELECT * FROM `tblcolisenattente` WHERE id_expediteur = ' . get_expediteur_user_id() . ' AND `id_entreprise` = ' . $id_E . ' AND  ( `code_barre` LIKE "%' . $q . '%" OR `num_commande` LIKE "%' . $q . '%" OR `nom_complet` LIKE "%' . $q . '%" OR `crbt` LIKE "%' . $q . '%" OR `telephone` LIKE "%' . $q . '%" OR `date_creation` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['colis_en_attente'] = $queryColisEnAttente->result_array();

        // Bons livraison
        $queryBonsLivraison = $this->db->query('SELECT * FROM `tblbonlivraisoncustomer` WHERE id_expediteur = ' . get_expediteur_user_id() . ' AND `id_entreprise` = ' . $id_E . ' AND  ( `nom` LIKE "%' . $q . '%" OR `date_created` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['bons_livraison'] = $queryBonsLivraison->result_array();

        // Factures
        $queryFactures = $this->db->query('SELECT * FROM `tblfactures` WHERE id_expediteur = ' . get_expediteur_user_id() . ' AND `id_entreprise` = ' . $id_E . ' AND  ( `nom` LIKE "%' . $q . '%" OR `total_crbt` LIKE "%' . $q . '%" OR `total_frais` LIKE "%' . $q . '%" OR `total_refuse` LIKE "%' . $q . '%" OR `total_remise` LIKE "%' . $q . '%"  OR `total_net` LIKE "%' . $q . '%"  OR `date_created` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['factures'] = $queryFactures->result_array();

        // Demandes
        $queryDemandes = $this->db->query('SELECT * FROM `tbldemandes` WHERE client_id = ' . get_expediteur_user_id() . ' AND `id_entreprise` = ' . $id_E . ' AND  ( `name` LIKE "%' . $q . '%" OR `message` LIKE "%' . $q . '%" OR `datecreated` LIKE "%' . $q . '%" ) LIMIT ' . $limit);
        $result['demandes'] = $queryDemandes->result_array();


        return $result;
    }

    public function get_activities_staff()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if (!is_admin()) {
            $this->db->where('staffid', get_staff_user_id());
        }
        $this->db->where('id_entreprise', $id_E);
        $this->db->order_by('id', 'desc');
        $this->db->limit(50);
        return $this->db->get('tblactivitylog')->result_array();
    }

    public function get_activities_log_client()
    {
        $activitiesLog = array();
        if (is_numeric(get_expediteur_user_id())) {
            $this->db->where('clientid', get_expediteur_user_id());
            $this->db->order_by('tblactivitylogcustomer.id', 'desc');
            $this->db->limit(50);
            $activitiesLog = $this->db->get('tblactivitylogcustomer')->result_array();
        }

        return $activitiesLog;
    }

    public function get_appels_livreur()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $is_livreur = is_livreur();

        $this->db->select('tblappelslivreur.livreur_id, tblcolis.code_barre, tblexpediteurs.nom, tblappelslivreur.date_created, tblappelslivreur.client_id');
        $this->db->join('tblcolis', 'tblcolis.id = tblappelslivreur.colis_id', 'left');
        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tblappelslivreur.client_id', 'left');
        if ($is_livreur) {
            $this->db->where('livreur_id', get_staff_user_id());
        }
        $this->db->where('tblappelslivreur.id_entreprise', $id_E);
        $this->db->order_by('tblappelslivreur.id', 'desc');
        $this->db->limit(50);
        return $this->db->get('tblappelslivreur')->result_array();
    }

    public function get_activities_log_sms()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->select('tblsmsactivitylog.*');
        $this->db->where('tblsmsactivitylog.id_entreprise', $id_E);
        $this->db->order_by('tblsmsactivitylog.id', 'desc');
        $this->db->limit(50);
        return $this->db->get('tblsmsactivitylog')->result_array();
    }

    public function get_activities_log_sms_by_client()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $activitiesLogSms = array();
        if(is_numeric(get_expediteur_user_id())) {
            $this->db->select('tblsmsactivitylog.*');
            $this->db->join('tblcolis', 'tblcolis.code_barre = tblsmsactivitylog.code_barre', 'left');
            $this->db->where('tblcolis.id_expediteur', get_expediteur_user_id());
            $this->db->where('tblsmsactivitylog.id_entreprise', $id_E);
            $this->db->order_by('tblsmsactivitylog.id', 'desc');
            $this->db->limit(50);
            $activitiesLogSms =  $this->db->get('tblsmsactivitylog')->result_array();
        }

        return $activitiesLogSms;
    }

    public function get_taches_staff()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->select('*, (SELECT COUNT(id) FROM tblsupportcomments WHERE supportid = tblsupports.id) as nbr_comments, (SELECT COUNT(id) FROM tblsupportsattachments WHERE supportid = tblsupports.id) as nbr_attachements, (SELECT COUNT(id) FROM tblsupportchecklists WHERE supportid = tblsupports.id) as nbr_checklists');
        $this->db->where('(id IN (SELECT supportid FROM tblsupportstaffassignees WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ')');
        $this->db->where('tblsupports.finished', 0);
        $this->db->where('tblsupports.id_entreprise', $id_E);
        $this->db->order_by('tblsupports.id', 'DESC');
        return $this->db->get('tblsupports')->result_array();
    }
}
