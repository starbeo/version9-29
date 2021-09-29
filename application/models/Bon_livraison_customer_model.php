<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bon_livraison_customer_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_expediteur_logged_in()) {
            $this->db->where('id_expediteur', get_expediteur_user_id());
        }
        
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblbonlivraisoncustomer')->row();
        }

        return $this->db->get('tblbonlivraisoncustomer')->result_array();
    }

    public function get_last_bon_livraison()
    {
        $this->db->where('id_expediteur', get_expediteur_user_id());
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('tblbonlivraisoncustomer')->row();
    }

    public function get_items_bon_livraison($id = '')
    {
        $this->db->select('tblcolisenattente.code_barre, tblcolisenattente.num_commande, tblcolisenattente.nom_complet, tblcolisenattente.telephone, tblcolisenattente.commentaire, tblcolisenattente.adresse, tblcolisenattente.ouverture, tblquartiers.name as quartier, tblcolisenattente.crbt, tblcolisenattente.date_creation, tblexpediteurs.nom, tblexpediteurs.telephone as telephone_expediteur,  tblvilles.name as ville');
        $this->db->from('tblbonlivraisoncustomercolis');
        $this->db->join('tblcolisenattente', 'tblcolisenattente.id = tblbonlivraisoncustomercolis.colis_id', 'left');
        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tblcolisenattente.id_expediteur', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolisenattente.ville', 'left');
        $this->db->join('tblquartiers', 'tblquartiers.id = tblcolisenattente.quartier', 'left');
        $this->db->where('bonlivraison_id', $id);
        return $this->db->get()->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new bon livraison
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $data['id_expediteur'] = get_expediteur_user_id();
        $data['date_created'] = date('Y-m-d H:i:s');

        $this->db->insert('tblbonlivraisoncustomer', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Update Name Bon Livraison
            $nom = 'BL-' . date('dmY') . '-' . $insert_id;
            $this->db->where('id', $insert_id);
            $this->db->update('tblbonlivraisoncustomer', array('nom' => $nom));
            //Add Notification to admin
            $_data['description'] = 'Nouveau Bon Livraison Ajouté [Nom: <b>' . $nom . '</b>]';
            $_data['link'] = NULL;
            add_notification_to_admin($_data);

            logActivityCustomer('Nouveau Bon Livraison Ajouté [Bon Livraison: ' . $nom . ', ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Update bon livraison
     */
    public function update($data = array(), $id)
    {
        $data['nom'] = 'BL-' . date('dmY') . '-' . $id;
        $data['date_created'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tblbonlivraisoncustomer', $data);
        if ($this->db->affected_rows() > 0) {
            logActivityCustomer('Bon Livraison Modifié [Bon Livraison: ' . $data['nom'] . ', ID: ' . $id . ']');
            return true;
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
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        //check if colis exists in list colis bon livraison
        $exists = total_rows('tblbonlivraisoncustomercolis', array('bonlivraison_id' => $bonLivraisonId, 'colis_id' => $colisId, 'id_entreprise' => $id_E));
        if ($exists == 0) {
            $id_expediteur = get_expediteur_user_id();
            $date_created = date('Y-m-d H:i:s');

            //Add colis to list colis bon livraison
            $this->db->insert('tblbonlivraisoncustomercolis', array(
                'bonlivraison_id' => $bonLivraisonId,
                'colis_id' => $colisId,
                'date_created' => $date_created,
                'id_expediteur' => $id_expediteur,
                'id_entreprise' => $id_E
                )
            );
            $insert_id = $this->db->insert_id();

            if ($insert_id) {
                //Affectation du numéro du bon de livraison 
                $this->db->where('id', $colisId);
                $this->db->update('tblcolisenattente', array('num_bonlivraison' => $bonLivraisonId));

                logActivityCustomer('Nouveau Colis ajouté au bon de Livraison [Bon ID: ' . $bonLivraisonId . ', Colis en attente ID: ' . $colisId . ']');

                return $insert_id;
            }
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
        //Get ID Colis en attente
        if (is_expediteur_logged_in()) {
            $id_expediteur = get_expediteur_user_id();
            $this->db->where('id_expediteur', $id_expediteur);
        }
        $this->db->where('id', $colisBonLivraisonId);
        $colisBonLivraison = $this->db->get('tblbonlivraisoncustomercolis')->row();
        if ($colisBonLivraison) {
            //Delete colis en attente du bon livraison
            $this->db->where('id', $colisBonLivraisonId);
            $this->db->delete('tblbonlivraisoncustomercolis');
            if ($this->db->affected_rows() > 0) {
                //Update numero bon livraison to colis
                if (is_numeric($colisBonLivraison->colis_id)) {
                    $this->db->where('id', $colisBonLivraison->colis_id);
                    $this->db->update('tblcolisenattente', array('num_bonlivraison' => NULL));
                }

                logActivityCustomer('Colis en attente Bon Livraison Supprimé [ID: ' . $colisBonLivraisonId . ']');
                return true;
            }
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
	   logActivity('Tentative Bon livraison client Supprimé [ID: ' . $id . ']');
        return false; 
	    if (is_expediteur_logged_in()) {
            $clientId = get_expediteur_user_id();
            //Delete bon livraison
            $this->db->where('id_expediteur', $clientId);
            $this->db->where('id', $id);
            $this->db->delete('tblbonlivraisoncustomer');
            if ($this->db->affected_rows() > 0) {
                //Update numero bon livraison to colis
                $this->db->where('id_expediteur', $clientId);
                $this->db->where('num_bonlivraison', $id);
                $this->db->update('tblcolisenattente', array('num_bonlivraison' => null));

                //Delete colis en attente bon livraison
                $this->db->where('id_expediteur', $clientId);
                $this->db->where('bonlivraison_id', $id);
                $this->db->delete('tblbonlivraisoncustomercolis');

                logActivity('Bon livraison Supprimé [ID: ' . $id . ']');
                return true;
            }
        }

        return false;
    }
}
