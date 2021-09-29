<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bon_livraison_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $select = '', $orderBy = '', $startLimit = '', $endLimit = '')
    {
        if (is_numeric($id)) {
            $this->db->where('tblbonlivraison.id', $id);
            return $this->db->get('tblbonlivraison')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tblbonlivraison.id', 'desc');

        if (is_numeric($startLimit) && is_numeric($endLimit)) {
            $this->db->limit($startLimit, $endLimit);
        }

        return $this->db->get('tblbonlivraison')->result_array();
    }

    public function get_items_bon_livraison($id = '')
    {
        $this->db->select('tblcolis.id as colis_id, tblcolis.code_barre, tblcolis.num_commande, tblcolis.nom_complet, tblcolis.telephone, tblcolis.adresse, tblcolis.ouverture, tblquartiers.name as quartier, tblcolis.crbt, tblcolis.date_ramassage, tblexpediteurs.id as expediteur_id, tblexpediteurs.nom, tblexpediteurs.telephone as telephone_expediteur, tblexpediteurs.logo, tblvilles.name as ville, tblcolis.status_reel, tblcolis.date_livraison, tblcolis.commentaire');
        $this->db->from('tblcolisbonlivraison');
        $this->db->join('tblcolis', 'tblcolis.id = tblcolisbonlivraison.colis_id', 'left');
        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tblcolis.id_expediteur', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');
        $this->db->join('tblquartiers', 'tblquartiers.id = tblcolis.quartier', 'left');
        $this->db->where('bonlivraison_id', $id);
        return $this->db->get()->result_array();
    }

    public function get_types()
    {
        $types = array(
            array('id' => 1, 'name' => _l('output')),
            array('id' => 2, 'name' => _l('returned'))
        );

        return $types;
    }

    /**
     * @param  integer bonLivraisonId
     * @return boolean
     * Change status bon livraison
     */
    public function change_status($bonLivraisonId, $statusBonLivraison)
    {
        // Get bon livraison
        $bonLivraison = $this->get($bonLivraisonId);
        if ($bonLivraison) {
            if ($bonLivraison && $bonLivraison->status == 1) {
                $items = $this->get_items_bon_livraison($bonLivraisonId);
                if (count($items) > 0) {
                    $this->load->model('status_model');
                    $this->load->model('colis_model');
                    foreach ($items as $item) {
                        //Get Colis
                        $colis = $this->colis_model->get($item['colis_id']);
                        if (!is_null($colis)) {
                            $colisId = $colis->id;
                            //Changement status id, status reel et livreur du bon de livraison
                            if ($bonLivraison->type == 1) {
                                $statusId = 1;
                                $statusReel = 4;
                                $emplacementId = 10;
                            } else if ($bonLivraison->type == 2) {
                                $statusId = 1;
                                $statusReel = 13;
                                $emplacementId = 9;
                            }
                            //Update colis
                            $this->db->where('id', $colisId);
                            $this->db->update('tblcolis', array('status_id' => $statusId, 'status_reel' => $statusReel));
                            //Add nouveau status
                            $dataStatus = array();
                            $dataStatus['coli_id'] = $colisId;
                            $dataStatus['code_barre_verifie'] = $colis->code_barre;
                            $dataStatus['telephone'] = $colis->telephone;
                            $dataStatus['clientid'] = $colis->id_expediteur;
                            $dataStatus['crbt'] = $colis->crbt;
                            $dataStatus['type'] = $statusReel;
                            $dataStatus['emplacement_id'] = $emplacementId;
                            $dataStatus['date_reporte'] = '0000-00-00';
                            $dataStatus['motif'] = 0;
                            $this->status_model->add($dataStatus);
                            //Add colis in tblcoliscashplus
                            if ($bonLivraison->type_livraison == 'point_relai') {
                                $dataColis['colis_id'] = $colisId;
                                $this->colis_model->add_colis_to_colis_cash_plus($dataColis);
                            }
                        }
                    }
                    //Update status bon de livraison
                    $this->db->where('id', $bonLivraisonId);
                    $this->db->update('tblbonlivraison', array('status' => $statusBonLivraison));

                    return true;
                }
            } else if ($bonLivraison && $bonLivraison->status == 2) {
                return array('bon_livraison_confirmer' => true);
            }
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new bon livraison
     */
    public function add($data)
    {
        //Champs manuelle
        $data['status'] = 1;
        $data['id_utilisateur'] = get_staff_user_id();
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['id_entreprise'] = get_entreprise_id();
        //Vérification du champ type_livraison
        if (is_point_relais_logged_in()) {
            $data['type_livraison'] = 'point_relai';
        } else {
            if (isset($data['type_livraison'])) {
                $data['type_livraison'] = $data['type_livraison'];
            }
        }
        //Vérification du champ point relai id
        if (isset($data['point_relai_id']) && !is_numeric($data['point_relai_id'])) {
            unset($data['point_relai_id']);
        }
        //Vérification du champ id livreur
        if (isset($data['id_livreur']) && !is_numeric($data['id_livreur'])) {
            unset($data['id_livreur']);
        }
        //Ajout du bon de livraison
        $this->db->insert('tblbonlivraison', $data);
        $insertId = $this->db->insert_id();
        if ($insertId) {
            //Modification du nom du bon de livraison
            $nom = 'BL-' . date('dmY') . '-' . $insertId;
            $this->db->where('id', $insertId);
            $this->db->update('tblbonlivraison', array('nom' => $nom));
            //Ajouter la traçabilité
            logActivity('Nouveau Bon Livraison Ajouté [Bon Livraison: ' . $nom . ', ID: ' . $insertId . ']');

            return $insertId;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new colis to bon livraison
     */
    public function add_colis_to_bon_livraison($bonLivraisonId, $colisId)
    {
        //Récupération du bon de livraison
        $bonlivraison = $this->get($bonLivraisonId);
        if ($bonlivraison->status == 1) {
            //Vérification si la coli existe déjà dans la liste des colis de ce bon de livraison
            $exists = total_rows('tblcolisbonlivraison', array('colis_id' => $colisId));
            if ($exists == 0) {
                //Vérification si le statut de la coli est livré ou bien retourné
                $existsColis = total_rows('tblcolis', 'id = ' . $colisId . ' AND status_reel IN (2,4,3)');
                if ($existsColis == 0) {
                    //Ajouter la traçabilité
                    logActivity('Coli Ajouté au bon de livraison [Bon de livraison ID: ' . $bonLivraisonId . ', Colis ID : ' . $colisId . ']');
                    //Ajout de la coli dans la liste des colis du bon de livraison
                    $this->db->insert('tblcolisbonlivraison', array(
                            'bonlivraison_id' => $bonLivraisonId,
                            'colis_id' => $colisId,
                            'date_created' => date('Y-m-d H:i:s'),
                            'id_utilisateur' => get_staff_user_id(),
                            'id_entreprise' => get_entreprise_id()
                        )
                    );
                    $insertId = $this->db->insert_id();
                    if ($insertId) {
                        //Affectation du numéro du bon de livraison à la colis
                        $this->db->where('id', $colisId);
                        $this->db->update('tblcolis', array('num_bonlivraison' => $bonLivraisonId, 'livreur' => $bonlivraison->id_livreur));

                        return $insertId;
                    }
                } else {
                    return array('colis_already_delivered_or_returned' => true);
                }
            } else {
                return array('colis_already_exists_in_the_delivery_note' => true);
            }
        } else {
            return array('bon_livraison_confirmer' => true);
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete colis bon livraison from database
     */
    public function remove_colis_to_bon_livraison($colisBonLivraisonId)
    {
        //Récupération de la ligne de la coli qui va etre supprimer
        $this->db->where('id', $colisBonLivraisonId);
        $colisBonLivraison = $this->db->get('tblcolisbonlivraison')->row();
        //Suppression de la coli du bon de livraison
        $this->db->where('id', $colisBonLivraisonId);
        $this->db->delete('tblcolisbonlivraison');
        if ($this->db->affected_rows() > 0) {
            if (is_numeric($colisBonLivraison->colis_id)) {
                //Modification du numéro du bon de livraison de la coli
                $this->db->where('id', $colisBonLivraison->colis_id);
                $this->db->update('tblcolis', array('num_bonlivraison' => NULL));
                //Récupération de la coli
                $this->load->model('colis_model');
                $coli = $this->colis_model->get($colisBonLivraison->colis_id);
                if ($coli) {
                    $barcode = $coli->code_barre;
                    //Récupération du dernier statut
                    $this->load->model('status_model');
                    $lastStatus = $this->status_model->get('', 'code_barre = "' . $barcode . '"', 'DESC', 1);
                    if ($lastStatus && is_numeric($lastStatus[0]['id'])) {
                        $statusId = $lastStatus[0]['id'];
                        //Supprimer le dernier statut de la coli
                        $this->status_model->delete($statusId);
                    }
                }
            }
            //Ajouter la traçabilité
            logActivity('Colis Bon Livraison Supprimé [ID: ' . $colisBonLivraisonId . ', Colis ID : ' . $colisBonLivraison->colis_id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete bon livraison from database, if used return array with key referenced
     */
    public function delete($id)
    {
        do_action('before_bon_livraison_deleted', $id);

        if (is_reference_in_table('bonlivraison_id', 'tblcolisbonlivraison', $id)) {
            return array('referenced' => true);
        }

        //Delete bon livraison
        $this->db->where('id', $id);
        $this->db->delete('tblbonlivraison');
        if ($this->db->affected_rows() > 0) {
            //Update numero bon livraison to colis
            $this->db->where('num_bonlivraison', $id);
            $this->db->update('tblcolis', array('num_bonlivraison' => null));
            //Delete colis bon livraison
            $this->db->where('bonlivraison_id', $id);
            $this->db->delete('tblcolisbonlivraison');

            logActivity('Bon livraison Supprimé [ID: ' . $id . ']' .get_staff_user_id());
            return true;
        }

        return false;
    }
    public function get_colis_sms($code_barre = '')
    {

        if ($code_barre !='') {

            $array = array('tblsmsactivitylog.code_barre' => $code_barre);
            $this->db->where($array);
            $data = $this->db->get('tblsmsactivitylog')->row();
            return $data->sent;


        }
        return 'work';



    }

    public function get_bl_comment($bl_id = '')
    {

        if ($bl_id !='') {

            $array = array('tblcommentaire.bon_livraison_id' => $bl_id);
            $this->db->where($array);
            $data = $this->db->get('tblcommentaire')->row();
            return $data->commentaire;


        }
        return 'work';



    }
    public function get_bl_name($bl_id = '')
    {

        if ($bl_id !='') {

            $array = array('tblbonlivraison.id' => $bl_id);
            $this->db->where($array);
            $data = $this->db->get('tblbonlivraison')->row();
            return $data->nom;


        }
        return 'work';



    }



    public function get_livreur_id($id = '', $active = 1, $where = array(), $select = '')
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
        $columns =
            'tblbonlivraison.nom,
              tblbonlivraison.type,
             tblbonlivraison.status,
            tblbonlivraison.id_livreur, 
            tblbonlivraison.date_created,
            tblbonlivraison.type_livraison,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_livreur,
            CONCAT(a.firstname, " ", a.lastname) as fullname_staff
            ';

        $this->db->select($columns);

   $this->db->join('tblstaff', 'tblstaff.staffid = tblbonlivraison.id_livreur', 'left');
   $this->db->join('tblstaff as a', 'a.staffid = tblbonlivraison.id_utilisateur', 'left');
    //    $this->db->join('tbldepartementobjets', 'tbldepartementobjets.id = tbldemandes.object', 'left');


        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tblbonlivraison.id', 'desc');
        $this->db->limit(100000);
        return $this->db->get('tblbonlivraison')->result_array();
    }

}

