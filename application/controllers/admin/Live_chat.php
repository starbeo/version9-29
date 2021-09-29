<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Live_chat extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('live_chat_model');
        
        if(get_permission_module('chat') == 0) {
            redirect(admin_url('home'));
        }
    }

    public function index()
    {
        if (!has_permission('chat', '', 'view') && !has_permission('chat', '', 'view_own')) {
            access_denied('Chat');
        }

        $data['chat_assets'] = true;
        $data['title'] = 'Live Chat';
        $this->load->view('admin/live_chat/manage', $data);
    }

    public function get_list_contacts_data_ajax($search = '')
    {
        if (!has_permission('chat', '', 'view') && !has_permission('chat', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        //Concatenation Array Staff & Clients
        $staffid = get_staff_user_id();
        $data['contacts'] = array();
        $cpt = 0;
        //Get Staff
        $staffs = $this->live_chat_model->get_lists_staff($search, 1);
        $last_messages_staffs = $this->live_chat_model->get_last_messages_staff();
        foreach ($staffs as $key => $staff) {
            $data['contacts'][$cpt]['type'] = 'staff';
            $data['contacts'][$cpt]['contactid'] = $staff['staffid'];
            $data['contacts'][$cpt]['username'] = $staff['username'];
            $data['contacts'][$cpt]['fullname'] = $staff['firstname'] . ' ' . $staff['lastname'];
            $data['contacts'][$cpt]['online'] = $staff['online'];
            $data['contacts'][$cpt]['nbr_message_unread'] = 0;
            $data['contacts'][$cpt]['last_message'] = '';
            $data['contacts'][$cpt]['last_message_created_at'] = '';

            foreach ($last_messages_staffs as $key => $message) {
                if (($message['creator_id'] == $staff['staffid'] && $message['receiver_id'] == $staffid) || ($message['creator_id'] == $staffid && $message['receiver_id'] == $staff['staffid'])) {
                    $data['contacts'][$cpt]['nbr_message_unread'] = $message['nbr_message_unread'];
                    $data['contacts'][$cpt]['last_message'] = $message['last_message'];
                    $data['contacts'][$cpt]['last_message_created_at'] = $message['last_message_created_at'];
                }
            }

            $cpt++;
        }

        //Get Clients
        $clients = $this->live_chat_model->get_lists_clients($search, 1);
        $last_messages_clients = $this->live_chat_model->get_last_messages_client();
        foreach ($clients as $key => $client) {
            $data['contacts'][$cpt]['type'] = 'client';
            $data['contacts'][$cpt]['contactid'] = $client['id'];
            $data['contacts'][$cpt]['username'] = $client['username'];
            $data['contacts'][$cpt]['fullname'] = $client['nom'];
            $data['contacts'][$cpt]['online'] = $client['online'];
            $data['contacts'][$cpt]['nbr_message_unread'] = 0;
            $data['contacts'][$cpt]['last_message'] = '';
            $data['contacts'][$cpt]['last_message_created_at'] = '';

            foreach ($last_messages_clients as $key => $message) {
                if ($message['creator_id'] == $staffid && $message['receiver_id'] == $client['id']) {
                    $data['contacts'][$cpt]['nbr_message_unread'] = $message['nbr_message_unread'];
                    $data['contacts'][$cpt]['last_message'] = $message['last_message'];
                    $data['contacts'][$cpt]['last_message_created_at'] = $message['last_message_created_at'];
                }
            }

            $cpt++;
        }
        usort($data['contacts'], 'date_compare');

        $this->load->view('admin/live_chat/list_contacts_template', $data);
    }

    public function get_conversation_data_ajax($type = '', $receiverid = '')
    {
        if (!has_permission('chat', '', 'view') && !has_permission('chat', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        //Get Staffid
        $data['staffid'] = get_staff_user_id();
        //Get Conversation
        $conversation = $this->live_chat_model->get_conversation($type, $receiverid);
        if ($conversation) {
            $conversationid = $conversation->id;
        } else {
            $conversationid = $this->live_chat_model->add_conversation(array('type' => $type, 'receiver_id' => $receiverid));
        }
        //Update Message not read to read for this staff
        $this->live_chat_model->update_messages_to_read($conversationid, $receiverid, $type);
        //Get Total Messages
        $data['total_messages'] = total_rows('tblmessages', array('conversation_id' => $conversationid));
        //Get Messages
        $data['messages'] = array_reverse($this->live_chat_model->get_messages_conversation($conversationid));
        //Conversation id
        $data['conversationid'] = $conversationid;
        //Get Type conversation
        $data['type_conversation'] = $type;
        //Get Receiverid
        $data['receiverid'] = $receiverid;
        //Get Recipient
        if ($type == 'staff') {
            $this->load->model('staff_model');
            $data['recipient'] = $this->staff_model->get($receiverid);
        } else {
            $this->load->model('expediteurs_model');
            $data['recipient'] = $this->expediteurs_model->get($receiverid);
        }
        $this->load->view('admin/live_chat/conversation_template', $data);
    }

    public function get_sync_conversation_data_ajax($type = '', $receiverid = '')
    {
        if (!has_permission('chat', '', 'view') && !has_permission('chat', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        //Get Staffid
        $staffid = get_staff_user_id();
        //Get Conversation
        $conversation = $this->live_chat_model->get_conversation($type, $receiverid);
        if ($conversation) {
            $conversationid = $conversation->id;
        } else {
            $conversationid = $this->live_chat_model->add_conversation(array('type' => $type, 'receiver_id' => $receiverid));
        }
        //Get Total Messages
        $total_messages = total_rows('tblmessages', array('conversation_id' => $conversationid));
        //Get Messages
        $messages = array_reverse($this->live_chat_model->sync_messages_conversation($conversationid, $type));
        //Update Message not read to read for this staff
        $this->live_chat_model->update_messages_to_read($conversationid, $receiverid, $type);
        //Conversation id
        $conversationid = $conversationid;
        //Get Type conversation
        $type_conversation = $type;
        //Get Receiverid
        $receiverid = $receiverid;
        //Get Recipient
        if ($type == 'staff') {
            $this->load->model('staff_model');
            $recipient = $this->staff_model->get($receiverid);
        } else {
            $this->load->model('expediteurs_model');
            $recipient = $this->expediteurs_model->get($receiverid);
        }

        $success = false;
        $html = '';
        if (count($messages) > 0) {
            foreach ($messages as $key => $m) {
                if (isset($type_conversation) && isset($m)) {
                    //Generate Username
                    $username = '';
                    if ($type_conversation == 'staff') {
                        $username = '';
                    } else {
                        $username = '';
                    }
                    //Generate Image
                    $image = '';
                    if ($type_conversation == 'staff') {
                        $image = staff_profile_image($m['creator_id'], array('img-avatar', 'mright5'), 'thumb', array('alt' => $username));
                    } else {
                        $image = client_logo($m['creator_id'], array('img-avatar', 'mright5'), 'thumb', array('alt' => $username));
                    }
                    //Generate Content
                    $content = '';
                    if ($m['type'] == 'text') {
                        $content = $m['content'];
                    } else if ($m['type'] == 'image') {
                        $content = '';
                    } else if ($m['type'] == 'audio') {
                        $content = '';
                    } else if ($m['type'] == 'video') {
                        $content = '';
                    }
                    //Check if read or not read
                    $check_class = 'check_notread';
                    if ($m['_read'] == 1) {
                        $check_class = 'check_read';
                    }
                    //Generate Date
                    $date = date(get_current_date_format(), strtotime($m['created_at']));
                    $time = date('H:i', strtotime($m['created_at']));

                    $html .= '
                    <div id="message-' . $m['id'] . '" class="message-feed media">
                      <div class="pull-left">
                          ' . $image . '
                      </div>
                      <div class="media-body">
                          <div class="mf-content">
                            ' . $content . '     
                          </div>
                          <small class="mf-date">
                            <i class="fa fa-clock-o"></i> 
                            ' . $date . ' à ' . $time;
                    if ($m['creator_id'] == $staffid) {
                        $html .= '<i class="fa fa-check mleft5 ' . $check_class . '"></i>';
                        if ($check_class == 'check_read') {
                            $html .= '<i class="fa fa-check mleft-8 ' . $check_class . '"></i>';
                        }
                    }
                    $html .= '</small>
                      </div>
                    </div>';
                }
            }

            $success = true;
        }

        echo json_encode(array('success' => $success, 'total' => count($messages), 'bloc' => $html));
    }

    public function get_old_conversation_data_ajax($conversationid = '', $type, $receiverid = '', $page = '')
    {
        if (!has_permission('chat', '', 'view') && !has_permission('chat', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        //Page & Limit
        $data['limit2'] = 10;
        if (is_numeric($page)) {
            $data['page'] = $page + 1;
            $data['limit1'] = $page * $data['limit2'];
        }
        //Get Total Messages
        $data['total_messages'] = total_rows('tblmessages', array('conversation_id' => $conversationid));
        if ($data['total_messages'] > ($data['limit1'] + $data['limit2'])) {
            $data['show_bloc_more_messages'] = true;
        }
        //Get Messages
        $data['messages'] = array_reverse($this->live_chat_model->get_old_messages_conversation($conversationid, $data['limit1'], $data['limit2']));
        //$data['query'] = $this->db->last_query();
        //Get Staffid
        $data['staffid'] = get_staff_user_id();
        //Conversation id
        $data['conversationid'] = $conversationid;
        //Get Type conversation
        $data['type_conversation'] = $type;
        //Get Receiverid
        $data['receiverid'] = $receiverid;

        $this->load->view('admin/live_chat/old_messages_template', $data);
    }

    public function add_message($type = '', $receiverid = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $this->live_chat_model->add_message($data);
            $success = false;
            if (is_numeric($id)) {
                $success = true;
                $bloc = $this->live_chat_model->generate_bloc_message($data['conversationid'], $id);

                echo json_encode(array('success' => $success, 'bloc' => $bloc));
            }
        }
    }

    public function delete_message()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['for_me'])) {
                $success = $this->live_chat_model->update_display_messages($data);
            } else if (isset($data['for_all'])) {
                $success = $this->live_chat_model->delete_messages($data);
            }

            echo json_encode(array('success' => $success));
        }
    }
}
