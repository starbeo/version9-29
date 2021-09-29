<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sliders extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('sliders_model');
    }

    /**
     * List all sliders
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('sliders');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array('tblsliders.name', 'tblsliders.file', 'tblsliders.active', 'tblsliders.addedfrom', 'tblsliders.date_created');

            $sIndexColumn = "id";
            $sTable = 'tblsliders';

            $join = array('LEFT JOIN tblstaff ON tblstaff.staffid = tblsliders.addedfrom');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, '', array(), array('id', 'tblsliders.file_type', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur'), 'tblsliders.id', '', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblsliders.name') {
                        $_data = '<a href="' . admin_url('sliders/slider/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblsliders.file') {
                        $_data = '<a href="' . site_url('download/file/slider/' . $aRow['id']) . '"><i class="' . get_mime_class($aRow['file_type']) . '"></i> ' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblsliders.active') {
                        $checked = '';
                        if ($aRow['tblsliders.active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['id'] . '" data-switch-url="admin/sliders/change_slider_status" ' . $checked . '>';
                        // For exporting
                        $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                    } else if ($aColumns[$i] == 'tblsliders.addedfrom') {
                        $utilisateurId = $_data;
                        $_data = staff_profile_image($utilisateurId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $utilisateurId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
                    } else if ($aColumns[$i] == 'tblsliders.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('admin/sliders/slider/' . $aRow['id'], 'pencil-square-o', 'btn-default');
                $options .= icon_btn('admin/sliders/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('sliders');
        $this->load->view('admin/sliders/manage', $data);
    }

    /**
     * Edit or add new slider 
     */
    public function slider($id = '')
    {
        if (!is_admin()) {
            access_denied('sliders');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                $id = $this->sliders_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('slider')));
                    redirect(admin_url('sliders'));
                }
            } else {
                $success = $this->sliders_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('slider')));
                }
                redirect(admin_url('sliders/slider/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('slider_lowercase'));
        } else {
            $slider = $this->sliders_model->get($id);
            if (!$slider) {
                set_alert('warning', _l('not_found', _l('slider')));
                redirect(admin_url('sliders'));
            }

            $data['slider'] = $slider;
            $title = _l('edit', _l('slider_lowercase'));
        }

        $data['title'] = $title;
        $this->load->view('admin/sliders/slider', $data);
    }

    /**
     * Remove file slider
     */
    public function remove_file_slider($id)
    {
        if (!is_admin()) {
            access_denied('sliders');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('sliders'));
        }

        $response = $this->sliders_model->remove_file($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('file')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('file_lowercase')));
        }

        redirect(admin_url('sliders/slider/' . $id));
    }

    /**
     * Change slider status / active / inactive
     */
    public function change_slider_status($id, $status)
    {
        if (is_admin() && $this->input->is_ajax_request()) {
            $this->sliders_model->change_slider_status($id, $status);
        }
    }

    /**
     * Delete slider
     */
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('sliders');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('sliders'));
        }

        $response = $this->sliders_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('slider')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('slider_lowercase')));
        }

        redirect(admin_url('sliders'));
    }
}
