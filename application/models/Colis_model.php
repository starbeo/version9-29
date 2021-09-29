<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $select = '', $orderBy = '', $startLimit = '', $endLimit = '')
    {
        $this->db->select('tblcolis.*, tblvilles.name as ville_name');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');

        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if ($id != '') {
            $this->db->where('tblcolis.id', $id);
            return $this->db->get('tblcolis')->row();
        }

        $this->db->order_by('tblcolis.id', 'desc');

        if (is_numeric($startLimit) && is_numeric($endLimit)) {
            $this->db->limit($startLimit, $endLimit);
        }

        return $this->db->get('tblcolis')->result_array();
    }

    public function get_colis_by_clientid($where = array())
    {
        $this->db->select('id, code_barre as name');

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('id', 'desc');
        return $this->db->get('tblcolis')->result_array();
    }

    public function get_count_coli_by_expediteur($expediteurId)
    {
        $this->db->select('count(id) as count');
        $this->db->where('id_expediteur', $expediteurId);
        return $this->db->get('tblcolis')->row()->count;
    }

    public function get_id_coli_by_barcode($barcode = '')
    {
        if (!empty($barcode)) {
            $this->db->select('id');
            $this->db->where('code_barre', $barcode);
            $this->db->limit(1);
            return $this->db->get('tblcolis')->row();
        }
    }

    public function get_colis_by_barcode($barcode = '')
    {
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('id_entreprise', get_entreprise_id());
        }

        if (!empty($barcode)) {
            $this->db->where('code_barre', $barcode);
            $this->db->limit(1);
            return $this->db->get('tblcolis')->row();
        }
    }

    public function get_colis_export($status = '', $statusReel = true)
    {
        // Columns by status
        $columns = '';
        if (is_numeric($status) && ($status == 9 || $status == 1)) {
            $columns = 'tblcolis.code_barre, tblexpediteurs.nom, tblcolis.nom_complet, tblcolis.telephone, replace(tblcolis.crbt, ".", ",") as "crbt", tblstatuscolis.name as "statut", 
                  tblvilles.name as "ville", tblcolis.date_ramassage, tblcolis.frais';
        } else {
            $columns = 'tblcolis.code_barre, tblexpediteurs.nom, tblstaff.firstname, tblcolis.telephone, replace(tblcolis.crbt, ".", ",") as "crbt", tblstatuscolis.name as "statut", 
                  tblvilles.name as "ville", tblcolis.date_ramassage, tblcolis.date_livraison, tblcolis.frais ,tbletatcolis.name as "etat_colis",(select nom from tblbonlivraison where id=tblcolis.num_bonlivraison) as "BonLivraison"';
        }

        $where = ' ';
        if (is_numeric($status)) {
            if ($statusReel == true) {
                $where = ' AND tblcolis.status_reel = ' . $status . ' ';
            } else {
                if ($status == 1) {
                    $where = ' AND tblcolis.status_reel NOT IN (2, 3, 9) AND tblcolis.status_id = ' . $status . ' ';
                } else {
                    $where = ' AND tblcolis.status_id = ' . $status . ' ';
                }
            }
        }

        $query = 'SELECT DISTINCT ' . $columns . '
                  FROM tblcolis, tblvilles, tblexpediteurs, tblstaff, tblstatuscolis,tbletatcolis
                  WHERE tblcolis.status_reel = tblstatuscolis.id 
                  AND tblcolis.id_expediteur = tblexpediteurs.id 
                  AND tblcolis.ville = tblvilles.id 
                  AND tblcolis.livreur = tblstaff.staffid 
                  AND tblcolis.etat_id = tbletatcolis.id
                  ' . $where . '
                  ORDER BY tblcolis.id DESC
                  LIMIT 50000';

        return $this->db->query($query)->result_array();
    }

    public function get_colis_facture_export()
    {
        $query = 'SELECT DISTINCT tblcolis.code_barre, tblexpediteurs.nom, tblvilles.name as "ville", tblfactures.nom as "facture", tbletatcolis.name, tblstatuscolis.name as "statut",
                 (case tblfactures.status
                    when 1 then "Non regler"
                    when 2 then "regler"
                  end) as "status_colis", 
                  tblstaff.firstname, tblcolis.date_ramassage, tblcolis.date_livraison, tblcolis.frais,replace(tblcolis.crbt, ".", ",") as "crbt"
                 FROM tblcolis, tblvilles, tblexpediteurs, tblstaff, tblstatuscolis, tblfactures, tbletatcolis
                 WHERE tblcolis.status_id = tblstatuscolis.id 
                 AND tblcolis.id_expediteur = tblexpediteurs.id 
                 AND tblcolis.ville = tblvilles.id 
                 AND tblcolis.livreur = tblstaff.staffid 
                 AND tblcolis.status_id = tblstatuscolis.id
                 AND tblcolis.num_facture = tblfactures.id
                 AND tblcolis.etat_id = tbletatcolis.id
                 ORDER BY tblcolis.id DESC
                 LIMIT 40000';
        return $this->db->query($query)->result_array();
    }

    public function export_colis($where = array(), $colisFacturer = false)
    {
        // Columns
        $columns = 'tblcolis.code_barre, 
                    tblcolis.num_commande, 
                    tblexpediteurs.nom,
                    CONCAT(CONCAT(UCASE(LEFT(tblstaff.firstname, 1)), SUBSTRING(LOWER(tblstaff.firstname), 2)), " ", CONCAT(UCASE(LEFT(tblstaff.lastname, 1)), SUBSTRING(LOWER(tblstaff.lastname), 2))) as livreur,
                    tblcolis.nom_complet, 
                    tblcolis.telephone, 
                    tblcolis.crbt, 
                    tblstatuscolis.name as statutName, 
                    tblvilles.name as ville, 
                    tblcolis.date_ramassage, 
                    tblcolis.date_livraison, 
                    tblcolis.frais,
                    tbletatcolis.name as etatName,
                    (SELECT nom FROM tblbonlivraison WHERE id = tblcolis.num_bonlivraison) as bon_livraison,
                    (SELECT nom FROM tbletatcolislivre WHERE id = tblcolis.num_etatcolislivrer) as etat_colis_livrer,
                    (SELECT nom FROM tblfactures WHERE id = tblcolis.num_facture) as facture,
                    tblcolis.type_livraison,
                    CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as point_relai,
                    tblcolis.date_retour,
                    tblcolis.status_reel,
                    tblstatuscolis.color as statutColor';

        if ($colisFacturer) {
            $columns .= ', tblfactures.status as statut_facture';
            $this->db->select($columns);
        }
        $this->db->select($columns);
        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tblcolis.id_expediteur', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblcolis.livreur', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');
        $this->db->join('tblpointsrelais', 'tblpointsrelais.id = tblcolis.point_relai_id', 'left');
        $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblcolis.status_reel', 'left');
        $this->db->join('tbletatcolis', 'tbletatcolis.id = tblcolis.etat_id', 'left');
        if ($colisFacturer) {
            $this->db->join('tblfactures', 'tblfactures.id = tblcolis.num_facture', 'left');
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tblcolis.id', 'desc');
        $this->db->limit(100000);
        return $this->db->get('tblcolis')->result_array();
    }

    public function get_colis_expediteur_export($where = array())
    {
        $this->db->select('tblcolis.code_barre, tblcolis.nom_complet, tblcolis.telephone, tblcolis.crbt, tblcolis.anc_crbt, tblstatuscolis.name as "statut", tblvilles.name as "ville", tblcolis.date_ramassage, tblcolis.date_livraison, tblcolis.frais');
        $this->db->from('tblcolis');
        $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblcolis.status_reel', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tblcolis.id', 'desc');
        return $this->db->get()->result_array();
    }

    public function get_total_colis($id = '')
    {
        $this->load->model('expediteurs_model');

        //Get Colis
        $colis = $this->get($id);
        //Calculate Total Colis
        $total = 0;
        if ($colis) {
            if ($colis->status_reel == 9) {
                //$total += get_option('frais_colis_refuse_par_defaut');
            } else {
                if ($colis->crbt > 0) {
                    $total += $colis->crbt;
                }
            }
            $total = number_format($total, 2, '.', '');
        }

        return $total;
    }

    public function get_types_livraison()
    {
        $types = array(
            array('id' => 'a_domicile', 'name' => _l('a_domicile')),
            array('id' => 'point_relai', 'name' => _l('au_point_relai'))
        );

        return $types;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new colis
     */
    public function add_colis_to_colis_cash_plus($data)
    {
        $dataAdded['colis_id'] = $data['colis_id'];
        $dataAdded['id_entreprise'] = get_entreprise_id();
        $this->db->insert('tblcoliscashplus', $dataAdded);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new colis
     */
    public function add($data)
    {
        //Annulation des champs
        unset($data['id']);
        //Vérification du type de livraison
        if (isset($data['type_livraison'])) {
            //Si le type de livraison est point relais, et le numéro du point relais existe, on fait la récupération de la ville
            if ($data['type_livraison'] == 'point_relai' && isset($data['point_relai_id']) && is_numeric($data['point_relai_id'])) {
                // Get point relai
                $this->load->model('points_relais_model');
                $pointRelai = $this->points_relais_model->get($data['point_relai_id']);
                if ($pointRelai) {
                    $data['ville'] = $pointRelai->ville;
                }
            }
        } else {
            $data['type_livraison'] = 'a_domicile';
        }
        //Vérification si la coli est ajouté à partir du module colis en attente ou bien du module colis
        $colisEnAttente = false;
        if (isset($data['en_attente'])) {
            $colisEnAttente = true;
            $colis_en_attente_id = $data['colis_en_attente_id'];
            unset($data['en_attente']);
            unset($data['colis_en_attente_id']);
        } else {
            //Vérification si le numéro de commande est lié par Amana
            if (isset($data['num_commande']) && !empty($data['num_commande']) && _startsWith(strtoupper($data['num_commande']), 'TA') && endsWith(strtoupper($data['num_commande']), 'MA')) {
                $data['code_barre'] = $data['num_commande'];
            } else {
                $data['code_barre'] = get_option('alias_barcode') . $data['id_expediteur'] . 'MA' . get_nbr_coli_by_expediteur($data['id_expediteur']);
            }
            if (isset($data['num_commande']) && empty($data['num_commande'])) {
                $data['num_commande'] = $data['code_barre'];
            }
        }
        //Vérification du champ adresse
        if (isset($data['adresse']) && !empty($data['adresse'])) {
            $data['adresse'] = trim($data['adresse']);
        } else {
            $data['adresse'] = '';
        }
        //Vérification du champ quartier
        if (isset($data['quartier']) && empty($data['quartier'])) {
            unset($data['quartier']);
        }
        //Vérification du champ livreur
        if (isset($data['livreur']) && empty($data['livreur'])) {
            unset($data['livreur']);
        }
        //Vérification du champ ouverture
        if (isset($data['ouverture'])) {
            $data['ouverture'] = 1;
        } else {
            $data['ouverture'] = 0;
        }
        //Vérification du champ option frais
        if (isset($data['option_frais'])) {
            $data['option_frais'] = 1;
        } else {
            $data['option_frais'] = 0;
        }
        //Vérification du champ option frais assurance
        if (isset($data['option_frais_assurance'])) {
            $data['option_frais_assurance'] = 1;
        } else {
            $data['option_frais_assurance'] = 0;
        }
        //Vérification du champ importer
        if (!isset($data['importer'])) {
            unset($data['importer']);
        }
        //Champs manuelle
        $data['etat_id'] = 1;
        $data['status_id'] = 1;
        $data['status_reel'] = 5;
        $data['date_ramassage'] = date('Y-m-d');
        $data['id_utilisateur'] = get_staff_user_id();
        $data['id_entreprise'] = get_entreprise_id();
        //Ajout de la coli
        $this->db->insert('tblcolis', $data);
        $insertId = $this->db->insert_id();
        if ($insertId) {
            //Add log Activity
            logActivity("Nouveau Colis Ajouté [Code d'envoi: " . $data['code_barre'] . ", Colis ID: " . $insertId . "]");
            //Update field colis id to colis en attente
            if ($colisEnAttente) {
                $this->db->where('id', $colis_en_attente_id);
                $this->db->update('tblcolisenattente', array('colis_id' => $insertId));
            }
            //Add default status to colis 
            $dataStatus = [];
            $dataStatus['code_barre_verifie'] = $data['code_barre'];
            $dataStatus['type'] = 5;
            $dataStatus['emplacement_id'] = 9;
            $this->load->model('status_model');
            $this->status_model->add($dataStatus);

            return $insertId;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update colis to database
     */
    public function update($data, $id)
    {
        // Get coli
        $coli = $this->get($id);

        $barcode = $data['code_barre'];
        //Annulation des champs
        unset($data['code_barre']);
        unset($data['num_commande']);
        //Vérification du type de livraison
        if (isset($data['type_livraison'])) {
            //Si le type de livraison est point relais, et le numéro du point relais existe, on fait la récupération de la ville
            if ($data['type_livraison'] == 'point_relai' && isset($data['point_relai_id']) && is_numeric($data['point_relai_id'])) {
                // Get point relai
                $this->load->model('points_relais_model');
                $pointRelai = $this->points_relais_model->get($data['point_relai_id']);
                if ($pointRelai) {
                    $data['ville'] = $pointRelai->ville;
                }
            }
        }
        //Vérification du champ adresse
        if (isset($data['adresse']) && !empty($data['adresse'])) {
            $data['adresse'] = trim($data['adresse']);
        } else {
            $data['adresse'] = '';
        }
        //Vérification du champ quartier
        if (isset($data['quartier']) && empty($data['quartier'])) {
            unset($data['quartier']);
        }
        //Vérification du champ livreur
        if (isset($data['livreur']) && empty($data['livreur'])) {
            unset($data['livreur']);
        }
        //Vérification du champ ouverture
        if (isset($data['ouverture'])) {
            $data['ouverture'] = 1;
        } else {
            $data['ouverture'] = 0;
        }
        //Vérification du champ option frais
        if (isset($data['option_frais'])) {
            $data['option_frais'] = 1;
        } else {
            $data['option_frais'] = 0;
        }
        //Vérification du champ option frais assurance
        if (isset($data['option_frais_assurance'])) {
            $data['option_frais_assurance'] = 1;
        } else {
            $data['option_frais_assurance'] = 0;
        }
        //Vérification changement du champ crbt
        $addLogActivityUpdatedCrbt = false;
        if ($coli && $coli->crbt_modifiable == 0) {
            $data['crbt'] = $coli->crbt;
        } else {
            if ($coli && $coli->crbt != $data['crbt']) {
                $addLogActivityUpdatedCrbt = true;
                $lastCrbt = $coli->crbt;
                $newCrbt = $data['crbt'];
            }
        }
        //Modification de la coli
        $this->db->where('id', $id);
        $this->db->update('tblcolis', $data);
        if ($this->db->affected_rows() > 0) {
            //Add Notification
            $_data['description'] = "Colis Modifié [Code d'envoi: " . $barcode . "]";
            $_data['touserid'] = 2;
            $_data['link'] = admin_url('colis');
            add_notification($_data);
            //Add log activity
            logActivity("Colis Modifié [Code d'envoi: " . $barcode . ", ID: " . $id . "]");
            //Add log activity CRBT updated
            if ($addLogActivityUpdatedCrbt) {
                logActivity("CRBT Coli Modifié [Code d'envoi: " . $barcode . ", ID: " . $id . ", Utilisateur ID: " . get_staff_user_id() . ", Ancien CRBT: " . $lastCrbt . ", Nouveau CRBT: " . $newCrbt . "]");
            }

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete colis from database, if used return array with key referenced
     */
    public function delete($id)
    {
        //Get colis
        $colis = $this->get($id);
        //Delete colis
        $this->db->where('id', $id);
        $this->db->delete('tblcolis');
        if ($this->db->affected_rows() > 0) {
            //Delete status colis
            if ($colis) {
                $this->db->where('code_barre', $colis->code_barre);
                $this->db->delete('tblstatus');
            }

            logActivity(_l('colis') . _l('deleted') . ' [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  barcode
     * @return mixed
     * Delete all status colis from database, if used return array with key referenced
     */
    public function delete_all_status_colis($barcode)
    {
        $this->db->where('code_barre', $barcode);
        $this->db->delete('tblstatus');
        if ($this->db->affected_rows() > 0) {
            logActivity('Tous les statuts de cette colis sont supprimé [code à barre:' . $barcode . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  barcode
     * @return mixed
     * Remove colis of bon livraison from database, if used return array with key referenced
     */
    public function remove_colis_of_bon_livraison($colisId, $barcode)
    {
        $this->db->where('colis_id', $colisId);
        $this->db->delete('tblcolisbonlivraison');
        if ($this->db->affected_rows() > 0) {
            logActivity('Colis retirer du bon de livraison [Colis Id : ' . $colisId . ', Code à barre : ' . $barcode . ']');
            return true;
        }

        return false;
    }

    /**
     * Get Status Colis
     * @return mixed
     */
    public function get_status_colis($id = '')
    {
        $this->db->where('tblstatuscolis.id_entreprise', get_entreprise_id());

        if (is_numeric($id)) {
            $this->db->where('tblstatuscolis.id', $id);
            return $this->db->get('tblstatuscolis')->row();
        }

        $this->db->order_by('tblstatuscolis.name', 'asc');
        return $this->db->get('tblstatuscolis')->result_array();
    }

    public function add_status_colis($data)
    {
        $dataInsert['id_entreprise'] = get_entreprise_id();
        $dataInsert['is_default'] = 0;
        $dataInsert['name'] = $data['name'];
        $dataInsert['color'] = $data['color'];

        if (isset($data['show_in_delivery_app'])) {
            $dataInsert['show_in_delivery_app'] = 1;
        } else {
            $dataInsert['show_in_delivery_app'] = 0;
        }

        $this->db->insert('tblstatuscolis', $dataInsert);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Statut Créé [ID:' . $insert_id . ', Nom:' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function update_status_colis($data)
    {
        $dataUpdate['name'] = $data['name'];
        $dataUpdate['color'] = $data['color'];

        if (isset($data['show_in_delivery_app'])) {
            $dataUpdate['show_in_delivery_app'] = 1;
        } else {
            $dataUpdate['show_in_delivery_app'] = 0;
        }

        $this->db->where('id', $data['id']);
        $this->db->update('tblstatuscolis', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            logActivity('Statut Modifié [ID:' . $data['id'] . ']');

            return true;
        }

        return false;
    }

    public function delete_status_colis($id)
    {
        if (is_reference_in_table('status_id', 'tblcolis', $id)) {
            return array('referenced' => true);
        }

        $this->db->where('id', $id);
        $this->db->where('is_default', 0);
        $this->db->delete('tblstatuscolis');
        if ($this->db->affected_rows() > 0) {
            logActivity('Statut Supprimé [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get States Colis
     * @return mixed
     */
    public function get_states_colis($id = '')
    {
        $this->db->where('id_entreprise', get_entreprise_id());

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tbletatcolis')->row();
        }

        return $this->db->get('tbletatcolis')->result_array();
    }

    public function add_states_colis($data)
    {
        $data['id_entreprise'] = get_entreprise_id();
        $data['is_default'] = 0;

        $this->db->insert('tbletatcolis', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Etat Crée [ID:' . $insert_id . ', Nom:' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    public function update_states_colis($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('tbletatcolis', array('name' => $data['name']));
        if ($this->db->affected_rows() > 0) {
            logActivity('Etat Modifié [ID:' . $data['id'] . ']');
            return true;
        }
        return false;
    }

    public function delete_states_colis($id)
    {

        if (is_reference_in_table('etat_id', 'tblcolis', $id)) {
            return array('referenced' => true);
        }

        $this->db->where('id', $id);
        $this->db->where('is_default !=', 1);
        $this->db->delete('tbletatcolis');

        if ($this->db->affected_rows() > 0) {
            logActivity('Etat Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Default Total colis / chart
     * @return array chart data
     */
    public function default_total_colis()
    {
        $clientid = $this->input->post('client');
        $months_report = $this->input->post('months_report');

        $custom_date_select = '1 = 1 ';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                $custom_date_select .= 'AND tblcolis.date_ramassage > DATE_SUB(now(), INTERVAL ' . $months_report . ' MONTH)';
            } else if ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select .= 'AND tblcolis.date_ramassage ="' . $from_date . '"';
                } else {
                    $custom_date_select .= 'AND (tblcolis.date_ramassage BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
        }

        // GET TOTAL COLIS EN COURS
        $this->db->select('tblcolis.id as id, tblcolis.date_ramassage as date');
        $this->db->from('tblcolis');
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }
        $this->db->where('tblcolis.status_id', 1);
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $this->db->order_by('tblcolis.id', 'desc');
        $colis_en_cours = $this->db->get()->result_array();

        // GET TOTAL COLIS RETOURNER
        $this->db->select('tblcolis.id as id, tblcolis.date_ramassage as date');
        $this->db->from('tblcolis');
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }
        $this->db->where('tblcolis.status_id', 3);
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $this->db->order_by('tblcolis.id', 'desc');
        $colis_retourner = $this->db->get()->result_array();

        //IF STATUS EGALE LIVRE OU RETOURNER CHANGE DATE
        $custom_date_select = str_replace("date_ramassage", "date_livraison", $custom_date_select);

        // GET TOTAL COLIS LIVRER
        $this->db->select('tblcolis.id as id, tblcolis.date_livraison as date');
        $this->db->from('tblcolis');
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }
        $this->db->where('tblcolis.status_id', 2);
        if (is_numeric($clientid)) {
            $this->db->where('tblcolis.id_expediteur', $clientid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $this->db->order_by('tblcolis.id', 'desc');
        $colis_livrer = $this->db->get()->result_array();

        $data = array();
        $data['months'] = array();
        $data['temp_en_cours'] = array();
        $data['temp_livrer'] = array();
        $data['temp_retourner'] = array();
        $data['total_en_cours'] = array();
        $data['total_livrer'] = array();
        $data['total_retourner'] = array();
        $data['labels'] = array();

        foreach ($colis_en_cours as $c) {
            $month = date('m', strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!m', $month);
            $month = $dateObj->format('F');
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        foreach ($colis_livrer as $c) {
            $month = date('m', strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!m', $month);
            $month = $dateObj->format('F');
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }
        foreach ($colis_retourner as $c) {
            $month = date('m', strtotime($c['date']));
            $dateObj = DateTime::createFromFormat('!m', $month);
            $month = $dateObj->format('F');
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }

        // GET MONTH FRENCH
        $month_french = get_month_french();

        $data['months'] = array_reverse($data['months'], true);
        foreach ($data['months'] as $month) {
            $cpt_en_cours = 0;
            foreach ($colis_en_cours as $key_en_cours => $c) {
                $_month = date('m', strtotime($c['date']));
                if ($key_en_cours == 0 || $cpt_en_cours == 0) {
                    $year = date('Y', strtotime($c['date']));
                    $year1 = '';
                } else {
                    $year1 = date('Y', strtotime($c['date']));
                }
                if ($key_en_cours == 0 || $cpt_en_cours == 0 || $year == $year1) {
                    $dateObj = DateTime::createFromFormat('!m', $_month);
                    $_month = $dateObj->format('F');
                    if ($month == $_month) {
                        $cpt_en_cours++;
                        $data['temp_en_cours'][$month][] = 1;
                    }
                }
            }
            if (isset($data['temp_en_cours'][$month])) {
                $total_colis_en_cours = array_sum($data['temp_en_cours'][$month]);
            } else {
                $total_colis_en_cours = 0;
            }

            $cpt_livrer = 0;
            foreach ($colis_livrer as $key_livrer => $c) {
                $_month = date('m', strtotime($c['date']));
                if ($key_livrer == 0 || $cpt_livrer == 0) {
                    $year = date('Y', strtotime($c['date']));
                    $year1 = '';
                } else {
                    $year1 = date('Y', strtotime($c['date']));
                }
                if ($key_livrer == 0 || $cpt_livrer == 0 || $year == $year1) {
                    $dateObj = DateTime::createFromFormat('!m', $_month);
                    $_month = $dateObj->format('F');
                    if ($month == $_month) {
                        $cpt_livrer++;
                        $data['temp_livrer'][$month][] = 1;
                    }
                }
            }
            if (isset($data['temp_livrer'][$month])) {
                $total_colis_livrer = array_sum($data['temp_livrer'][$month]);
            } else {
                $total_colis_livrer = 0;
            }

            $cpt_retourner = 0;
            foreach ($colis_retourner as $key_retourner => $c) {
                $_month = date('m', strtotime($c['date']));
                if ($key_retourner == 0 || $cpt_retourner == 0) {
                    $year = date('Y', strtotime($c['date']));
                    $year1 = '';
                } else {
                    $year1 = date('Y', strtotime($c['date']));
                }
                if ($key_retourner == 0 || $cpt_retourner == 0 || $year == $year1) {
                    $dateObj = DateTime::createFromFormat('!m', $_month);
                    $_month = $dateObj->format('F');
                    if ($month == $_month) {
                        $cpt_retourner++;
                        $data['temp_retourner'][$month][] = 1;
                    }
                }
            }
            if (isset($data['temp_retourner'][$month])) {
                $total_colis_retourner = array_sum($data['temp_retourner'][$month]);
            } else {
                $total_colis_retourner = 0;
            }

            foreach ($month_french as $key => $value) {
                if ($key == $month) {
                    $month = $value;
                }
            }
            array_push($data['labels'], $month);
            $data['total_en_cours'][] = $total_colis_en_cours;
            $data['total_livrer'][] = $total_colis_livrer;
            $data['total_retourner'][] = $total_colis_retourner;
        }

        $chart = array(
            'labels' => $data['labels'],
            'datasets' => array(
                array(
                    'label' => 'En cours',
                    'tension' => false,
                    'borderWidth' => 1,
                    'backgroundColor' => "#ff6f00",
                    'borderColor' => "#ff6f00",
                    'data' => $data['total_en_cours']
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
                )
            )
        );

        return $chart;
    }

    /**
     * Default Total fresh & crbt colis / chart
     * @return array chart data
     */
    public function default_fresh_crbt_colis()
    {
        $livreurid = $this->input->post('livreur');
        $months_report = $this->input->post('months_report');

        $custom_date_select = 'tblcolis.date_livraison  > DATE_SUB(now(), INTERVAL 1 DAY)';
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
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }
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
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }
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
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }
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
        if (is_numeric(get_entreprise_id())) {
            $this->db->where('tblcolis.id_entreprise', get_entreprise_id());
        }
        $this->db->where('tblcolis.status_id', 3);
        if (is_numeric($livreurid)) {
            $this->db->where('tblcolis.livreur', $livreurid);
        }
        if (!empty($custom_date_select)) {
            $this->db->where($custom_date_select);
        }
        $colis_retourner = $this->db->get()->result_array();

        $data = array();
        $data['months'] = array();
        $data['temp_frais'] = array();
        $data['temp_crbt'] = array();
        $data['temp_livrer'] = array();
        $data['temp_retourner'] = array();
        $data['total_frais'] = array();
        $data['total_crbt'] = array();
        $data['total_livrer'] = array();
        $data['total_retourner'] = array();
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
            $total_frais_colis = array_sum($data['temp_frais'][$month]);

            foreach ($crbt_colis as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_crbt'][$month][] = $c['crbt'];
                }
            }
            $total_crbt_colis = array_sum($data['temp_crbt'][$month]);

            foreach ($colis_livrer as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_livrer'][$month][] = 1;
                }
            }
            $total_colis_livrer = array_sum($data['temp_livrer'][$month]);

            foreach ($colis_retourner as $c) {
                $_month = date($attr, strtotime($c['date']));
                $dateObj = DateTime::createFromFormat('!' . $attr, $_month);
                $_month = $dateObj->format($attr1);
                if ($month == $_month) {
                    $data['temp_retourner'][$month][] = 1;
                }
            }
            $total_colis_retourner = array_sum($data['temp_retourner'][$month]);

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
                )
            )
        );

        return $chart;
    }
}

