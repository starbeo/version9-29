<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supports_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get support by id
     * @param  mixed $id support id
     * @return object
     */
    public function get($id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $is_admin = is_admin();
        $this->db->where('id', $id);
        if (!$is_admin) {
            $this->db->where('(id IN (SELECT supportid FROM tblsupportstaffassignees WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ')');
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblsupports')->row();
        }

        $this->db->where('tblsupports.id_entreprise', $id_E);

        return $this->db->get('tblsupports')->result_array();
    }

    /**
     * Get Priorities
     * @return object
     */
    public function get_priorities($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->where('tblsupportpriorities.id_entreprise', $id_E);

        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblsupportpriorities')->row();
        }

        return $this->db->get('tblsupportpriorities')->result_array();
    }

    /**
     * Add new support
     * @param array $data support $_POST data
     * @return mixed
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $data['startdate'] = to_sql_date($data['startdate']);
        if (!empty($data['duedate'])) {
            $data['duedate'] = to_sql_date($data['duedate']);
        }
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();
        $data['description'] = nl2br($data['description']);
        $data['id_entreprise'] = $id_E;

        if (date('Y-m-d') >= $data['startdate']) {
            $data['status'] = 4;
        } else {
            $data['status'] = 1;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $this->db->insert('tblsupports', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_support_added') . ' [ID:' . $insert_id . ', ' . _l('name') . ' : ' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update support data
     * @param  array $data support data $_POST
     * @param  mixed $id   support id
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;
        if (!empty($data['startdate'])) {
            $data['startdate'] = to_sql_date($data['startdate']);
        } else {
            unset($data['startdate']);
        }
        if (!empty($data['duedate'])) {
            $data['duedate'] = to_sql_date($data['duedate']);
        } else {
            unset($data['duedate']);
        }
        $data['description'] = nl2br($data['description']);

        $this->db->where('id', $id);
        $this->db->update('tblsupports', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            logActivity(_l('support_updated') . ' [ID:' . $id . ', ' . _l('name') . ' : ' . $data['name'] . ']');
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Delete support
     * @param  mixed $id supportid
     * @return boolean
     */
    public function delete_support($id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->where('id', $id);
        $this->db->delete('tblsupports');

        if ($this->db->affected_rows() > 0) {
            $this->db->where('supportid', $id);
            $this->db->delete('tblsupportstaffassignees');

            $this->db->where('supportid', $id);
            $this->db->delete('tblsupportcomments');

            if (is_dir(SUPPORTS_ATTACHMENTS_FOLDER . $id_E . '/' . $id)) {
                delete_dir(SUPPORTS_ATTACHMENTS_FOLDER . $id_E . '/' . $id);
            }

            return true;
        }

        return false;
    }

    /**
     * Mark support as complete
     * @param  mixed $id support id
     * @return boolean
     */
    public function mark_complete($id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->where('id', $id);
        $this->db->where('id_entreprise', $id_E);
        $this->db->update('tblsupports', array(
            'datefinished' => date('Y-m-d H:i:s'),
            'finished' => 1
        ));

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Unmark support as complete
     * @param  mixed $id support id
     * @return boolean
     */
    public function unmark_complete($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblsupports', array(
            'datefinished' => NULL,
            'finished' => 0
        ));

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get support comment
     * @param  mixed $id support id
     * @return array
     */
    public function get_support_comments($id)
    {
        $this->db->select('id,dateadded,content,tblstaff.firstname,tblstaff.lastname,tblsupportcomments.staffid as commentator');
        $this->db->from('tblsupportcomments');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblsupportcomments.staffid', 'left');
        $this->db->where('supportid', $id);
        $this->db->order_by('dateadded', 'asc');

        return $this->db->get()->result_array();
    }

    /**
     * Get all support assigneed
     * @param  mixed $id support id
     * @return array
     */
    public function get_support_assignees($id)
    {
        $this->db->select('id,tblsupportstaffassignees.staffid as assigneeid');
        $this->db->from('tblsupportstaffassignees');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblsupportstaffassignees.staffid', 'left');
        $this->db->where('supportid', $id);

        return $this->db->get()->result_array();
    }

    /**
     * Get all support attachments
     * @param  mixed $supportid supportid
     * @return array
     */
    public function get_support_attachments($supportid)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->where('supportid', $supportid);
        $this->db->where('id_entreprise', $id_E);
        $this->db->order_by('dateadded', 'desc');
        $attachments = $this->db->get('tblsupportsattachments')->result_array();

        $i = 0;
        foreach ($attachments as $attachment) {
            $attachments[$i]['mimeclass'] = get_mime_class($attachment['filetype']);
            $i++;
        }

        return $attachments;
    }

    /**
     * Remove support attachment from server and database
     * @param  mixed $id attachmentid
     * @return boolean
     */
    public function remove_support_attachment($id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        // Get the attachment
        $this->db->where('id', $id);
        $attachment = $this->db->get('tblsupportsattachments')->row();
        if (is_file(SUPPORTS_ATTACHMENTS_FOLDER . $id_E . '/' . $attachment->supportid . '/' . $attachment->file_name)) {
            $deleted = unlink(SUPPORTS_ATTACHMENTS_FOLDER . $id_E . '/' . $attachment->supportid . '/' . $attachment->file_name);
            if ($deleted) {
                $this->db->where('id', $id);
                $this->db->delete('tblsupportsattachments');
                if ($this->db->affected_rows() > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public function get_checklist_item($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblsupportchecklists')->row();
    }

    public function get_checklist_items($supportid)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->where('supportid', $supportid);
        $this->db->where('id_entreprise', $id_E);
        $this->db->order_by('list_order', 'asc');
        return $this->db->get('tblsupportchecklists')->result_array();
    }

    public function update_checklist_order($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        foreach ($data['order'] as $order) {
            $this->db->where('id', $order[0]);
            $this->db->where('id_entreprise', $id_E);

            $this->db->update('tblsupportchecklists', array('list_order' => $order[1]));
        }
    }

    /**
     * Add uploaded attachments to database
     * @param mixed $supportid     support id
     * @param array $attachment attachment data
     */
    public function add_attachment_to_database($supportid, $attachment)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->insert('tblsupportsattachments', array(
            'file_name' => $attachment[0]['filename'],
            'dateadded' => date('Y-m-d H:i:s'),
            'supportid' => $supportid,
            'filetype' => $attachment[0]['filetype'],
            'id_entreprise' => $id_E
        ));

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add support new blank check list item
     * @param mixed $data $_POST data with taxid
     */
    public function add_checklist_item($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->insert('tblsupportchecklists', array(
            'supportid' => $data['supportid'],
            'description' => '',
            'dateadded' => date('Y-m-d H:i:s'),
            'addedfrom' => get_staff_user_id(),
            'id_entreprise' => $id_E
        ));
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * Add new support comment
     * @param array $data comment $_POST data
     * @return boolean
     */
    public function add_support_comment($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $data['staffid'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['content'] = nl2br($data['content']);
        $data['id_entreprise'] = $id_E;
        $this->db->insert('tblsupportcomments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * Remove support comment from database
     * @param  mixed $id support id
     * @return boolean
     */
    public function remove_comment($id)
    {
        // Check if user really creator
        $this->db->where('id', $id);
        $comment = $this->db->get('tblsupportcomments')->row();
        if ($comment->staffid == get_staff_user_id()) {
            $this->db->where('id', $id);
            $this->db->delete('tblsupportcomments');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }

    public function delete_checklist_item($id)
    {

        $this->db->where('id', $id);
        $this->db->delete('tblsupportchecklists');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Update checklist item
     * @param  mixed $id          check list id
     * @param  mixed $description checklist description
     * @return void
     */
    public function update_checklist_item($id, $description)
    {
        $this->db->where('id', $id);
        $this->db->update('tblsupportchecklists', array('description' => nl2br($description)));
    }

    /**
     * Get support creator id
     * @param  mixed $supportid support id
     * @return mixed
     */
    public function get_support_creator_id($taskid)
    {
        return $this->get($supportid)->addedfrom;
    }

    /**
     * Assign support to staff
     * @param array $data support assignee $_POST data
     * @return boolean
     */
    public function add_support_assignees($data)
    {
        $this->db->insert('tblsupportstaffassignees', array(
            'supportid' => $data['supportid'],
            'staffid' => $data['assignee']
        ));

        if ($this->db->affected_rows() > 0) {
            //Add Notification
            if (is_numeric($data['supportid']) && is_numeric($data['assignee'])) {
                $support = $this->get($data['supportid']);
                if ($support) {
                    $_data['description'] = "Nouveau Support vous a été affecter [Titre: " . $support->name . "]";
                    $_data['touserid'] = $data['assignee'];
                    $_data['link'] = admin_url('supports/support/' . $support->id);
                    add_notification($_data);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Remove support assignee from database
     * @param  mixed $id     assignee id
     * @param  mixed $supportid support id
     * @return boolean
     */
    public function remove_assignee($id, $supportid)
    {
        $support = $this->get($supportid);
        if ($support->addedfrom != get_staff_user_id()) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete('tblsupportstaffassignees');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
}
