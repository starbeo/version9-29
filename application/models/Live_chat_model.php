<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Live_chat_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
        //Get ID Entreprise
        $this->id_E = $this->session->userdata('staff_user_id_entreprise');
        //Get ID Staff
        $this->staffid = '';
        if (!is_expediteur_logged_in()) {
            $this->staffid = get_staff_user_id();
        } else {
            $this->staffid = 1;
        }
    }

    /**
     * Get List Staff
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get_lists_staff($search = '', $active = '')
    {
        $this->db->where('tblstaff.id_entreprise', $this->id_E);

        if (is_numeric($this->staffid)) {
            $this->db->where('tblstaff.staffid !=', $this->staffid);
        }

        if (is_int($active)) {
            $this->db->where('tblstaff.active', $active);
        }

        if (!empty($search)) {
            $this->db->like('tblstaff.firstname', $search);
            $this->db->or_like('tblstaff.lastname', $search);
            $this->db->or_like('tblstaff.username', $search);
        }

        return $this->db->get('tblstaff')->result_array();
    }

    /**
     * Get List Staff
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get_last_messages_staff()
    {
        $query = $this->db->query('SELECT COUNT(m.id) as nbr_message_unread, c.creator_id, c.receiver_id, m.content as last_message, m.created_at as last_message_created_at FROM tblmessages m, tblconversations c WHERE c.id = m.conversation_id AND m.creator_type = "staff" AND m._read = 0 AND m._display != 0  GROUP BY c.creator_id, c.receiver_id ORDER BY m.created_at DESC');

        return $query->result_array();
    }

    /**
     * Get List Clients
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get_lists_clients($search = '', $active = '')
    {
        $this->db->where('tblexpediteurs.id_entreprise', $this->id_E);

        if (is_int($active)) {
            $this->db->where('tblexpediteurs.active', $active);
        }

        if (!empty($search)) {
            $this->db->like('tblexpediteurs.nom', $search);
        }

        return $this->db->get('tblexpediteurs')->result_array();
    }

    /**
     * Get List Staff
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get_last_messages_client()
    {
        $id = '';
        if (is_expediteur_logged_in()) {
            $id = get_expediteur_user_id();
        } else {
            $id = get_staff_user_id();
        }

        $query = $this->db->query('SELECT COUNT(m.id) as nbr_message_unread, c.creator_id, c.receiver_id, m.content as last_message, m.created_at as last_message_created_at FROM tblmessages m, tblconversations c WHERE c.id = m.conversation_id AND m.creator_id != ' . $id . ' AND m._read = 0 AND m._display != 0 GROUP BY c.creator_id, c.receiver_id ORDER BY m.created_at DESC');

        return $query->result_array();
    }

    /**
     * Get Message
     * @param  integer $messageid
     * @return mixed
     */
    public function get_message($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblmessages')->row();
        }
    }

    /**
     * Get Conversation
     * @param  integer $conversationid
     * @return mixed
     */
    public function get_conversation_by_id($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblconversations')->row();
        }
    }

    /**
     * Add Conversation
     * @param  array $data
     * @return mixed
     */
    public function add_conversation($data)
    {
        $data['creator_id'] = get_staff_user_id();
        if (is_expediteur_logged_in()) {
            $data['creator_id'] = $data['receiver_id'];
            $data['receiver_id'] = get_expediteur_user_id();
        }

        $this->db->insert('tblconversations', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Get Conversation
     * @param  integer $receiverid
     * @return mixed
     */
    public function get_conversation($type = '', $receiverid = '')
    {
        $id = get_staff_user_id();
        if (is_expediteur_logged_in()) {
            $id = get_expediteur_user_id();
        }

        //Get conversation id
        if (is_numeric($receiverid) && is_numeric($id)) {
            $this->db->where('(type = "' . $type . '" AND creator_id = ' . $id . ' AND receiver_id = ' . $receiverid . ')');
            $this->db->or_where('(type = "' . $type . '" AND creator_id = ' . $receiverid . ' AND receiver_id = ' . $id . ')');
            return $this->db->get('tblconversations')->row();
        }
    }

    /**
     * Get Messages Conversation
     * @param  integer $conversationid
     * @return mixed
     */
    public function get_messages_conversation($conversationid = '')
    {
        $messages = array();
        if (is_numeric($conversationid)) {
            $this->db->where('conversation_id', $conversationid);
            $this->db->order_by('created_at', 'desc');
            $this->db->limit(10);
            $messages = $this->db->get('tblmessages')->result_array();
        }

        return $messages;
    }

    /**
     * Sync Messages Conversation
     * @param  integer $conversationid
     * @return mixed
     */
    public function sync_messages_conversation($conversationid = '', $type = '')
    {
        $messages = array();
        if (is_numeric($conversationid)) {
            $this->db->where('conversation_id', $conversationid);
            $this->db->where('creator_type', $type);
            $this->db->where('_read', 0);
            $this->db->order_by('created_at', 'desc');
            $messages = $this->db->get('tblmessages')->result_array();
        }

        return $messages;
    }

    /**
     * Get Old Messages Conversation
     * @param  integer $conversationid
     * @return mixed
     */
    public function get_old_messages_conversation($conversationid = '', $limit1 = '', $limit2 = '')
    {
        $messages = array();
        if (is_numeric($conversationid)) {
            $this->db->where('conversation_id', $conversationid);
            $this->db->order_by('created_at', 'desc');
            if (is_numeric($limit1) && is_numeric($limit2)) {
                $this->db->limit($limit2, $limit1);
            }
            $messages = $this->db->get('tblmessages')->result_array();
        }

        return $messages;
    }

    /**
     * Update Messages Read
     * @param  integer $conversationid & integer $creator_id
     * @return mixed
     */
    public function update_messages_to_read($conversationid = '', $creator_id, $type)
    {
        $this->db->where('creator_type', $type);
        $this->db->where('creator_id', $creator_id);
        $this->db->where('conversation_id', $conversationid);
        $this->db->update('tblmessages', array('_read' => 1));
    }

    /**
     * Add Message
     * @param  array $data
     * @return id
     */
    public function add_message($_data = '')
    {
        $creator_type = 'staff';
        $creator_id = get_staff_user_id();
        if (is_expediteur_logged_in()) {
            $creator_type = 'client';
            $creator_id = get_expediteur_user_id();
        }

        $data['conversation_id'] = $_data['conversationid'];
        $data['creator_id'] = $creator_id;
        $data['creator_type'] = $creator_type;
        $data['type'] = $_data['type'];
        $data['_read'] = 0;

        if ($data['type'] == 'text') {
            $data['content'] = $_data['message'];
        } else {
            $data['content'] = $_data['message'];
        }

        $this->db->insert('tblmessages', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Generate Bloc Message
     * @param  array $data
     * @return id
     */
    public function generate_bloc_message($conversationid = '', $messageid = '')
    {
        $bloc = '';
        if (is_numeric($conversationid) && is_numeric($messageid)) {
            //Get Staff id or Client id
            $id = get_staff_user_id();
            if (is_expediteur_logged_in()) {
                $id = get_expediteur_user_id();
            }
            //Get Conversation
            $conversation = $this->get_conversation_by_id($conversationid);
            //Get Message
            $message = $this->get_message($messageid);
            //Generate Bloc Message
            if (isset($conversation) && isset($message)) {
                //Generate Username
                $username = '';
                if ($conversation->type == 'staff') {
                    $username = '';
                } else {
                    $username = '';
                }
                //Generate Image
                $image = '';
                if ($conversation->type == 'staff') {
                    $image = staff_profile_image($message->creator_id, array('img-avatar', 'mright5'), 'small', array('alt' => $username));
                } else {
                    $image = client_logo($message->creator_id, array('img-avatar', 'mright5'), 'small', array('alt' => $username));
                }
                //Generate Content
                $content = '';
                if ($message->type == 'text') {
                    $content = $message->content;
                } else if ($message->type == 'image') {
                    $content = '';
                } else if ($message->type == 'audio') {
                    $content = '';
                } else if ($message->type == 'video') {
                    $content = '';
                }
                //Check if read or not read
                $check_class = 'check_notread';
                if ($message->_read == 1) {
                    $check_class = 'check_read';
                }
                //Generate Date
                $date = date(get_current_date_format(), strtotime($message->created_at));
                $time = date('H:i', strtotime($message->created_at));
                $bloc .= '
                            <div class="message-feed right">
                              <div class="pull-right">
                                ' . $image . '
                              </div>
                              <div class="media-body">
                                  <div class="mf-content">
                                    ' . $content . '   
                                  </div>
                                  <small class="mf-date">
                                    <i class="fa fa-clock-o"></i> 
                                    ' . $date . ' à ' . $time;
                if ($message->creator_id == $id) {
                    $bloc .= '
                                    <i class="fa fa-check mleft5 ' . $check_class . '"></i>';
                    if ($check_class == 'check_read') {
                        $bloc .= '
                                    <i class="fa fa-check mleft-8 ' . $check_class . '"></i>';
                    }
                }
                $bloc .= '      </small>
                              </div>
                            </div>';
            }
        }

        return $bloc;
    }

    /**
     * @param  array data
     * @return boolean
     * Update display messages
     */
    public function update_display_messages($data)
    {
        $affectedRows = 0;

        //Get Staff id or Client id
        $id = get_staff_user_id();
        if (is_expediteur_logged_in()) {
            $id = get_expediteur_user_id();
        }

        if (is_numeric($data['conversationid'])) {
            $messages = $data['messages'];
            foreach ($messages as $msg_id) {
                //Get Message
                $message = $this->get_message($msg_id);
                if ($message) {
                    if ($message->creator_id == $id) {
                        $this->db->set('deleted_at', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s'))));
                    } else {
                        $this->db->set('_display', 0);
                    }
                    $this->db->where('id', $msg_id);
                    $this->db->where('conversation_id', $data['conversationid']);
                    $this->db->update('tblmessages');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  array data
     * @return boolean
     * Delete messages
     */
    public function delete_messages($data)
    {
        $affectedRows = 0;

        //Get Staff id or Client id
        $id = get_staff_user_id();
        if (is_expediteur_logged_in()) {
            $id = get_expediteur_user_id();
        }

        if (is_numeric($data['conversationid'])) {
            $messages = $data['messages'];
            foreach ($messages as $msg_id) {
                //Get Message
                $message = $this->get_message($msg_id);
                if ($message) {
                    if ($message->creator_id == $id || ($message->creator_id !== $id && $message->_read == 0)) {
                        $this->db->set('deleted_at', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s'))));
                    }
                    $this->db->set('_display', 0);
                    $this->db->where('id', $msg_id);
                    $this->db->where('conversation_id', $data['conversationid']);
                    $this->db->update('tblmessages');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }
}
