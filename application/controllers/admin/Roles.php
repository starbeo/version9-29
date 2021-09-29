<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        
        if(get_permission_module('roles') == 0) {
            redirect(admin_url('home'));
        }
    }
    /* List all staff roles */

    public function index()
    {
        if (!is_admin()) {
            access_denied('roles');
        }
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $this->vtech_base->get_table_data($id_E, 'roles');
        }
        $data['title'] = _l('all_roles');
        $this->load->view('admin/roles/manage', $data);
    }
    /* Add new role or edit existing one */

    public function role($id = '')
    {
        if (!is_admin()) {
            access_denied('roles');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!is_admin()) {
                    access_denied('roles');
                }
                $id = $this->roles_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('role')));
                    redirect(admin_url('roles/role/' . $id));
                }
            } else {
                if (!is_admin()) {
                    access_denied('roles');
                }
                $success = $this->roles_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('role')));
                }
                redirect(admin_url('roles/role/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('role_lowercase'));
        } else {
            $data['role_permissions'] = $this->roles_model->get_role_permissions($id);
            $role = $this->roles_model->get($id);
            $data['role'] = $role;
            $title = _l('edit', _l('role_lowercase')) . ' ' . $role->name;
        }
        $data['permissions'] = $this->roles_model->get_permissions();
        $data['title'] = $title;
        $this->load->view('admin/roles/role', $data);
    }
    /* Delete staff role from database */

    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('roles');
        }
        if (!$id) {
            redirect(admin_url('roles'));
        }
        $response = $this->roles_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('role_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('role')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('role_lowercase')));
        }
        redirect(admin_url('roles'));
    }
}
