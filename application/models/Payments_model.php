<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('factures_internes_model');
    }

    /**
     * Get payment by ID
     * @param  mixed $id payment id
     * @return object
     */
    public function get($id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->select('*,tblfactureinternepaymentrecords.id as paymentid');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblfactureinternepaymentrecords.paymentmode', 'left');
        $this->db->order_by('tblfactureinternepaymentrecords.id', 'asc');
        $this->db->where('tblfactureinternepaymentrecords.id', $id);
        $this->db->where('tblfactureinternepaymentrecords.id_entreprise', $id_E);
        $payment = $this->db->get('tblfactureinternepaymentrecords')->row();

        if (!$payment) {
            return false;
        }

        return $payment;
    }

    /**
     * Get all facture interne payments
     * @param  mixed $factureinterneid
     * @return array
     */
    public function get_facture_interne_payments($factureinterneid)
    {
        $this->db->select('*,tblfactureinternepaymentrecords.id as paymentid');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblfactureinternepaymentrecords.paymentmode', 'left');
        $this->db->order_by('tblfactureinternepaymentrecords.id', 'asc');
        $this->db->where('factureinterneid', $factureinterneid);

        return $this->db->get('tblfactureinternepaymentrecords')->result_array();
    }

    /**
     * Update payment
     * @param  array $data payment data
     * @param  mixed $id   paymentid
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['date'] = to_sql_date($data['date']);
        $data['note'] = nl2br($data['note']);
        $_data = do_action('before_payment_updated', array(
            'data' => $data,
            'id' => $id
        ));

        $data = $_data['data'];

        $this->db->where('id', $id);
        $this->db->update('tblfactureinternepaymentrecords', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('payment_updated') . ' [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Process facture interne payment
     * @param  array $data $_POST data
     * @return boolean
     */
    public function process_payment($data, $factureinterneid = '')
    {
        // Offline payment mode from the admin side
        if (is_numeric($data['paymentmode'])) {
            if (is_staff_logged_in()) {
                return $this->add($data);
            }
        }

        return false;
    }

    /**
     * Record new payment
     * @param array $data payment data
     * @return boolean
     */
    public function add($data)
    {
        // Check if field do not redirect to payment processor is set so we can unset from the database
        if (isset($data['do_not_redirect'])) {
            unset($data['do_not_redirect']);
        }

        if (is_staff_logged_in()) {
            //add payment
            $data['addedfrom'] = get_staff_user_id();
            if (isset($data['date'])) {
                $data['date'] = to_sql_date($data['date']);
            } else {
                $data['date'] = date('Y-m-d H:i:s');
            }
            if (isset($data['note'])) {
                $data['note'] = nl2br($data['note']);
            }
        } else {
            $data['date'] = date('Y-m-d H:i:s');
        }

        $data['daterecorded'] = date('Y-m-d H:i:s');

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $data = do_action('before_payment_recorded', $data);
        $this->db->insert('tblfactureinternepaymentrecords', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            //Update facture interne total received
            $this->db->where('id', $data['factureinterneid']);
            $this->db->where('id_entreprise', $id_E);
            $this->db->set('total_received', 'total_received+' . $data['amount'], FALSE);
            $this->db->update('tblfacturesinternes');
            //Get facture interne
            $factureinterne = $this->factures_internes_model->get($data['factureinterneid']);
            $total_received = $factureinterne->total_received;
            $total_crbt = $factureinterne->total;
            $total_frais = $factureinterne->total_frais;
            $rest = $total_received - ($total_crbt - $total_frais);
            if ($rest == 0) {
                $motif = NULL;
            }
            //Update facture interne rest
            $this->db->where('id', $data['factureinterneid']);
            $this->db->where('id_entreprise', $id_E);
            $this->db->set('rest', $rest);
            if (is_null($motif)) {
                $this->db->set('motif', $motif);
            }
            $this->db->update('tblfacturesinternes');

            logActivity(_l('payment_recorded') . ' [ID:' . $insert_id . ', ' . _l('facture_interne_name') . ' : ' . $factureinterne->nom . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Delete payment from database
     * @param  mixed $id paymentid
     * @return boolean
     */
    public function delete($id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        //Get Payment
        $payment = $this->get($id);
        $factureinterneid = $payment->factureinterneid;
        //Get Facture Interne
        $factureinterne = $this->factures_internes_model->get($factureinterneid);

        do_action('before_payment_deleted', array(
            'paymentid' => $id,
            'factureinterneid' => $factureinterneid
        ));
        $this->db->where('id', $id);
        $this->db->delete('tblfactureinternepaymentrecords');

        if ($this->db->affected_rows() > 0) {
            if ($payment) {
                //Update facture interne total received
                $this->db->where('id', $factureinterneid);
                $this->db->set('total_received', $factureinterne->total_received . '-' . $payment->amount, FALSE);
                $this->db->update('tblfacturesinternes');

                if ($this->db->affected_rows() > 0) {
                    //Get Facture Interne
                    $factureinterne = $this->factures_internes_model->get($factureinterneid);
                    // Update rest & motif facture interne
                    $total_received = $factureinterne->total_received;
                    $total = $factureinterne->total;
                    $rest = $total_received - $total;
                    if ($rest == 0) {
                        $motif = NULL;
                    } else if ($rest < 0) {
                        $rest = $rest * -1;
                    }
                    //Update facture interne rest
                    $this->db->where('id', $factureinterneid);
                    $this->db->where('id_entreprise', $id_E);
                    $this->db->set('rest', $rest);
                    if (is_null($motif)) {
                        $this->db->set('motif', $motif);
                    }
                    $this->db->update('tblfacturesinternes');
                }
            }

            logActivity(_l('payment_deleted') . ' [ID:' . $id . ', ' . _l('facture_interne_name') . ' : ' . $factureinterne->nom . ']');

            return true;
        }

        return false;
    }
}
