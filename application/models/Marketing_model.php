<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get marketing
     * @return mixed
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblmarketing')->row();
        }

        return $this->db->get('tblmarketing')->result_array();
    }

    /**
     * Get types marketing
     * @return array
     */
    public function get_types()
    {
        $types = array(
            array('id' => 1, 'name' => _l('all_the_clients')),
            array('id' => 2, 'name' => _l('by_client')),
            array('id' => 3, 'name' => _l('by_group'))
        );

        return $types;
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Add new marketing
     */
    public function add($data)
    {
        $data['id_entreprise'] = get_entreprise_id();
        $data['addedfrom'] = get_staff_user_id();
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $addImage = false;
        if ($data['notification_by'] == 'email') {
            $data['sms'] = NULL;
            if ($data['notification_by_email'] == 'text') {
                $data['email'] = nl2br($data['email']);
                $data['image'] = NULL;
            } else {
                $data['email'] = NULL;
                $addImage = true;
            }
        } else {
            $data['subject'] = NULL;
            $data['email'] = NULL;
            $data['image'] = NULL;
        }

        $this->db->insert('tblmarketing', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Ajout de l'image
            if ($addImage) {
                handle_image_marketing_upload($insert_id);
            }

            logActivity('Nouveau Marketing Crée [ID:' . $insert_id . ', Name:' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Update marketing
     */
    public function update($data, $id)
    {
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $addImage = false;
        if ($data['notification_by'] == 'email') {
            $data['sms'] = NULL;
            if ($data['notification_by_email'] == 'text') {
                $data['email'] = nl2br($data['email']);
                $data['image'] = NULL;
            } else {
                $data['email'] = NULL;
                $addImage = true;
            }
        } else {
            $data['subject'] = NULL;
            $data['email'] = NULL;
            $data['image'] = NULL;
        }

        $this->db->where('id', $id);
        $this->db->update('tblmarketing', $data);

        $affedtedRows = 0;
        if ($this->db->affected_rows() > 0) {
            $affedtedRows++;
        }

        //Ajout de l'image
        if (handle_image_marketing_upload($id)) {
            $affedtedRows++;
        }

        if ($this->db->affected_rows() > 0) {
            logActivity('Marketing Modifié [ID:' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Add new marketing
     */
    public function add_historique_marketing($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $this->db->insert('tblmarketinghistoriques', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Envoi au destinatire Crée [ID:' . $insert_id . ', Destinataire ID:' . $data['relation_id'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Update marketing
     */
    public function update_historique_marketing($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblmarketinghistoriques', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Envoi au destinatire Modifié [ID:' . $id . ', Destinataire ID:' . $data['relation_id'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param integer $_GET id
     * @return boolean
     * Lanching marketing
     */
    public function start($id)
    {
        $marketing = $this->get($id);
        $lists = array();
        if ($marketing) {
            $this->load->model('expediteurs_model');
            if ($marketing->type == 1 || $marketing->type == 2) {
                $type = $marketing->type;
                if ($type == 1) {
                    // Get clients
                    $lists = $this->expediteurs_model->get();
                } else {
                    // Get client by id
                    if (is_numeric($marketing->rel_id)) {
                        $lists = $this->expediteurs_model->get('', 1, 'id = ' . $marketing->rel_id);
                    }
                }
            } else if ($marketing->type == 3) {
                $this->load->model('groupes_model');
                // Get groupe client by id
                if (is_numeric($marketing->rel_id)) {
                    $groupe = $this->groupes_model->get($marketing->rel_id);
                    if ($groupe) {
                        // Get client by groupe
                        $lists = $this->expediteurs_model->get('', 1, 'groupe_id = ' . $groupe->id);
                    }
                }
            }

            $this->load->model('emails_model');
            foreach ($lists as $item) {
                $idClient = $item['id'];
                $sent = 0;
                //Vérification dans le lancement du E-marketing si le client a déjà reçu le mail ou bien le SMS pour éviter le plantage et pour avoir la possibilité de relancer le marketing
                if ($marketing->sent == 0 || total_rows('tblmarketinghistoriques', array('marketing_id' => $marketing->id, 'relation_id' => $idClient, 'send_type' => $marketing->notification_by, 'sent' => 0)) > 0) {
                    if ($marketing->notification_by == 'email') {
                        $emailClient = $item['email'];
                        if (!empty($emailClient) && !empty($marketing->subject) && (!empty($marketing->email) || !is_null($marketing->image))) {
                            $emailMarketing = '';
                            if ($marketing->notification_by_email == 'text') {
                                $emailMarketing = $marketing->email;
                            } else if ($marketing->notification_by_email == 'image') {
                                $emailMarketing = '<html>
                                                        <body>
                                                            <div style="max-width: 800px; margin: 0 auto;">
                                                                <img src="' . base_url('', 'http') . 'uploads/marketing/' . $marketing->id . '/' . $marketing->image . '" style="width: 100%;" />
                                                            </div>
                                                        </body>
                                                    </html>';
                            }
//                            $this->emails_model->add_attachment(array(
//                                'attachment' => base_url() . 'assets/images/aid-adha-31-07-2020.jpeg',
//                                'filename' => 'Aid Adha 31-07-2020.jpeg'
//                            ));
                            $send = $this->emails_model->send_simple_email($emailClient, $marketing->subject, $emailMarketing);
                            if ($send) {
                                $sent = 1;
                            }
                        }
                    } else if ($marketing->notification_by == 'sms') {
                        $telephoneClient = $item['telephone'];
                        if (!empty($telephoneClient) && !empty($marketing->sms)) {
                            // Send sms to client
                            $send = send_sms_to_recipient($telephoneClient, $marketing->sms, true);
                            if ($send) {
                                $sent = 1;
                            }
                        }
                    }
                    if (total_rows('tblmarketinghistoriques', array('marketing_id' => $marketing->id, 'relation_id' => $idClient, 'send_type' => $marketing->notification_by, 'sent' => 0)) > 0) {
                        // Get number historique marketing
                        $this->db->where('marketing_id', $marketing->id);
                        $this->db->where('relation_id', $idClient);
                        $historiqueMarketing = $this->db->get('tblmarketinghistoriques')->row();
                        if ($historiqueMarketing) {
                            // Update historique marketing
                            $dataUpdateLine['sent'] = $sent;
                            $this->update_historique_marketing($historiqueMarketing->id, $dataUpdateLine);
                        }
                    } else {
                        // Add historique marketing
                        $dataAddLine['marketing_id'] = $marketing->id;
                        $dataAddLine['relation_id'] = $idClient;
                        $dataAddLine['send_type'] = $marketing->notification_by;
                        $dataAddLine['sent'] = $sent;
                        $this->add_historique_marketing($dataAddLine);
                    }
                }
            }
            // Update marketing
            $dataUpdateMarketing['sent'] = 1;
            $dataUpdateMarketing['staff_who_executed'] = get_staff_user_id();
            $dataUpdateMarketing['sending_date'] = date('Y-m-d H:i:s');
            $this->db->where('id', $marketing->id);
            $this->db->update('tblmarketing', $dataUpdateMarketing);

            return true;
        }

        return false;
    }

    /**
     * @param integer $_GET id
     * @return boolean
     * Delete marketing
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblmarketing');
        if ($this->db->affected_rows() > 0) {
            //Suppression de l'historique
            $this->db->where('marketing_id', $id);
            $this->db->delete('tblmarketinghistoriques');
            //Suppression de l'image
            if (file_exists(MARKETING_ATTACHED_PIECE_FOLDER . $id)) {
                delete_dir(MARKETING_ATTACHED_PIECE_FOLDER . $id);
            }
            
            logActivity('Marketing Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}
