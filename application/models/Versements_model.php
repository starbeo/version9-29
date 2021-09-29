<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Versements_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Versements
     * @return mixed
     */
    public function get($id = '', $where = array())
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        if (is_numeric($id_E)) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tbllivreurversements')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        return $this->db->get('tbllivreurversements')->result_array();
    }

    /**
     * Get Versement by etat colis livrer
     * @return mixed
     */
    public function get_versement_by_etat_colis_livrer_id($livreurId, $etatColisLivrerId)
    {
        if (is_numeric($livreurId) && is_numeric($etatColisLivrerId)) {
            $this->db->where('livreur_id', $livreurId);
            $this->db->where('etat_colis_livre_id', $etatColisLivrerId);
            return $this->db->get('tbllivreurversements')->row();
        }
    }

    /**
     * Add Versement
     * @return boolean
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $dataAdd['id_entreprise'] = $id_E;
        $dataAdd['addedfrom'] = get_staff_user_id();
        $dataAdd['type_livraison'] = $data['type_livraison'];
        if (isset($data['livreur_id']) && is_numeric($data['livreur_id'])) {
            $dataAdd['livreur_id'] = $data['livreur_id'];
        } else if (isset($data['user_point_relais']) && is_numeric($data['user_point_relais'])) {
            $dataAdd['livreur_id'] = $data['user_point_relais'];
        }
        $dataAdd['etat_colis_livre_id'] = $data['etat_colis_livre_id'];
        $dataAdd['total'] = $data['total'];
        $dataAdd['reference_transaction'] = $data['reference_transaction'];

        $this->db->insert('tbllivreurversements', $dataAdd);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Affected name to versement
            $dataUpdate['name'] = 'VRS-' . date('dmY') . '-' . $insert_id;
            $this->db->where('id', $insert_id);
            $this->db->update('tbllivreurversements', $dataUpdate);
            //Update Etat colis livrer
            $this->load->model('etat_colis_livrer_model');
            $success = $this->etat_colis_livrer_model->update_total_received($dataAdd['total'], $dataAdd['etat_colis_livre_id']);

            logActivity('Versement Crée [ID:' . $insert_id . ', Name:' . $dataUpdate['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Add Versement
     * @return boolean
     */
    public function update($data)
    {
        $versementId = $data['id'];
        //Get versement
        $versement = $this->get($versementId);

        $dataUpdate['type_livraison'] = $data['type_livraison'];
        if (isset($data['livreur_id']) && is_numeric($data['livreur_id'])) {
            $dataUpdate['livreur_id'] = $data['livreur_id'];
        } else if (isset($data['user_point_relais']) && is_numeric($data['user_point_relais'])) {
            $dataUpdate['livreur_id'] = $data['user_point_relais'];
        }
        $dataUpdate['etat_colis_livre_id'] = $data['etat_colis_livre_id'];
        $dataUpdate['total'] = $data['total'];
        $dataUpdate['reference_transaction'] = $data['reference_transaction'];
        $dataUpdate['last_update_date'] = date('Y-m-d H:i:s');

        $this->db->where('id', $versementId);
        $this->db->update('tbllivreurversements', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            //Update Etat colis livrer
            $versementName = '';
            if ($versement && is_numeric($versement->total)) {
                $versementName = $versement->name;
                $this->load->model('etat_colis_livrer_model');
                if ($versement->etat_colis_livre_id != $dataUpdate['etat_colis_livre_id']) {
                    $this->etat_colis_livrer_model->update_total_received(0, $versement->etat_colis_livre_id);
                }
                $this->etat_colis_livrer_model->update_total_received($data['total'], $dataUpdate['etat_colis_livre_id']);
            }

            logActivity('Versement Modifié [ID:' . $versementId . ', Name:' . $versementName . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete Versement
     * @return boolean
     */
    public function delete($id)
    {
        //Get versement
        $versement = $this->get($id);

        $this->db->where('id', $id);
        $this->db->delete('tbllivreurversements');
        if ($this->db->affected_rows() > 0) {
            //Update Etat colis livrer
            if ($versement && is_numeric($versement->total)) {
                $etatColisLivreId = $versement->etat_colis_livre_id;
                $this->load->model('etat_colis_livrer_model');
                $this->etat_colis_livrer_model->update_total_received(0, $etatColisLivreId);
            }

            logActivity('Versement Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}
