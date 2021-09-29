<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expediteurs_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get expediteurs
     * @return mixed
     */
    public function get($id = '', $active = 1, $where = array(), $select = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if (!empty($select)) {
            $this->db->select($select);
        }

        if (is_int($active)) {
            $this->db->where('active', $active);
        }

        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblexpediteurs')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        return $this->db->get('tblexpediteurs')->result_array();
    }
    
    /**
     * Get expediteurs
     * @return mixed
     */
    public function get_by_affiliation_code($affiliationCode)
    {
        $this->db->where('code_parrainage', $affiliationCode);
        return $this->db->get('tblexpediteurs')->row();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new expediteur
     */
    public function add($data, $clientEnAttente = false)
    {
        unset($data['custom_view']);
        unset($data['etat']);

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        //Insertion de l'ID de l'utilisateur
        $data['id_user'] = get_staff_user_id();
        //Generate Mot de passe
        $data['pass_no_crypte'] = $data['password'];
        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $data['password'] = $hasher->HashPassword($data['password']);

        if (!isset($data['active'])) {
            $data['active'] = 1;
        }

        if (isset($data['ouverture'])) {
            $data['ouverture'] = 1;
        } else {
            $data['ouverture'] = 0;
        }

        if (isset($data['option_frais'])) {
            $data['option_frais'] = 1;
        } else {
            $data['option_frais'] = 0;
        }

        if (isset($data['option_frais_assurance'])) {
            $data['option_frais_assurance'] = 1;
        } else {
            $data['option_frais_assurance'] = 0;
        }

        if (isset($data['affiliation_code']) && $clientEnAttente == false) {
            unset($data['affiliation_code']);
        }

        if (isset($data['total_colis_parrainage'])) {
            $data['total_colis_parrainage'] = 0;
        }

        $this->db->insert('tblexpediteurs', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            // Get client
            $client = $this->get($insert_id, '');
            if ($expediteur) {
                //Generate Token
                $token = $hasher->HashPassword($client->date_created . '-' . $insert_id);
                //Update Expediteur
                $this->db->where('id', $insert_id);
                $this->db->update('tblexpediteurs', array('token' => $token));
            }
            //Send Email Bienvenue
            if (!empty($data['nom'])) {
                $dataEmail['name'] = $data['nom'];
                $dataEmail['email'] = $data['email'];
                $dataEmail['password'] = $data['pass_no_crypte'];
                $this->load->model('emails_model');
                $this->emails_model->send_email('', '', $data['email'], 'Bienvenue chez ' . get_option('companyname'), 'new-client-created', $dataEmail);
            }
            //Generate contract
            if (isset($data['nom']) && !empty($data['nom']) && isset($data['contact']) && !empty($data['contact']) && isset($data['adresse']) && !empty($data['adresse']) && isset($data['frais_livraison_interieur']) && !empty($data['frais_livraison_interieur']) && isset($data['frais_livraison_exterieur']) && !empty($data['frais_livraison_exterieur'])) {
                $dataAddedContrat = array();
                $dataAddedContrat['client_id'] = $insert_id;
                $dataAddedContrat['datestart'] = date('d/m/Y');
                $dataAddedContrat['fullname'] = $data['nom'];
                $dataAddedContrat['contact'] = $data['contact'];
                $dataAddedContrat['address'] = $data['adresse'];
                $dataAddedContrat['commercial_register'] = $data['registre_commerce'];
                $dataAddedContrat['frais_livraison_interieur'] = $data['frais_livraison_interieur'];
                $dataAddedContrat['frais_livraison_exterieur'] = $data['frais_livraison_exterieur'];
                $dataAddedContrat['date_created_client'] = date('d/m/Y');
                //Add contrat
                $this->load->model('contrats_model');
                $this->contrats_model->add($dataAddedContrat);
            }

            logActivity('Nouveau Expediteur Ajouté [' . $data['nom'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update Expediteur to database
     */
    public function update($data, $id)
    {
        //Get client
        $client = $this->get($id, '');

        $affectedRows = 0;
        unset($data['custom_view']);
        unset($data['etat']);

        if (isset($data['password'])) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $this->load->helper('phpass');
                $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
                $data['pass_no_crypte'] = $data['password'];
                $data['password'] = $hasher->HashPassword($data['password']);
            }
        }

        if (isset($data['ouverture'])) {
            $data['ouverture'] = 1;
        } else {
            $data['ouverture'] = 0;
        }

        if (isset($data['option_frais'])) {
            $data['option_frais'] = 1;
        } else {
            $data['option_frais'] = 0;
        }

        if (isset($data['option_frais_assurance'])) {
            $data['option_frais_assurance'] = 1;
        } else {
            $data['option_frais_assurance'] = 0;
        }

        if (isset($data['affiliation_code'])) {
            unset($data['affiliation_code']);
        }

        if (isset($data['total_colis_parrainage'])) {
            unset($data['total_colis_parrainage']);
        }

        if (is_array($data) && !empty($data)) {
            $this->db->where('id', $id);
            $this->db->update('tblexpediteurs', $data);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
                //Generate contract
                if ($client && (($client->nom != $data['nom']) || ($client->contact != $data['contact']) || ($client->adresse != $data['adresse']) || ($client->frais_livraison_interieur != $data['frais_livraison_interieur']) || ($client->frais_livraison_exterieur != $data['frais_livraison_exterieur']) || ($client->registre_commerce != $data['registre_commerce']))) {
                    //Load model contract
                    $this->load->model('contrats_model');
                    //Check if contract client exist
                    if (total_rows('tblcontrats', array('client_id' => $id)) == 0) {
                        $dataAddedContrat = array();
                        $dataAddedContrat['client_id'] = $id;
                        $dataAddedContrat['datestart'] = date('d/m/Y');
                        $dataAddedContrat['fullname'] = $data['nom'];
                        $dataAddedContrat['contact'] = $data['contact'];
                        $dataAddedContrat['address'] = $data['adresse'];
                        $dataAddedContrat['commercial_register'] = $data['registre_commerce'];
                        $dataAddedContrat['frais_livraison_interieur'] = $data['frais_livraison_interieur'];
                        $dataAddedContrat['frais_livraison_exterieur'] = $data['frais_livraison_exterieur'];
                        $dataAddedContrat['date_created_client'] = date('d/m/Y');
                        //Add contract
                        $contratId = $this->contrats_model->add($dataAddedContrat);
                    } else {
                        //Get contract client
                        $contrat = $this->contrats_model->get_contrat_by_client($id);
                        if ($contrat) {
                            $dataUpdatedContrat = array();
                            $dataUpdatedContrat['client_id'] = $id;
                            $dataUpdatedContrat['datestart'] = date('d/m/Y');
                            $dataUpdatedContrat['fullname'] = $data['nom'];
                            $dataUpdatedContrat['contact'] = $data['contact'];
                            $dataUpdatedContrat['address'] = $data['adresse'];
                            $dataUpdatedContrat['commercial_register'] = $data['registre_commerce'];
                            $dataUpdatedContrat['frais_livraison_interieur'] = $data['frais_livraison_interieur'];
                            $dataUpdatedContrat['frais_livraison_exterieur'] = $data['frais_livraison_exterieur'];
                            if (is_date($client->date_created)) {
                                $dataUpdatedContrat['date_created_client'] = date('d/m/Y', strtotime($client->date_created));
                            } else {
                                $dataUpdatedContrat['date_created_client'] = date('d/m/Y');
                            }
                            //Update contract
                            $contratSuccess = $this->contrats_model->update($dataUpdatedContrat, $contrat->id);
                        }
                    }
                }
            }
        } else {
            if ($client) {
                $data['nom'] = $client->nom;
            }
        }

        if (handle_client_logo_upload() == true) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Expediteur Modifié [Nom: ' . $data['nom'] . ', ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete expediteur from database, if used return array with key referenced
     */
    public function delete($id)
    {
        do_action('before_expediteur_deleted', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblexpediteurs');

        if ($this->db->affected_rows() > 0) {
            logActivity('Expediteur Supprimé [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update expediteur status Active/Inactive
     */
    public function change_expediteur_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblexpediteurs', array(
            'active' => $status
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Expediteur Status Changé [ExpediteurID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  mixed $_POST data
     * @return mixed
     * Change expediteur password, used from expediteur area
     */
    public function change_expediteur_password($data)
    {
        // Get current password
        $this->db->where('id', get_expediteur_user_id());
        $expediteur = $this->db->get('tblexpediteurs')->row();

        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        if (!$hasher->CheckPassword($data['oldpassword'], $expediteur->password)) {
            logActivity('Expediteur Mot de passe ne correspond pas [ExpediteurID: ' . get_expediteur_user_id() . ']');
            return array(
                'old_password_not_match' => true
            );
        }

        $update_data['pass_no_crypte'] = $data['newpassword'];
        $update_data['password'] = $hasher->HashPassword($data['newpassword']);

        $update_data['last_password_change'] = date('Y-m-d H:i:s');
        $this->db->where('id', get_expediteur_user_id());
        $this->db->update('tblexpediteurs', $update_data);


        if ($this->db->affected_rows() > 0) {
            logActivity('Expediteur  Mot de passe Changé [ExpediteurID: ' . get_expediteur_user_id() . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update Expediteur to database
     */
    public function update_settings($data, $id)
    {
        if (isset($data['ouverture'])) {
            $dataUpdate['ouverture'] = 1;
        } else {
            $dataUpdate['ouverture'] = 0;
        }

        if (isset($data['option_frais'])) {
            $dataUpdate['option_frais'] = 1;
        } else {
            $dataUpdate['option_frais'] = 0;
        }

        if (isset($data['option_frais_assurance'])) {
            $dataUpdate['option_frais_assurance'] = 1;
        } else {
            $dataUpdate['option_frais_assurance'] = 0;
        }

        $this->db->where('id', $id);
        $this->db->update('tblexpediteurs', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            logActivity('Paramètre Expediteur Modifié [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get notifications client
     * @return mixed
     */
    public function get_notifications_client($clientid = '')
    {
        if (is_numeric($clientid)) {
            $this->db->where('toclientid', $clientid);
            $this->db->order_by('id', 'desc');
            $this->db->limit(40);
            $notifications = $this->db->get('tblnotificationscustomer')->result_array();
            $i = 0;
            foreach ($notifications as $notification) {
                $notifications[$i]['link'] = str_replace("expediteurs/colis", "client/colis/index", $notification['link']);
                $i++;
            }

            return $notifications;
        }
    }

    /**
     * Default Total fresh & crbt colis / chart
     * @return array chart data
     */
    public function default_fresh_crbt_colis_expediteur()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $clientid = $this->input->post('client');
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
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        } else if (get_expediteur_user_id()) {
            $id_expediteur = get_expediteur_user_id();
            $this->db->where('tblcolis.id_expediteur', $id_expediteur);
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
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        } else if (get_expediteur_user_id()) {
            $id_expediteur = get_expediteur_user_id();
            $this->db->where('tblcolis.id_expediteur', $id_expediteur);
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
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        } else if (get_expediteur_user_id()) {
            $id_expediteur = get_expediteur_user_id();
            $this->db->where('tblcolis.id_expediteur', $id_expediteur);
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
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        } else if (get_expediteur_user_id()) {
            $id_expediteur = get_expediteur_user_id();
            $this->db->where('tblcolis.id_expediteur', $id_expediteur);
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
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        } else if (get_expediteur_user_id()) {
            $id_expediteur = get_expediteur_user_id();
            $this->db->where('tblcolis.id_expediteur', $id_expediteur);
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

 public function change_expediteur_password_home($data)
    {
        // Get current password
        $this->db->where('id', get_expediteur_user_id());
        $expediteur = $this->db->get('tblexpediteurs')->row();

        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        if (!$hasher->CheckPassword($data['oldpassword'], $expediteur->password)) {
            logActivity('Expediteur Mot de passe ne correspond pas [ExpediteurID: ' . get_expediteur_user_id() . ']');
            return array(
                'old_password_not_match' => true
            );
        }

      $update_data['pass_check '] = '1';
        $update_data['password'] = $hasher->HashPassword($data['newpassword']);

        $update_data['last_password_change'] = date('Y-m-d H:i:s');
        $this->db->where('id', get_expediteur_user_id());
        $this->db->update('tblexpediteurs', $update_data);


        if ($this->db->affected_rows() > 0) {
            logActivity('Expediteur  Mot de passe Changé [ExpediteurID: ' . get_expediteur_user_id() . ']');
            return true;
        }

        return false;
    }


}
