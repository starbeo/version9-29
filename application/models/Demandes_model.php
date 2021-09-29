<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demandes_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array())
    {
        if (is_numeric($id)) {
            $this->db->select('tbldemandes.*, tbldepartements.name as departement_name, tbldepartements.color as departement_color, tbldepartementobjets.name as departement_objet_name, tbldepartementobjets.bind as departement_objet_bind, tbldepartementobjets.bind_to as departement_objet_bind_to');
            $this->db->join('tbldepartements', 'tbldepartements.id = tbldemandes.department', 'left');
            $this->db->join('tbldepartementobjets', 'tbldepartementobjets.id = tbldemandes.object', 'left');
            $this->db->where('tbldemandes.id', $id);
            $demande = $this->db->get('tbldemandes')->row();
            if ($demande) {
                $this->load->model('expediteurs_model');
                $demande->client = $this->expediteurs_model->get($demande->client_id);
            }

            return $demande;
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        return $this->db->get('tbldemandes')->result_array();
    }

    public function get_types()
    {
        $types = array(
            array('id' => 'demande', 'name' => _l('request')),
            array('id' => 'reclamation', 'name' => _l('reclamation'))
        );

        return $types;
    }

    public function get_priorities()
    {
        $priorities = array(
            array('id' => 1, 'name' => _l('low'), 'color_text' => 'info'),
            array('id' => 2, 'name' => _l('average'), 'color_text' => 'warning'),
            array('id' => 3, 'name' => _l('high'), 'color_text' => 'danger')
        );

        return $priorities;
    }

    public function get_statuses()
    {
        $priorities = array(
            array('id' => 1, 'name' => _l('in_progress'), 'color_text' => 'warning'),
            array('id' => 2, 'name' => _l('answered'), 'color_text' => 'info'),
            array('id' => 3, 'name' => _l('answered_per_customer'), 'color_text' => 'info'),
            array('id' => 4, 'name' => _l('fencing'), 'color_text' => 'success')
        );

        return $priorities;
    }

    public function get_objets_departement()
    {
        $this->db->order_by('tbldepartementobjets.name', 'asc');
        return $this->db->get('tbldepartementobjets')->result_array();
    }

    public function get_departement_by_objet($departementObjetId)
    {
        $this->db->where('id', $departementObjetId);
        $departementObjet = $this->db->get('tbldepartementobjets')->row();
        $departementId = 0;
        if ($departementObjet) {
            $departementId = $departementObjet->departement_id;
        }

        return $departementId;
    }

    /**
     * Get discussions
     * @param  int $demandeId
     * @return array
     */
    public function get_discussions($demandeId)
    {
        $this->db->where('demande_id', $demandeId);
        $this->db->order_by('date_created', 'desc');
        $discussions = $this->db->get('tbldemandediscussions')->result_array();
        $i = 0;
        foreach ($discussions as $discussion) {
            if (!is_null($discussion['staff_id'])) {
                $discussions[$i]['profile_image'] = staff_profile_image($discussion['staff_id'], array('staff-profile-image-small mright5', 'pull-left'));
                if ($discussion['staff_id'] == get_staff_user_id()) {
                    $discussions[$i]['name'] = 'Vous';
                } else {
                    $discussions[$i]['name'] = ucwords(get_staff_full_name($discussion['staff_id']));
                }
            } else if (!is_null($discussion['client_id'])) {
                $discussions[$i]['profile_image'] = client_logo($discussion['client_id'], array('staff-profile-image-small mright5', 'pull-left'));
                if ($discussion['client_id'] == get_expediteur_user_id()) {
                    $discussions[$i]['name'] = 'Vous';
                } else {
                    $discussions[$i]['name'] = ucwords(get_client_full_name($discussion['client_id']));
                }
            } else {
                $discussions[$i]['profile_image'] = '';
                $discussions[$i]['name'] = '';
            }
            $discussions[$i]['date'] = time_ago($discussion['date_created']);
            $i++;
        }

        return $discussions;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new demande
     */
    public function add($data)
    {
        unset($data['hidden_object']);
        unset($data['hidden_rel_id']);

        $data['id_entreprise'] = get_entreprise_id();

        if (is_expediteur_logged_in()) {
            $data['client_id'] = get_expediteur_user_id();
        } else if (is_staff_logged_in() || is_point_relais_logged_in()) {
            $data['addedfrom'] = get_staff_user_id();
        }

        $data['status'] = 1;
        $data['message'] = nl2br($data['message']);
        $data['department'] = NULL;
        if (is_numeric($data['object'])) {
            $data['department'] = $this->get_departement_by_objet($data['object']);
        }
        if (!is_numeric($data['rel_id']) && empty($data['rel_id'])) {
            $data['rel_id'] = NULL;
        }
        if (!isset($data['rels_id'])) {
            $data['rels_id'] = NULL;
        }
        else {
            $data['rel_id'] = NULL;
            $data['rels_id'] = json_encode($data['rels_id']);

        }

        $this->db->insert('tbldemandes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Update Demande
            $name = '';
            if ($data['type'] == 'demande') {
                $name .= 'DMD-';
            } else {
                $name .= 'RCL-';
            }
            $name .= date('dmY') . '-' . $insert_id;
            $this->db->where('id', $insert_id);
            $this->db->update('tbldemandes', array('name' => $name));
            //Handle attached piece
            handle_attached_piece_demande_upload($insert_id);
            // Alert staff, livreur and client
            $this->alert_staff_livreur_client($insert_id, $data, null, $name);
            //Add activity log
            if ($data['type'] == 'demande') {
                logActivity('Nouveau demande Ajouté [Name : ' . $name . ', ID : ' . $insert_id . ']');
            } else {
                logActivity('Nouveau réclamation Ajouté [Name : ' . $name . ', ID : ' . $insert_id . ']');
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update demande to database
     */
    public function update($data, $id)
    {
        // Get demande
        $currentDemande = $this->get($id);

        $affectedRows = 0;

        unset($data['hidden_object']);
        unset($data['hidden_rel_id']);

        $data['message'] = nl2br($data['message']);
        $data['department'] = NULL;
        if (is_numeric($data['object'])) {
            $data['department'] = $this->get_departement_by_objet($data['object']);
        }
        if (!is_numeric($data['rel_id']) && empty($data['rel_id'])) {
            $data['rel_id'] = NULL;
        }

        $this->db->where('id', $id);
        $this->db->update('tbldemandes', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            // Check if client or object is changed
            if ($currentDemande->client_id != $data['client_id'] || $currentDemande->object != $data['object']) {
                // Alert staff, livreur and client
                $this->alert_staff_livreur_client($id, $data, $currentDemande);
            }
        }

        if (handle_attached_piece_demande_upload($id)) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            //Add activity log
            if ($data['type'] == 'demande') {
                logActivity('Demande Modifié [ID : ' . $id . ']');
            } else {
                logActivity('Réclamation Modifié [ID : ' . $id . ']');
            }

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Cloturer demande demande to database
     */
    public function cloturer($idDemande)
    {
        //Cloturer la demande
        $this->db->where('id', $idDemande);
        $this->db->update('tbldemandes', array('status' => 4));
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  array $_POST data
     * @param  object $currentDemande
     * @return boolean
     * Alert staff livreur client
     */
    public function alert_staff_livreur_client($demandeId, $data, $currentDemande = null, $nameDemande = '')
    {
        if (is_null($currentDemande)) {
            $idStatusDemande = $data['status'];
        } else {
            $nameDemande = $currentDemande->name;
            $idStatusDemande = $currentDemande->status;
        }

        // Get objet
        $this->load->model('departements_model');
        $objet = $this->departements_model->get_objets($data['object']);
        // Update Colis
        $relation = "";
        if (!empty($data['rels_id']) && $data['object'] == 14 && $objet && $objet->bind == 1 && !empty($objet->bind_to) && $objet->bind_to == 'colis') {
            $rels_id =  json_decode($data['rels_id']);
            for($i = 0; $i<count($rels_id);$i++) {
                //Update colis to crbt modifiable
                $this->db->where('id', $rels_id[$i]);
                $this->db->update('tblcolis', array('crbt_modifiable' => 1, 'id_demande' => $demandeId));
                // Variable relation
                $relation = "colis";

            }
        }
        else if (is_numeric($data['rel_id']) && $objet && $objet->bind == 1 && !empty($objet->bind_to) && $objet->bind_to == 'factures') {
            if (!is_null($currentDemande) && ($currentDemande->rel_id != $data['rel_id'])) {
                //Remove id demande to facture
                $this->db->where('id', $currentDemande->rel_id);
                $this->db->update('tblfactures', array('id_demande' => NULL));
            }
            //Update colis to crbt modifiable
            $this->db->where('id', $data['rel_id']);
            $this->db->update('tblfactures', array('id_demande' => $demandeId));
            // Variable relation
            $relation = "factures";
        }

        //Load model sms & emails
        $this->load->model('sms_model');
        $this->load->model('emails_model');
        $this->load->model('staff_model');
        //Add notification, Send SMS and Email to client
        if (isset($data['client_id']) && is_numeric($data['client_id'])) {
            $this->load->model('expediteurs_model');
            $client = $this->expediteurs_model->get($data['client_id']);
            //Add notification to client
            if ($client && $objet && $objet->send_notification_client == 1) {
                $dataNotification = [];
                $dataNotification['description'] = '';
                if ($data['type'] == 'demande') {
                    $dataNotification['description'] .= 'Nouvelle demande Ajouté ';
                } else {
                    $dataNotification['description'] .= 'Nouvelle réclamation Ajouté ';
                }
                $dataNotification['description'] .= '[' . _l('name') . ' : <b>' . $nameDemande . '</b>, ' . _l('priority') . ' : <b>' . format_priorite_demande($data['priorite']) . '</b>, ' . _l('status') . ' : <b>' . format_status_demande($idStatusDemande) . '</b>]';
                $dataNotification['toclientid'] = $data['client_id'];
                $dataNotification['link'] = client_url('demandes/preview/' . $demandeId);
                add_notification_customer($dataNotification);
            }
            //Send SMS to client
            if ($client && $objet && $objet->send_sms_client == 1 && !empty($objet->sms_client)) {
                $telephoneClient = $client->telephone;
                if ($relation == "colis") {
                    
                    
                    $messageSms = $this->sms_model->parse_message($objet->sms_client, $data['client_id'], $data['rel_id'], false, false, $demandeId);
                } else if ($relation == "factures") {
                    $messageSms = $this->sms_model->parse_message($objet->sms_client, $data['client_id'], false, false, $data['rel_id'], $demandeId);
                } else {
                    $messageSms = $this->sms_model->parse_message($objet->sms_client, $data['client_id'], false, false, false, $demandeId);
                }
                if (!empty($telephoneClient) && !empty($messageSms)) {
                    $send = send_sms_to_recipient($telephoneClient, $messageSms);
                }
            }
            //Send Email to client
            if ($client && $objet && $objet->send_email_client == 1 && !empty($objet->email_client)) {
                $emailClient = $client->email;
                if ($relation == "colis") {
                    $rels_id =  json_decode($data['rels_id']);
                    for($i = 0; $i<count($rels_id);$i++) {
                    $subjetEmail = $this->sms_model->parse_message($objet->subject_email_client, $data['client_id'], $rels_id[$i], false, false, $demandeId);
                    $messageEmail = $this->sms_model->parse_message($objet->email_client, $data['client_id'], $rels_id[$i], false, false, $demandeId);
                    }
                } else if ($relation == "factures") {
                    $subjetEmail = $this->sms_model->parse_message($objet->subject_email_client, $data['client_id'], false, false, $data['rel_id'], $demandeId);
                    $messageEmail = $this->sms_model->parse_message($objet->email_client, $data['client_id'], false, false, $data['rel_id'], $demandeId);
                } else {
                    $subjetEmail = $this->sms_model->parse_message($objet->subject_email_client, $data['client_id'], false, false, false, $demandeId);
                    $messageEmail = $this->sms_model->parse_message($objet->email_client, $data['client_id'], false, false, false, $demandeId);
                }
                if (!empty($emailClient) && !empty($messageEmail)) {
                    $send = $this->emails_model->send_simple_email($emailClient, $subjetEmail, $messageEmail);
                }
            }
        }
        //Add notification, Send SMS and Email to the user of this department
        if (is_numeric($data['department']) && !is_null($data['department'])) {
            $staffs = $this->staff_model->get('', '', 'admin != 0 AND department = ' . $data['department']);
            if (count($staffs) > 0 && $objet && ($objet->send_notification_staff == 1 || $objet->send_email_staff == 1 || $objet->send_sms_staff == 1)) {
                foreach ($staffs as $staff) {
                    //Add notification to staff
                    if ($objet && $objet->send_notification_staff == 1 && is_numeric($staff['staffid'])) {
                        $dataNotification = [];
                        $dataNotification['description'] = '';
                        if ($data['type'] == 'demande') {
                            $dataNotification['description'] .= 'Nouvelle demande crée ';
                        } else {
                            $dataNotification['description'] .= 'Nouvelle réclamation crée ';
                        }
                        $dataNotification['description'] .= '[' . _l('name') . ' : <b>' . $nameDemande . '</b>, ' . _l('priority') . ' : <b>' . format_priorite_demande($data['priorite']) . '</b>, ' . _l('status') . ' : <b>' . format_status_demande($idStatusDemande) . '</b>]';
                        $dataNotification['fromcompany'] = $data['client_id'];
                        $dataNotification['touserid'] = $staff['staffid'];
                        $dataNotification['link'] = admin_url('demandes/preview/' . $demandeId);
                        add_notification($dataNotification);
                    }
                    //Send SMS to staff
                    if ($objet && $objet->send_sms_staff == 1 && !empty($objet->sms_staff)) {
                        $telephoneStaff = $staff['phonenumber'];
                        if ($relation == "colis") {
                            $messageSms = $this->sms_model->parse_message($objet->sms_staff, $data['client_id'], $data['rel_id'], false, false, $demandeId, $staff['staffid']);
                        } else if ($relation == "factures") {
                            $messageSms = $this->sms_model->parse_message($objet->sms_staff, $data['client_id'], false, false, $data['rel_id'], $demandeId, $staff['staffid']);
                        } else {
                            $messageSms = $this->sms_model->parse_message($objet->sms_staff, $data['client_id'], false, false, false, $demandeId, $staff['staffid']);
                        }
                        if (!empty($telephoneStaff) && !empty($messageSms)) {
                            $send = send_sms_to_recipient($telephoneStaff, $messageSms);
                        }
                    }
                    //Send Email to staff
                    if (!empty($staff['email']) && $objet && $objet->send_email_staff == 1 && !empty($objet->email_staff)) {
                        $emailStaff = $staff['email'];
                        if ($relation == "colis") {
                            $subjetEmail = $this->sms_model->parse_message($objet->subject_email_staff, $data['client_id'], $data['rel_id'], false, false, $demandeId, $staff['staffid']);
                            $messageEmail = $this->sms_model->parse_message($objet->email_staff, $data['client_id'], $data['rel_id'], false, false, $demandeId, $staff['staffid']);
                        } else if ($relation == "factures") {
                            $subjetEmail = $this->sms_model->parse_message($objet->subject_email_staff, $data['client_id'], false, false, $data['rel_id'], $demandeId, $staff['staffid']);
                            $messageEmail = $this->sms_model->parse_message($objet->email_staff, $data['client_id'], false, false, $data['rel_id'], $demandeId, $staff['staffid']);
                        } else {
                            $subjetEmail = $this->sms_model->parse_message($objet->subject_email_staff, $data['client_id'], false, false, false, $demandeId, $staff['staffid']);
                            $messageEmail = $this->sms_model->parse_message($objet->email_staff, $data['client_id'], false, false, false, $demandeId, $staff['staffid']);
                        }
                        if (!empty($emailStaff) && !empty($messageEmail)) {
                            $send = $this->emails_model->send_simple_email($emailStaff, $subjetEmail, $messageEmail);
                        }
                    }
                }
            }
        }
        //Add notification, Send SMS and Email to the delivery men
   
        return true;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new discussion
     */
    public function add_discussion($demandeId, $content)
    {
        $dataInsert['demande_id'] = $demandeId;
        $dataInsert['content'] = nl2br($content);

        if (is_staff_logged_in() || is_point_relais_logged_in()) {
            $dataInsert['type'] = 'staff';
            $dataInsert['staff_id'] = get_staff_user_id();
            $statusId = 2;
        } else {
            $dataInsert['type'] = 'client';
            $dataInsert['client_id'] = get_expediteur_user_id();
            $statusId = 3;
        }

        $this->db->insert('tbldemandediscussions', $dataInsert);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            // Update demande
            $dataUpdate['status'] = $statusId;
            $this->db->where('id', $demandeId);
            $this->db->update('tbldemandes', $dataUpdate);

            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @return boolean
     * Add note demande to database
     */
    public function add_note($data)
    {
        $dataUpdate['notes'] = $data['note'];
        $this->db->where('id', $data['demande_id']);
        $this->db->update('tbldemandes', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            logActivity('Note Ajouté à la demande [ID: ' . $data['demande_id'] . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @return boolean
     * Add rating demande to database
     */
    public function add_rating($data)
    {
        // Status cloturer
        $dataUpdate['status'] = 4;
        $dataUpdate['rating'] = $data['rating'];
        $this->db->where('id', $data['demande_id']);
        $this->db->update('tbldemandes', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            logActivity('Evaluation Ajouté à la demande [ID: ' . $data['demande_id'] . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer statusid
     * @return boolean
     * Update status demande
     */
    public function change_status($id, $statusId)
    {
        $this->db->where('id', $id);
        $this->db->update('tbldemandes', array('status' => $statusId));
        if ($this->db->affected_rows() > 0) {
            logActivity('Demande STATUT Changé [Demande ID: ' . $id . ' Statut(En cours / Répondu / Cloturé): ' . $statusId . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete demande from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_expediteur_logged_in()) {
            $this->db->where('client_id', get_expediteur_user_id());
        }
        $this->db->where('id', $id);
        $this->db->delete('tbldemandes');
        if ($this->db->affected_rows() > 0) {
            logActivity('Demande Supprimé [ID: ' . $id . ']');

            return true;
        }

        return false;
    }


    public function get_demandes_by_clientid($where = array())
    {
        $this->db->select('id, name as name');

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('id', 'desc');
        return $this->db->get('tbldemandes')->result_array();
    }




   public function get_colis_export($status = '', $statusReel = true)
    {
        // Columns by status
        $columns =     'tbldemandes.id,
                tbldemandes.name,
                tbldemandes.type,
                tbldemandes.department,
                tbldemandes.priorite,
                tbldemandes.status,
                tbldemandes.rating,
                tbldemandes.datecreated,
              
                tbldemandes.message'
                ;
     //   if (is_numeric($status) && ($status == 9 || $status == 1)) {
        //    $columns = 'tblcolis.code_barre,tblcolis.num_commande ,tblexpediteurs.nom, tblcolis.nom_complet, tblcolis.telephone, replace(tblcolis.crbt, ".", ",") as "crbt", tblstatuscolis.name as "statut",
          //        tblvilles.name as "ville", tblcolis.date_ramassage, tblcolis.frais';
     //   } else {
      ///      $columns = 'tblcolis.code_barre,tblcolis.num_commande, tblexpediteurs.nom, tblstaff.firstname, tblcolis.telephone, replace(tblcolis.crbt, ".", ",") as "crbt", tblstatuscolis.name as "statut",
       //           tblvilles.name as "ville", tblcolis.date_ramassage, tblcolis.date_livraison, tblcolis.frais ,tbletatcolis.name as "etat_colis",(select nom from tblbonlivraison where id=tblcolis.num_bonlivraison) as "BonLivraison"';
////
        $where = ' ';
    //    if (is_numeric($status)) {
     //       if ($statusReel == true) {
      //          $where = ' AND tblcolis.status_reel = ' . $status . ' ';
      //      } else {
       ///         if ($status == 1) {
            //        $where = ' AND tblcolis.status_reel NOT IN (2, 3, 9) AND tblcolis.status_id = ' . $status . ' ';
            //    } else {
        //            $where = ' AND tblcolis.status_id = ' . $status . ' ';
             //   }
         //   }
      //  }

        $query = 'SELECT DISTINCT ' . $columns . '
                  FROM tbldemandes,
                  ' . $where . '
                  ORDER BY tblcolis.id DESC
                  LIMIT 50000';

        return $this->db->query($query)->result_array();
    }

    public function export_demandes($where = array())
    {
        // Columns
        $columns =  'tbldemandes.id,
                tbldemandes.name,
                tbldemandes.type,
                tbldepartementobjets.name as department,
                tblexpediteurs.nom as nom,
                tbldemandes.priorite,
                tbldemandes.status,
                tbldemandes.datecreated,
              
                tbldemandes.message';

        $this->db->select($columns);

        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tbldemandes.client_id', 'left');
        $this->db->join('tbldepartementobjets', 'tbldepartementobjets.id = tbldemandes.object', 'left');


        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tbldemandes.id', 'desc');
        $this->db->limit(100000);
        return $this->db->get('tbldemandes')->result_array();
    }



}



