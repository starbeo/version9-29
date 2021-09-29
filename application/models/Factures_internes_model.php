<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factures_internes_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('tblfacturesinternes.id', $id);
            $facture_interne = $this->db->get('tblfacturesinternes')->row();
            if ($facture_interne) {
                $facture_interne->items = $this->get_items_facture_interne($id);
                $this->load->model('payments_model');
                $facture_interne->payments = $this->payments_model->get_facture_interne_payments($id);
            }

            return $facture_interne;
        }

        return $this->db->get('tblfacturesinternes')->result_array();
    }

    public function get_last_facture_interne()
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('tblfacturesinternes')->row();
    }

    public function get_items_facture_interne($id = '')
    {
        $this->db->select('tblfactures.id as factureid, tblfactures.nom, tblfactures.total_crbt, tblfactures.total_frais, tblfactures.total_refuse, tblfactures.total_parrainage, tblfactures.total_remise, tblfactures.total_net, tblfactures.type, tblfactures.status, tblfactures.date_created, tblexpediteurs.nom as client, tblexpediteurs.id as client_id,  
            tblexpediteurs.logo, tblexpediteurs.rib as client_rib, tblexpediteurs.contact as client_contact, tblexpediteurs.marque as client_banque');
        $this->db->from('tblfactureinterneitems');
        $this->db->join('tblfactures', 'tblfactures.id = tblfactureinterneitems.facture_id', 'left');
        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tblfactures.id_expediteur', 'left');
        $this->db->where('tblfactureinterneitems.facture_interne_id', $id);
        return $this->db->get()->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new facture interne
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $data['id_utilisateur'] = get_staff_user_id();
        $data['date_created'] = date('Y-m-d H:i:s');

        $this->db->insert('tblfacturesinternes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Update Name Facture Interne
            $nom = 'FCT-INT-' . date('dmY') . '-' . $insert_id;
            $this->db->where('id', $insert_id);
            $this->db->update('tblfacturesinternes', array('nom' => $nom));

            logActivity('Nouveau Facture Interne Ajouté [Facture Interne: ' . $nom . ', ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Update facture interne
     */
    public function update($data, $id)
    {
        unset($data['facture_id']);

        $this->db->where('id', $id);
        $this->db->update('tblfacturesinternes', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Facture Interne Updated [Facture Interne: ' . $nom . ', ID: ' . $id . ']');
            return $id;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new facture to facture interne
     */
    public function add_facture_to_facture_interne($facture_interne_id, $facture_id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        //check if facture exists in list factures facture interne
        $exists = total_rows('tblfactureinterneitems', array('facture_interne_id' => $facture_interne_id, 'facture_id' => $facture_id, 'id_entreprise' => $id_E));
        if ($exists == 0) {
            $id_utilisateur = get_staff_user_id();
            $date_created = date('Y-m-d H:i:s');

            //Add facture to list facture interne
            $this->db->insert('tblfactureinterneitems', array(
                'facture_interne_id' => $facture_interne_id,
                'facture_id' => $facture_id,
                'date_created' => $date_created,
                'id_utilisateur' => $id_utilisateur,
                'id_entreprise' => $id_E
                )
            );
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                //Affectation du numéro de facture
                $this->db->where('id', $facture_id);
                $this->db->update('tblfactures', array('status' => 2, 'num_factureinterne' => $facture_interne_id));
                //Update Total(crbt, frais, rest) Facture Interne
                $success = $this->update_total_facture_interne($facture_interne_id, $facture_id);
                $total_crbt = 0;
                if (!is_null($success['total_crbt'])) {
                    $total_crbt = $success['total_crbt'];
                }
                $total_frais = 0;
                if (!is_null($success['total_frais'])) {
                    $total_frais = $success['total_frais'];
                }
                $total_refuse = 0;
                if (!is_null($success['total_refuse'])) {
                    $total_refuse = $success['total_refuse'];
                }
                $total_parrainage = 0;
                if (!is_null($success['total_parrainage'])) {
                    $total_parrainage = $success['total_parrainage'];
                }
                $total_remise = 0;
                if (!is_null($success['total_remise'])) {
                    $total_remise = $success['total_remise'];
                }
                $total_net = 0;
                if (!is_null($success['total_net'])) {
                    $total_net = $success['total_net'];
                }

                logActivity('Nouvelle Facture ajouté à la facture interne [Facture Interne ID: ' . $facture_interne_id . ', Facture ID : ' . $facture_id . ', Total CRBT facture : ' . $total_crbt . ', Total Frais facture : ' . $total_frais . ']');
                return array('id' => $insert_id, 'total_crbt' => $total_crbt, 'total_frais' => $total_frais, 'total_refuse' => $total_refuse, 'total_parrainage' => $total_parrainage, 'total_remise' => $total_remise, 'total_net' => $total_net);
            }
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete facture facture interne from database
     */
    public function remove_facture_to_facture_interne($id)
    {
        $affected_rows = 0;
        //Get ID Facture Interne
        $this->db->where('id', $id);
        $item = $this->db->get('tblfactureinterneitems')->row();
        if (!$item) {
            return false;
        }
        //Facture Id
        $facture_id = $item->facture_id;
        //Facture Interne Id
        $facture_interne_id = $item->facture_interne_id;
        //Delete item facture interne
        $this->db->where('id', $id);
        $this->db->delete('tblfactureinterneitems');
        //Update numero facture interne to facture
        $this->db->where('id', $facture_id);
        $this->db->update('tblfactures', array('status' => 1, 'num_factureinterne' => NULL));
        if ($this->db->affected_rows() > 0) {
            $affected_rows++;
        }
        //Update Total(crbt, frais, rest) Facture Interne
        $success = $this->update_total_facture_interne($facture_interne_id, $facture_id, false);
        $total_crbt = 0;
        if (!is_null($success['total_crbt'])) {
            $total_crbt = $success['total_crbt'];
        }
        $total_frais = 0;
        if (!is_null($success['total_frais'])) {
            $total_frais = $success['total_frais'];
        }
        $total_refuse = 0;
        if (!is_null($success['total_refuse'])) {
            $total_refuse = $success['total_refuse'];
        }
        $total_parrainage = 0;
        if (!is_null($success['total_parrainage'])) {
            $total_parrainage = $success['total_parrainage'];
        }
        $total_remise = 0;
        if (!is_null($success['total_remise'])) {
            $total_remise = $success['total_remise'];
        }
        $total_net = 0;
        if (!is_null($success['total_net'])) {
            $total_net = $success['total_net'];
        }

        if ($affected_rows > 0) {
            logActivity('Facture supprimé de la facture interne [Facture Interne ID: ' . $facture_interne_id . ', Facture ID : ' . $facture_id . ', Total CRBT facture : ' . $total_crbt . ', Total Frais facture : ' . $total_frais . ']');

            return array('total_crbt' => $total_crbt, 'total_frais' => $total_frais, 'total_refuse' => $total_refuse, 'total_parrainage' => $total_parrainage, 'total_remise' => $total_remise, 'total_net' => $total_net);
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Update Total facture interne from database
     */
    public function update_total_facture_interne($facture_interne_id, $facture_id, $add = true)
    {
        //Get Facture
        $this->load->model('factures_model');
        $facture = $this->factures_model->get($facture_id);
        //Calculate Total CRBT & FRAIS Facture
        $total_crbt_facture = 0;
        $total_frais_facture = 0;
        $total_refuse_facture = 0;
        $total_parrainage_facture = 0;
        $total_remise_facture = 0;
        $total_net_facture = 0;
        if ($facture) {
            $total_crbt_facture = $facture->total_crbt;
            $total_frais_facture = $facture->total_frais;
            $total_refuse_facture = $facture->total_refuse;
            $total_parrainage_facture = $facture->total_parrainage;
            $total_remise_facture = $facture->total_remise;
            $total_net_facture = $facture->total_net;
        }
        //Add Total CRBT & Total Frais
        $this->db->where('id', $facture_interne_id);
        if ($add == true) {
            $this->db->set('total', 'total + ' . $total_crbt_facture, FALSE);
            $this->db->set('total_frais', 'total_frais + ' . $total_frais_facture, FALSE);
            $this->db->set('total_refuse', 'total_refuse + ' . $total_refuse_facture, FALSE);
            $this->db->set('total_parrainage', 'total_parrainage + ' . $total_parrainage_facture, FALSE);
            $this->db->set('total_remise', 'total_remise + ' . $total_remise_facture, FALSE);
            $this->db->set('total_net', 'total_net + ' . $total_net_facture, FALSE);
            $this->db->set('rest', 'total_received - total_net', FALSE);
        } else {
            $this->db->set('total', 'total - ' . $total_crbt_facture, FALSE);
            $this->db->set('total_frais', 'total_frais - ' . $total_frais_facture, FALSE);
            $this->db->set('total_refuse', 'total_refuse - ' . $total_refuse_facture, FALSE);
            $this->db->set('total_parrainage', 'total_parrainage - ' . $total_parrainage_facture, FALSE);
            $this->db->set('total_remise', 'total_remise - ' . $total_remise_facture, FALSE);
            $this->db->set('total_net', 'total_net - ' . $total_net_facture, FALSE);
            $this->db->set('rest', 'total_received - total_net', FALSE);
        }
        $this->db->update('tblfacturesinternes');

        return array('total_crbt' => $total_crbt_facture, 'total_frais' => $total_frais_facture, 'total_refuse' => $total_refuse_facture, 'total_parrainage' => $total_parrainage_facture, 'total_remise' => $total_remise_facture, 'total_net' => $total_net_facture);
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete facture facture interne from database, if used return array with key referenced
     */
    public function delete($id)
    {
	logActivity('Tenttive Facture Interne Supprimé [ID: ' . $id . ']');
        return false;    
        do_action('before_facture_facture_interne_deleted', $id);

        //Delete facture interne
        $this->db->where('id', $id);
        $this->db->delete('tblfacturesinternes');
        if ($this->db->affected_rows() > 0) {
            //Delete items facture interne
            $this->db->where('facture_interne_id', $id);
            $this->db->delete('tblfactureinterneitems');
            //Delete payments facture interne
            $this->db->where('factureinterneid', $id);
            $this->db->delete('tblfactureinternepaymentrecords');
            //Update numero facture interne to facture
            $this->db->where('num_factureinterne', $id);
            $this->db->update('tblfactures', array('status' => 1, 'num_factureinterne' => null));

            logActivity('Facture Interne Supprimé [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
}
