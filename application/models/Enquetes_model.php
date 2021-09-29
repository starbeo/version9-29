<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enquetes_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $active = 1)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $this->db->where('id_entreprise', $id_E);

        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblenquetes')->row();
        }

        $this->db->where('active', $active);
        return $this->db->get('tblenquetes')->result_array();
    }

    public function get_questions($id = '', $ordre = '')
    {
        $this->db->where('enquete_id', $id);
        if (is_numeric($ordre)) {
            $this->db->where('ordre', $ordre);
        }
        return $this->db->get('tblenquetequestions')->result_array();
    }

    public function get_reponses_question($id = '')
    {
        $this->db->where('question_id', $id);
        return $this->db->get('tblenquetereponses')->result_array();
    }

    public function add_reponse_client($data = array())
    {
        $insert['client_id'] = get_expediteur_user_id();
        $insert['device_type'] = 'web';
        $insert['question_id'] = $data['question_id'];
        $insert['reponse_id'] = $data['reponse'];
        $this->db->insert('tblenquetereponsesclient', $insert);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;
    }
}
