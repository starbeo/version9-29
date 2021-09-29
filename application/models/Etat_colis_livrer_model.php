<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etat_colis_livrer_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $select = '', $orderBy = '', $startLimit = '', $endLimit = '')
    {
        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if (is_numeric($id)) {
            $this->db->where('tbletatcolislivre.id', $id);
            return $this->db->get('tbletatcolislivre')->row();
        }

        $this->db->order_by('tbletatcolislivre.id', 'desc');

        if (is_numeric($startLimit) && is_numeric($endLimit)) {
            $this->db->limit($startLimit, $endLimit);
        }

        return $this->db->get('tbletatcolislivre')->result_array();
    }

    public function get_last_etat_colis_livrer()
    {
        $this->db->where('user_point_relais', get_staff_user_id());
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('tbletatcolislivre')->row();
    }

    public function get_items_etat_colis_livrer($id = '')
    {
        $this->db->select('tblcolis.code_barre, tblcolis.num_commande, tblcolis.nom_complet, tblcolis.telephone, tblcolis.adresse, tblquartiers.name as quartier, tblcolis.crbt, tblcolis.date_ramassage, tblexpediteurs.id as expediteur_id, tblexpediteurs.nom, tblstatuscolis.name as status, tblexpediteurs.logo, tblvilles.name as ville, tblcolis.status_reel, tblcolis.date_livraison, tblcolis.commentaire');
        $this->db->from('tbletatcolislivreitems');
        $this->db->join('tblcolis', 'tblcolis.id = tbletatcolislivreitems.colis_id', 'left');
        $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblcolis.status_id', 'left');
        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tblcolis.id_expediteur', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');
        $this->db->join('tblquartiers', 'tblquartiers.id = tblcolis.quartier', 'left');
        $this->db->where('tbletatcolislivreitems.etat_id', $id);
        return $this->db->get()->result_array();
    }

    public function get_data_export($where = array())
    {
        $this->db->select('tbletatcolislivre.nom, CONCAT(CONCAT(UCASE(LEFT(a.firstname, 1)), SUBSTRING(LOWER(a.firstname), 2)), " ", CONCAT(UCASE(LEFT(a.lastname, 1)), SUBSTRING(LOWER(a.lastname), 2))) as fullname_livreur, tblvilles.name as ville, tbletatcolislivre.etat, tbletatcolislivre.status, (SELECT COUNT(id) FROM tbletatcolislivreitems WHERE etat_id = tbletatcolislivre.id) as nbr_colis, tbletatcolislivre.total, tbletatcolislivre.commision, (tbletatcolislivre.total - tbletatcolislivre.commision) as total_a_payer, tbletatcolislivre.total_received, tbllivreurversements.date_created as last_date_versement, tbletatcolislivre.manque as reste, tbllivreurversements.reference_transaction, tbletatcolislivre.justif, CONCAT(CONCAT(UCASE(LEFT(b.firstname, 1)), SUBSTRING(LOWER(b.firstname), 2)), " ", CONCAT(UCASE(LEFT(b.lastname, 1)), SUBSTRING(LOWER(b.lastname), 2))) as fullname_utilisateur, tbletatcolislivre.date_created');
        $this->db->from('tbletatcolislivre');
        $this->db->join('tbllivreurversements', 'tbllivreurversements.etat_colis_livre_id = tbletatcolislivre.id', 'left');
        $this->db->join('tblstaff as a', 'a.staffid = tbletatcolislivre.id_livreur', 'left');
        $this->db->join('tblstaff as b', 'b.staffid = tbletatcolislivre.id_utilisateur', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = a.city', 'left');

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tbletatcolislivre.id', 'desc');
        return $this->db->get()->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new etat
     */
    public function add($data)
    {
        //Champs manuelle
        $data['etat'] = 1;
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
        if (isset($data['user_point_relais']) && !is_numeric($data['user_point_relais'])) {
            unset($data['user_point_relais']);
        }
        //Vérification du champ manque
        if (isset($data['manque']) && !is_numeric($data['manque'])) {
            $data['manque'] = 0;
        }
        //Vérification du champ justif
        if (isset($data['justif']) && empty($data['justif'])) {
            unset($data['justif']);
        }
        //Ajout de l'état colis livrer
        $this->db->insert('tbletatcolislivre', $data);
        $insertId = $this->db->insert_id();
        if ($insertId) {
            //Update Name Etat Colis Livrer
            $nom = 'ECL-' . date('dmY') . '-' . $insertId;
            $this->db->where('id', $insertId);
            $this->db->update('tbletatcolislivre', array('nom' => $nom));
            //Ajouter la traçabilité
            logActivity('Nouveau Etat Colis Livrer Ajouté [Etat Colis Livrer: ' . $nom . ', ID: ' . $insertId . ']');

            return $insertId;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Update etat
     */
    public function update($data, $id)
    {
        //Annulation des champs
        if (isset($data['etat_id'])) {
            unset($data['etat_id']);
        }
        if (isset($data['etat_colis_livrer_status'])) {
            unset($data['etat_colis_livrer_status']);
        }
        //Vérification du champ justif
        if (isset($data['justif']) && empty($data['justif'])) {
            unset($data['justif']);
        }

        if (isset($data['commision_refuse'])) {
            $data['refuse_commision']=$data['commision_refuse'];
            unset($data['commision_refuse']);
        }
        //Si $data est vide : Envoyer de l'espace point relais
        if (empty($data)) {
            $data['nom'] = 'ECL-' . date('dmY') . '-' . $id;
            $data['date_created'] = date('Y-m-d H:i:s');
        }
        //Modification de l'état colis livrer
        $this->db->where('id', $id);
        $this->db->update('tbletatcolislivre', $data);
        if ($this->db->affected_rows() > 0) {
            //Ajouter la traçabilité
            logActivity('Etat Colis Livrer Updated [ID: ' . $id . ']');

            return $id;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Update etat
     */
    public function update_total_received($totalReceived, $id)
    {
        // Get etat colis livrer
        $etat = $this->get($id);
        $typePaiement = '';
        if ($etat) {
            $idLiveur = $etat->id_livreur;
            if (is_numeric($idLiveur)) {
                // Get staff
                $this->load->model('staff_model');
                $staff = $this->staff_model->get($idLiveur);
                if ($staff) {
                    $typePaiement = $staff->payment_type;
                }
            }
        }

        $this->db->where('id', $id);
        $this->db->set('etat', 2);
        $this->db->set('total_received', $totalReceived);
        if (is_numeric($typePaiement) && $typePaiement == 1) {
            $this->db->set('manque', 'total_received - total', FALSE);
        } else {
            $this->db->set('manque', 'total_received - (total - commision)', FALSE);
        }
        $this->db->update('tbletatcolislivre');
        if ($this->db->affected_rows() > 0) {
            logActivity('Etat Colis Livrer Updated Total Received [Total Received : ' . $totalReceived . ', ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer etatid
     * @return boolean
     * Update etat colis livrer etat Non Regle / Regle
     */
    public function change_etat($id, $etat)
    {
        $this->db->where('id', $id);
        $this->db->update('tbletatcolislivre', array('etat' => $etat));
        if ($this->db->affected_rows() > 0) {
            logActivity('Etat colis livrer STATUT Changé [EtatColisLivrerID: ' . $id . ' Statut(Non Regle / Regle): ' . $status . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer statusId
     * @return boolean
     * Change etat colis livrer
     */
    public function change_status($etatId, $etatEtatColisLivrer, $etatColis)
    {
        // Get etat colis livrer
        $etat = $this->get($etatId);
        if ($etat) {
            $items = $this->get_items_etat_colis_livrer($etatId);
            if (count($items) > 0) {
                //Update etat colis
                $this->db->where('num_etatcolislivrer', $etatId);
                $this->db->update('tblcolis', array('etat_id' => $etatColis));
                // Update etat Etat colis livrer
                $this->db->where('id', $etatId);
                $this->db->update('tbletatcolislivre', array('status' => $etatEtatColisLivrer));
                if ($this->db->affected_rows() > 0) {
                    logActivity('Etat colis livrer Etat Changé [EtatColisLivrerID: ' . $etatId . ' Etat(En attente / Valider): ' . $etatEtatColisLivrer . ']');
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new colis to etat colis livrer
     */
    public function add_colis_to_etat_colis_livrer($etatId, $colisId)
    {
        //Vérification si la coli existe déjà dans la liste des colis de cet état colis livrer
        $exists = total_rows('tbletatcolislivreitems', array('etat_id' => $etatId, 'colis_id' => $colisId));
        if ($exists == 0) {
            //Ajout de la coli dans la liste des colis de l'état colis livrer
            $this->db->insert('tbletatcolislivreitems', array(
                'etat_id' => $etatId,
                'colis_id' => $colisId,
                'date_created' => date('Y-m-d H:i:s'),
                'id_utilisateur' => get_staff_user_id(),
                'id_entreprise' => get_entreprise_id()
                )
            );
            $insertId = $this->db->insert_id();
            if ($insertId) {
                //Modification de l'état de la coli et l'affectation du numéro de l'état colis livrer
                $this->db->where('id', $colisId);
                $this->db->update('tblcolis', array('etat_id' => 2, 'num_etatcolislivrer' => $etatId));
                //Chargement du model
                $this->load->model('colis_model');
                //Récupération du total CRBT Colis
                $totalColis = $this->colis_model->get_total_colis($colisId);
                //Récupération de la coli
                $coli = $this->colis_model->get($colisId);
                //Calcule de la commision
                $totalCommision = 0;
                $totalCommisionRefuse = 0;
                if ($coli && $coli->type_livraison == 'a_domicile' && is_numeric($coli->livreur)) {
                    if ($coli->status_reel == 2) {
                        //Chargement du model
                        $this->load->model('commisions_model');
                        $totalCommision = $this->commisions_model->get_commision_livreur($coli->livreur, $coli->ville);
                    } else if ($coli->status_reel == 9) {
                        //$totalCommision = 45;
                        $this->load->model('commisions_model');

                        $totalCommision = $this->commisions_model->get_refuse_commision_livreur($coli->livreur, $coli->ville);
                        $totalCommisionRefuse = $this->commisions_model->get_refuse_commision_livreur($coli->livreur, $coli->ville);

                    }
                }
                //Modification de l'état colis livrer
                $this->db->where('id', $etatId);
                $this->db->set('total', 'total + ' . $totalColis, FALSE);
                $this->db->set('manque', 'total_received - (total - ' . $totalCommision . ')', FALSE);
                $this->db->set('commision', 'commision + ' . $totalCommision, FALSE);
                $this->db->set('refuse_commision', 'refuse_commision + ' . $totalCommisionRefuse, FALSE);
                $this->db->update('tbletatcolislivre');
                //Ajouter la traçabilité
                logActivity('Nouvelle Colis ajouté à l\'Etat Colis Livré [Etat ID: ' . $etatId . ', Colis id : ' . $colisId . ', Total colis : ' . $totalColis . ', Total commision : ' . $totalCommision . ']');

                return array('id' => $insertId, 'total' => $totalColis, 'commision' => $totalCommision, 'refuse_commision' => $totalCommisionRefuse);
            }
        } else {
            return array('colis_already_exists_in_the_etat_colis_livrer' => true);
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete colis etat colis livrer from database
     */
    public function remove_colis_to_etat_colis_livrer($id)
    {
        //Récupération du numéro de la coli
        $this->db->where('id', $id);
        $item = $this->db->get('tbletatcolislivreitems')->row();
        if (!is_numeric($item->colis_id)) {
            return false;
        }

        //Suppression de la coli de l'état colis livrer
        $this->db->where('id', $id);
        $this->db->delete('tbletatcolislivreitems');
        if ($this->db->affected_rows() > 0) {
            $colisId = $item->colis_id;
            $etatColisLivrerId = $item->etat_id;
            //Modification de l'état de la coli et l'affectation du numéro de l'état colis livrer
            $this->db->where('id', $colisId);
            $this->db->update('tblcolis', array('etat_id' => 1, 'num_etatcolislivrer' => NULL));
            //Chargement du model
            $this->load->model('colis_model');
            //Récupération du total CRBT Colis
            $totalColis = $this->colis_model->get_total_colis($colisId);
            //Récupération de la coli
            $coli = $this->colis_model->get($colisId);
            //Calcule de la commision
            $totalCommision = 0;
            $totalCommisionRefuse = 0;
            if ($coli && $coli->type_livraison == 'a_domicile' && is_numeric($coli->livreur)) {
                if ($coli->status_reel == 2) {
                    //Chargement du model
                    $this->load->model('commisions_model');
                    $totalCommision = $this->commisions_model->get_commision_livreur($coli->livreur, $coli->ville);
                } else if ($coli->status_reel == 9) {
                   //$totalCommision = $coli->frais;
                    $this->load->model('Commisions_model', 'commisions');
                    $totalCommision = $this->commisions_model->get_refuse_commision_livreur($coli->livreur, $coli->ville);
                    $totalCommisionRefuse = $this->commisions_model->get_refuse_commision_livreur($coli->livreur, $coli->ville);
                }
            }
            //Modification de l'état colis livrer
            $this->db->where('id', $etatColisLivrerId);
            $this->db->set('total', 'total - ' . $totalColis, FALSE);
            $this->db->set('manque', 'total_received - (total - ' . $totalCommision . ')', FALSE);
            $this->db->set('commision', 'commision - ' . $totalCommision, FALSE);
            $this->db->set('refuse_commision', 'refuse_commision + ' . $totalCommisionRefuse, FALSE);

            $this->db->update('tbletatcolislivre');
            //Ajouter la traçabilité
            logActivity('Colis supprimé de l\'Etat Colis Livré [Etat ID: ' . $etatColisLivrerId . ', Colis id : ' . $colisId . ', Total colis : ' . $totalColis . ', Total commision : ' . $totalCommision . ']');

            return array('total' => $totalColis, 'commision' => $totalCommision,'refuseCommision' => $totalCommisionRefuse);
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete etat colis livrer from database, if used return array with key referenced
     */
    public function delete($id)
    {
        //Suppression de l'état colis livrer
        $this->db->where('id', $id);
        $this->db->delete('tbletatcolislivre');
        if ($this->db->affected_rows() > 0) {
            //Suppression des colis de l'état colis livrer
            $this->db->where('etat_id', $id);
            $this->db->delete('tbletatcolislivreitems');
            //Modification de l'état des colis et l'affectation de la valeur NULL aux colis qui ont le numéro de cet état colis livrer
            $this->db->where('num_etatcolislivrer', $id);
            $this->db->update('tblcolis', array('etat_id' => 1, 'num_etatcolislivrer' => NULL));
            //Ajouter la traçabilité
            logActivity('Etat Colis Livrer Supprimé [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    public function getcolisrefuse($id = '',$status_reel)
    {

        if (is_numeric($id)) {
            $this->db->where('tbletatcolislivreitems.etat_id', $id);
            $query = $this->db->get('tbletatcolislivreitems');
            $dt = 0;
 
         if ($query->result() != null){
           foreach ($query->result() as $row)
            {
              $id_coli = $row->colis_id;
                $array = array('tblcolis.id' => $id_coli);
                $this->db->where($array);
                $data = $this->db->get('tblcolis')->row();
            if ($data != null)
                    {
                        if ( $data->status_reel  == $status_reel)
                            $dt ++;
                    }
            }
}
            return $dt;

        }
        return 0;



    }

    public function getbyetlid($id = '')
    {


        if (is_numeric($id)) {
            $this->db->where('tbletatcolislivre.facture_etl', $id);
            return $this->db->get('tbletatcolislivre')->row();
        }

        $this->db->order_by('tbletatcolislivre.facture_etl', 'desc');


        return $this->db->get('tbletatcolislivre')->result_array();
    }



}


