<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Villes extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('villes_model');

        if (get_permission_module('villes') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all cities
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('Villes');
        }

        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblvilles.name');
            if (get_option('shipping_cost_by_ville') == 1) {
                array_push($aColumns, 'tblshippingcost.name', 'tblshippingcost.shipping_cost');
            }
            array_push($aColumns, 'tblvilles.frais_special', 'tblvilles.delai', 'tblvilles.active');

            $join = array();
            if (get_option('shipping_cost_by_ville') == 1) {
                array_push($join, 'LEFT JOIN tblshippingcost ON tblshippingcost.id = tblvilles.category_shipping_cost');
            }

            $sIndexColumn = "id";
            $sTable = 'tblvilles';

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, array(), array('tblvilles.id as villeId, tblvilles.category_shipping_cost'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'tblvilles.name') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#city_modal" data-id="' . $aRow['villeId'] . '" data-category-shipping-cost="' . $aRow['category_shipping_cost'] . '" data-frais-special="' . $aRow['tblvilles.frais_special'] . '" data-delai="' . $aRow['tblvilles.delai'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblvilles.active') {
                        $checked = '';
                        if ($aRow['tblvilles.active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['villeId'] . '" data-switch-url="admin/villes/change_ville_status" ' . $checked . '>';
                        // For exporting
                        $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                    }

                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#city_modal', 'data-id' => $aRow['villeId'], 'data-category-shipping-cost' => $aRow['category_shipping_cost'], 'data-frais-special' => $aRow['tblvilles.frais_special'], 'data-delai' => $aRow['tblvilles.delai']));
                $row[] = $options .= icon_btn('admin/villes/delete/' . $aRow['villeId'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();
        //Get categories shipping cost
        $this->load->model('shipping_cost_model');
        $data['categories_shipping_cost'] = $this->shipping_cost_model->get();

        $data['title'] = _l('als_cities');
        $this->load->view('admin/villes/manage', $data);
    }

    /**
     * Edit or add new city
     */
    public function ville()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->villes_model->add($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfuly', _l('city'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            } else {
                $success = $this->villes_model->update($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfuly', _l('city'));
                }
                echo json_encode(array('success' => $success, 'message' => $message));
            }
        }
    }

    /**
     * Assignment city
     */
    public function affectation()
    {
        $success = false;
        $message = _l('problem_assignment', _l('als_cities'));
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if (is_array($data['cities']) && count($data['cities']) > 0) {
                $success = $this->villes_model->affectation($data);
                if ($success == true) {
                    $message = _l('assignment_successfuly', _l('als_cities'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'message' => $message));
    }

    /**
     * Get shipping cost city
     */
    public function get_shipping_cost($cityId)
    {
        //Get Infos Ville
        $city = $this->villes_model->get_shipping_cost_city($cityId);
        $shippingCost = 0;
        if ($city) {
            if (is_numeric($city->shipping_cost)) {
                $shippingCost = $city->shipping_cost;
            }
        }

        echo json_encode(array('shipping_cost' => $shippingCost));
    }

    /**
     * Change status
     */
    public function change_ville_status($id, $status)
    {
        if (has_permission('products', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->villes_model->change_ville_status($id, $status);
            }
        }
    }

    /**
     * Delete city
     */
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Villes');
        }
        if (!$id) {
            redirect(admin_url('villes'));
        }

        $response = $this->villes_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('city')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('city_lowercase')));
        }

        redirect(admin_url('villes'));
    }

    /**
     * Export cities
     */
    public function export()
    {
        //Get all colis
        $cities = $this->villes_model->get();

        //load our new PHPExcel library
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        $sheet = $this->excel->getActiveSheet();
        //name the worksheet
        $sheet->setTitle('Liste des villes');
        //set cell A1 content with some text
        $sheet->setCellValue('A1', 'Nom ville');
        //Style Borders
        $styleBorders = array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '000000'
                )
            )
        );
        $styleColonneHeader = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => true,
                'size' => 11,
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'CCCCCC'
                )
            )
        );
        $sheet->getStyle('A1')->getBorders()->applyFromArray($styleBorders);
        $sheet->getStyle('A1')->applyFromArray($styleColonneHeader);

        //Add cities
        $number = 2;
        foreach ($cities as $city) {
            if (!empty($city['name'])) {
                $city['name'] = mb_convert_encoding($city['name'], 'utf-16LE', 'utf-8');
                $sheet->setCellValue('A' . $number, $city['name']);
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
                $number++;
            }
        }

        $filename = 'Liste des villes.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
}
