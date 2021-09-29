<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Villes extends Client_controller
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * List cities
     */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblvilles.name',
                'tblvilles.delai'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblvilles';

            $where = array('AND tblvilles.active = 1');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $id_E, $where, array(), 'tblvilles.name', '', 'ASC');
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

                    if ($aColumns[$i] == 'tblvilles.name') {
                        $_data = '<b>' . $_data . '</b>';
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('cities');
        $this->load->view('client/villes/manage', $data);
    }
    
    /**
     * Export cities
     */
    public function export()
    {
        //Get all cities
        $this->load->model('villes_model');
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
