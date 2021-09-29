<?php

function handle_expense_attachments($id)
{
    $path = EXPENSE_ATTACHMENTS_FOLDER . $id . '/';
    $CI = & get_instance();
    if (isset($_FILES['file']['name'])) {
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path
            $filename = $_FILES["file"]["name"];
            $newFilePath = $path . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI->db->where('id', $id);
                $CI->db->update('tblexpenses', array(
                    'attachment' => $filename,
                    'filetype' => $_FILES["file"]["type"]
                ));
            }
        }
    }
}

/**
 * Check for company logo upload
 * @param  mixed $ticketid
 * @return boolean
 */
function handle_company_logo_upload()
{
    $CI = & get_instance();
    $id_E = $CI->session->userdata('staff_user_id_entreprise');
    if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
        $path = COMPANY_FILES_FOLDER . '/' . $id_E . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['company_logo']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["company_logo"]["name"]);
            $extension = $path_parts['extension'];

            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }

            // Setup our new file path
            $filename = 'logo' . '.' . $extension;
            $newFilePath = $path . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('company_logo', $id_E . '/' . $filename);
                return true;
            }
        }
    }
    return false;
}

function handle_favicon_upload()
{
    if (isset($_FILES['favicon']['name']) && $_FILES['favicon']['name'] != '') {
        $path = COMPANY_FILES_FOLDER . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['favicon']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["favicon"]["name"]);
            $extension = $path_parts['extension'];
            // Setup our new file path
            $filename = 'favicon' . '.' . $extension;
            $newFilePath = $path . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('favicon', $filename);
                return true;
            }
        }
    }
    return false;
}

/**
 * Check for staff profile image
 * @param  mixed $ticketid
 * @return boolean
 */
function handle_staff_profile_image_upload()
{
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        $path = STAFF_PROFILE_IMAGES_FOLDER . get_staff_user_id() . '/';

        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            // Getting file extension
            $path_parts = pathinfo($_FILES["profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }
            // Setup our new file path
            $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . '/index.html', 'w');
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI = & get_instance();

                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 32;
                $config['height'] = 32;

                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('staffid', get_staff_user_id());
                $CI->db->update('tblstaff', array(
                    'profile_image' => $filename
                ));

                // Remove original image
                unlink($newFilePath);
                return true;
            }
        }
    }
    return false;
}

/**
 * Check for staff profile image
 * @param  mixed $ticketid
 * @return boolean
 */
function handle_client_logo_upload()
{
    if (isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
        $path = CLIENTS_LOGO_FOLDER . get_expediteur_user_id() . '/';

        // Get the temp file path
        $tmpFilePath = $_FILES['logo']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            // Getting file extension
            $path_parts = pathinfo($_FILES["logo"]["name"]);
            $extension = $path_parts['extension'];
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }
            // Setup our new file path
            $filename = unique_filename($path, $_FILES["logo"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . '/index.html', 'w');
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI = & get_instance();

                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 32;
                $config['height'] = 32;

                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('id', get_expediteur_user_id());
                $CI->db->update('tblexpediteurs', array(
                    'logo' => $filename
                ));

                // Remove original image
                unlink($newFilePath);
                return true;
            }
        }
    }
    return false;
}

/**
 * Handles uploads error with translation texts
 * @param  mixed $error type of error
 * @return mixed
 */
function _vtech_upload_error($error)
{
    $phpFileUploadErrors = array(
        0 => _l('file_uploaded_success'),
        1 => _l('file_exceds_max_filesize'),
        2 => _l('file_exceds_maxfile_size_in_form'),
        3 => _l('file_uploaded_partially'),
        4 => _l('file_not_uploaded'),
        6 => _l('file_missing_temporary_folder'),
        7 => _l('file_failed_to_write_to_disk'),
        8 => _l('file_php_extension_blocked'),
    );

    if (isset($phpFileUploadErrors[$error]) && $error != 0) {
        return $phpFileUploadErrors[$error];
    }
    return false;
}

/**
 * Function that return full path for upload based on passed type
 * @param  string $type
 * @return string
 */
function get_upload_path_by_type($type)
{
    switch ($type) {
        case 'expense':
            return EXPENSE_ATTACHMENTS_FOLDER;
            break;
        case 'staff':
            return STAFF_PROFILE_IMAGES_FOLDER;
            break;
        case 'company':
            return COMPANY_FILES_FOLDER;
            break;
        case 'sliders':
            return SLIDERS_FILE_FOLDER;
            break;
        default:
            return false;
    }
}

function facture_pdf($invoice, $download = true)
{
    //Generate Name Invoice PDF
    $invoice_number = $invoice->nom;

    include(APPPATH . 'third_party/MPDF57/mpdf.php');
     $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($invoice_number);
    $mpdf->list_indent_first_level = 0;
    $data = '';
    include(APPPATH . 'views/pdf/' . get_option('theme_pdf_facture') . '/facture_pdf.php');
    $mpdf->WriteHTML($data);
    if ($download == true) {
        $mpdf->Output($invoice_number . '.pdf', 'D');
    } else {
        return $mpdf;
    }
}

function facture_factureinterne_pdf($invoice)
{
    //Generate Name Invoice PDF
    $invoice_number = $invoice->nom;

    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($invoice_number);
    $mpdf->list_indent_first_level = 0;
    include(APPPATH . 'views/pdf/' . get_option('theme_pdf_facture') . '/facture_pdf.php');
    $mpdf->WriteHTML($data);

    return $mpdf;
}





function facture_cl_pdf($invoice, $download = true)
{
    //Generate Name Invoice PDF
    $invoice_number = $invoice->nom;

    include(APPPATH . 'third_party/MPDF57/mpdf.php');
    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($invoice_number);
    $mpdf->list_indent_first_level = 0;
    $data = '';
    include(APPPATH . 'views/pdf/' . get_option('theme_pdf_facture') . '/ecl_pdf.php');
    $mpdf->WriteHTML($data);
    if ($download == true) {
        $mpdf->Output($invoice_number . '.pdf', 'D');
    } else {
        return $mpdf;
    }
}




function bon_livraison_pdf($bon_livraison)
{
    //Generate Name Bon Livraison PDF
    $bonLivraisonName = $bon_livraison->nom;

    include(APPPATH . 'third_party/MPDF57/mpdf.php');
    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($bonLivraisonName);
    $mpdf->list_indent_first_level = 0;
    $data = '';
    include(APPPATH . 'views/pdf/' . get_option('theme_pdf_bon_livraison') . '/bon_livraison_pdf.php');
    $mpdf->WriteHTML($data);
    $mpdf->Output($bonLivraisonName . '.pdf', 'D');
}

function bon_livraison_customer_pdf($bon_livraison)
{
    //Generate Name Bon Livraison PDF
    $bon_livraison_number = 'BL-' . date('dmy') . '-' . $bon_livraison->id;

    include(APPPATH . 'third_party/MPDF57/mpdf.php');
    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($bon_livraison_number);
    $mpdf->list_indent_first_level = 0;
    $data = '';
    include(APPPATH . 'views/pdf/' . get_option('theme_pdf_bon_livraison') . '/bon_livraison_customer_pdf.php');
    $mpdf->WriteHTML($data);
    $mpdf->Output($bon_livraison_number . '.pdf', 'D');
}

function etiquette_bon_livraison_pdf($bon_livraison, $type)
{
    include(APPPATH . 'third_party/MPDF57/mpdf.php');

    //Generate Name Bon Livraison PDF
    $etiquette_number = 'ETIQUETTE-' . date('dmy') . '-' . $bon_livraison->id;

    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 5, 5, 0, 0);
    $mpdf->AddPage('L');
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($etiquette_number);
    $mpdf->list_indent_first_level = 0;
    $data = '';
    if (get_option('etiquette_amana') == 1 && $type == 'amana') {
        include(APPPATH . 'views/pdf/' . get_option('theme_pdf_etiquette_bon_livraison') . '/etiquette_amana_bon_livraison_pdf.php');
    } else {
        include(APPPATH . 'views/pdf/' . get_option('theme_pdf_etiquette_bon_livraison') . '/etiquette_bon_livraison_pdf.php');
    }
    $mpdf->WriteHTML($data);
    $mpdf->Output($etiquette_number . '.pdf', 'D');
}

function etiquette_bon_livraison_customer_pdf($bon_livraison, $type)
{
    include(APPPATH . 'third_party/MPDF57/mpdf.php');

    //Generate Name Bon Livraison PDF
    $etiquette_number = 'ETIQUETTE-' . date('dmy') . '-' . $bon_livraison->id;

    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 5, 5, 0, 0);
    $mpdf->AddPage('L');
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($etiquette_number);
    $mpdf->list_indent_first_level = 0;
    $data = '';
    if (get_option('etiquette_amana') == 1 && $type == 'amana') {
        include(APPPATH . 'views/pdf/' . get_option('theme_pdf_etiquette_bon_livraison') . '/etiquette_amana_bon_livraison_customer_pdf.php');
    } else {
        include(APPPATH . 'views/pdf/' . get_option('theme_pdf_etiquette_bon_livraison') . '/etiquette_bon_livraison_customer_pdf.php');
    }
    $mpdf->WriteHTML($data);
    $mpdf->Output($etiquette_number . '.pdf', 'D');
}

function etat_colis_livrer_pdf($etat)
{
    //Generate Name Etat Colis Livrer PDF
    $etat_number = $etat->nom;

    include(APPPATH . 'third_party/MPDF57/mpdf.php');
    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($etat_number);
    $mpdf->list_indent_first_level = 0;
    include(APPPATH . 'views/pdf/etat_colis_livrer_pdf.php');
    $mpdf->WriteHTML($data);
    $mpdf->Output($etat_number . '.pdf', 'D');
}

function etat_colis_livrer_excel($filename, $etatsColisLivrer)
{
    $CI = & get_instance();
    //load PHPExcel library
    $CI->load->library('excel');
    $filename = str_replace("/", "-", $filename);

    $CI->excel->setActiveSheetIndex(0);
    $number = 1;
    $sheet = $CI->excel->getActiveSheet();
    $sheet->setTitle('Liste Etats Colis Livrer');
    $sheet->setCellValue('A' . $number, $filename);
    $sheet->mergeCells('A' . $number . ':Q' . $number);
    //Style Column Header
    $styleBorders = array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $styleColumnHeader = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(30);
    $sheet->getStyle('A' . $number)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array(
            'rgb' => 'CCCCCC'
        )
    ));

    $number++;
    $sheet->setCellValue('A' . $number, 'N°');
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('B' . $number, 'Nom Etat');
    $sheet->getStyle('B' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('C' . $number, 'Livreur');
    $sheet->getStyle('C' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('D' . $number, 'Ville');
    $sheet->getStyle('D' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('E' . $number, 'Statut');
    $sheet->getStyle('E' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('F' . $number, 'Etat');
    $sheet->getStyle('F' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('G' . $number, 'Colis Livré');
    $sheet->getStyle('G' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('H' . $number, 'Montant Total Colis');
    $sheet->getStyle('H' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('I' . $number, 'Commission');
    $sheet->getStyle('I' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('J' . $number, 'Total A Payer');
    $sheet->getStyle('J' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('K' . $number, 'Total Versement');
    $sheet->getStyle('K' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('K' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('L' . $number, 'Date Dernier Versement');
    $sheet->getStyle('L' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('L' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('M' . $number, 'Reste');
    $sheet->getStyle('M' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('M' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('N' . $number, 'Référence Transaction');
    $sheet->getStyle('N' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('N' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('O' . $number, 'Commentaire');
    $sheet->getStyle('O' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('O' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('P' . $number, 'Créer Par');
    $sheet->getStyle('P' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('P' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('Q' . $number, 'Date Création');
    $sheet->getStyle('Q' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('Q' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(25);

    //Style Column Header
    $styleColumnData = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    //Add Lines Etats Colis Livrer
    $number++;
    foreach ($etatsColisLivrer as $key => $item) {
        $sheet->getRowDimension($number)->setRowHeight(20);
        //Column Numéro
        $sheet->setCellValue('A' . $number, $key + 1);
        $sheet->getStyle('A' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('A')->setWidth(5);
        //Column Name Etat Colis Livrer
        $sheet->setCellValue('B' . $number, $item['nom']);
        $sheet->getStyle('B' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('B')->setWidth(25);
        //Column Full Name Delivery Men
        $sheet->setCellValue('C' . $number, $item['fullname_livreur']);
        $sheet->getStyle('C' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('C')->setWidth(25);
        //Column City
        $sheet->setCellValue('D' . $number, $item['ville']);
        $sheet->getStyle('D' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('D')->setWidth(20);
        //Column Status
        if ($item['status'] == 1) {
            $valueColumnEtat = 'En attente';
            $colorColumnEtat = '03a9f4';
        } else {
            $valueColumnEtat = 'Valider';
            $colorColumnEtat = '259b24';
        }
        $sheet->getStyle('E' . $number)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => $colorColumnEtat
            )
        ));
        $sheet->setCellValue('E' . $number, $valueColumnEtat);
        $sheet->getStyle('E' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('E')->setWidth(12);
        //Column Etat
        if ($item['etat'] == 1) {
            $valueColumnStatus = 'Non Réglé';
            $colorColumnStatus = 'fc2d42';
        } else {
            $valueColumnStatus = 'Réglé';
            $colorColumnStatus = '259b24';
        }
        $sheet->getStyle('F' . $number)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => $colorColumnStatus
            )
        ));
        $sheet->setCellValue('F' . $number, $valueColumnStatus);
        $sheet->getStyle('F' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('F')->setWidth(12);
        //Column Number of Colis
        $sheet->setCellValue('G' . $number, $item['nbr_colis']);
        $sheet->getStyle('G' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('G')->setWidth(15);
        //Column Total
        $sheet->setCellValue('H' . $number, (float) number_format($item['total'], 2, ',', ''));
        $sheet->getStyle('H' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('H')->setWidth(20);
        //Column Commision
        $sheet->setCellValue('I' . $number, (float) number_format($item['commision'], 2, ',', ''));
        $sheet->getStyle('I' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('I')->setWidth(20);
        //Column Total to pay
        $sheet->setCellValue('J' . $number, (float) number_format($item['total_a_payer'], 2, ',', ''));
        $sheet->getStyle('J' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('J')->setWidth(20);
        //Column Total payment
        $sheet->setCellValue('K' . $number, (float) number_format($item['total_received'], 2, ',', ''));
        $sheet->getStyle('K' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('K' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('K')->setWidth(20);
        //Column Last date payment
        $sheet->setCellValue('L' . $number, date(get_current_date_format(), strtotime($item['last_date_versement'])));
        $sheet->getStyle('L' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('L' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('L')->setWidth(20);
        //Column Rest
        $sheet->setCellValue('M' . $number, (float) number_format($item['reste'], 2, ',', ''));
        if ($item['reste'] > 0) {
            $colorColumnReste = '03a9f4';
        } else if ($item['reste'] == 0) {
            $colorColumnReste = '259b24';
        } else {
            $colorColumnReste = 'fc2d42';
        }
        $sheet->getStyle('M' . $number)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => $colorColumnReste
            )
        ));
        $sheet->getStyle('M' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('M' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('M')->setWidth(10);
        //Column Reference Transaction
        $sheet->setCellValue('N' . $number, $item['reference_transaction']);
        $sheet->getStyle('N' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('N' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('N')->setWidth(25);
        //Column Comment
        $sheet->setCellValue('O' . $number, $item['justif']);
        $sheet->getStyle('O' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('O' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('O')->setWidth(60);
        //Column Full Name Staff
        $sheet->setCellValue('P' . $number, $item['fullname_utilisateur']);
        $sheet->getStyle('P' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('P' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('P')->setWidth(20);
        //Column Date created
        $sheet->setCellValue('Q' . $number, date(get_current_date_format(), strtotime($item['date_created'])));
        $sheet->getStyle('Q' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('Q' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('Q')->setWidth(25);

        $number++;
    }

    $filename = str_replace("/", "-", $filename) . '.xls';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . str_replace("/", "-", $filename));
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

function facture_interne_pdf($facture_interne, $upload = true, $type = '')
{
    //Generate Name Invoice PDF
    $invoice_number = $facture_interne->nom;

    include(APPPATH . 'third_party/MPDF57/mpdf.php');
    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($invoice_number);
    $mpdf->list_indent_first_level = 0;
    if ($type == 'detailles') {
        include(APPPATH . 'views/pdf/facture_interne_detailles_pdf.php');
    } else {
        include(APPPATH . 'views/pdf/facture_interne_pdf.php');
    }
    $mpdf->WriteHTML($data);

    if ($upload == true) {
        $mpdf->Output($invoice_number . '.pdf', 'D');
    } else {
        return $mpdf;
    }
}

function facture_interne_excel($facture_interne)
{
    $CI = & get_instance();
    //load PHPExcel library
    $CI->load->library('excel');

    $factureInterneName = $facture_interne->nom;
    $CI->excel->setActiveSheetIndex(0);
    $sheet = $CI->excel->getActiveSheet();
    $sheet->setTitle('Virements Clients');
    $sheet->setCellValue('A1', $factureInterneName);
    $sheet->mergeCells('A1:G1');
    //Style Column Header
    $styleBorders = array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $styleColumnHeader = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $sheet->getStyle('A1')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A1')->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension('1')->setRowHeight(40);
    $sheet->getStyle('A1')->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array(
            'rgb' => 'CCCCCC'
        )
    ));

    $sheet->setCellValue('A2', 'N°');
    $sheet->getStyle('A2')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A2')->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('B2', 'Facture');
    $sheet->getStyle('B2')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('B2')->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('C2', 'Responsable');
    $sheet->getStyle('C2')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('C2')->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('D2', 'Client');
    $sheet->getStyle('D2')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('D2')->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('E2', 'Banque');
    $sheet->getStyle('E2')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('E2')->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('F2', 'RIB');
    $sheet->getStyle('F2')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('F2')->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('G2', 'Total NET');
    $sheet->getStyle('G2')->applyFromArray($styleColumnHeader);
    $sheet->getStyle('G2')->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension('2')->setRowHeight(25);

    //Style Column Header
    $styleColumnData = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    //Add cities
    $number = 3;
    $cpt = 1;
    foreach ($facture_interne->items as $key => $item) {
        $sheet->getRowDimension($number)->setRowHeight(20);
        //Column Numéro
        $sheet->setCellValue('A' . $number, $cpt);
        $sheet->getStyle('A' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('A')->setWidth(5);
        //Column Name
        $sheet->setCellValue('B' . $number, $item['nom']);
        $sheet->getStyle('B' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('B')->setWidth(30);
        //Column Contact Client
        $sheet->setCellValue('C' . $number, strtoupper($item['client_contact']));
        $sheet->getStyle('C' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('C')->setWidth(25);
        //Column Client
        $sheet->setCellValue('D' . $number, strtoupper($item['client']));
        $sheet->getStyle('D' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('D')->setWidth(25);
        //Column Bank Client
        $sheet->setCellValue('E' . $number, strtoupper($item['client_banque']));
        $sheet->getStyle('E' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('E')->setWidth(20);
        //Column RIB Client
        $sheet->setCellValue('F' . $number, $item['client_rib']);
        $sheet->getStyle('F' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('F')->setWidth(50);
        //Column Total
        $sheet->setCellValue('G' . $number, (float) number_format($item['total_net'], 2, ',', ''));
        $sheet->getStyle('G' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getStyle('G' . $number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $number++;
        $cpt++;
    }

    //Column Total Globale
    $sheet->getRowDimension($number)->setRowHeight(20);
    $sheet->setCellValue('A' . $number, 'Total Globale (Dhs)');
    $sheet->mergeCells('A' . $number . ':F' . $number);
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnData);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getStyle('A' . $number)->getFont()->setBold(true);
    $sheet->getStyle('A' . $number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue('G' . $number, (float) number_format($facture_interne->total_net, 2, ',', ''));
    $sheet->getStyle('G' . $number)->applyFromArray($styleColumnData);
    $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getStyle('G' . $number)->getFont()->setBold(true);
    $sheet->getStyle('G' . $number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $fileName = 'Virements Clients : ' . $factureInterneName . '.xls';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $fileName);
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

/**
 * Check for support attachment
 * @param  mixed $supportid
 * @return mixed false if no attachment || array uploaded attachments
 */
function handle_supports_attachments($supportid)
{
    $CI = & get_instance();
    $id_E = $CI->session->userdata('staff_user_id_entreprise');

    $path = SUPPORTS_ATTACHMENTS_FOLDER . $id_E . '/';
    if (!file_exists($path)) {
        mkdir($path);
    }
    $path .= $supportid . '/';

    $uploaded_files = array();
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path
            $filename = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;

            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }

            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                array_push($uploaded_files, array(
                    'filename' => $filename,
                    'filetype' => $_FILES["file"]["type"]
                ));
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Generate payment pdf
 * @param  object $payment All payment data
 * @return mixed object
 */
function payment_pdf($payment)
{
    $CI = & get_instance();

    $CI->load->library('pdf');
    $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle(_l('payment') . '#-' . $payment->id);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 26, PDF_MARGIN_RIGHT);
    $CI->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $CI->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $CI->pdf->SetAutoPageBreak(TRUE, 30);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont('freesans', '', 10);
    $pdf->AddPage();
    include(APPPATH . 'views/pdf/paymentpdf.php');
    return $pdf;
}

function etat_colis_livrer_by_date_pdf($name, $etats)
{
    include(APPPATH . 'third_party/MPDF57/mpdf.php');
    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 5, 5, 10, 10, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($name);
    $mpdf->list_indent_first_level = 0;
    include(APPPATH . 'views/pdf/etat_colis_livrer_by_date_pdf.php');
    $mpdf->WriteHTML($data);
    $mpdf->Output($name . '.pdf', 'D');
}

/**
 * Check for demande attached piece
 * @param  mixed $ticketid
 * @return boolean
 */
function handle_attached_piece_demande_upload($demandeId)
{
    if (isset($_FILES['attached_piece']['name']) && $_FILES['attached_piece']['name'] != '') {
        $path = DEMANDES_ATTACHED_PIECE_FOLDER . $demandeId . '/';

        // Get the temp file path
        $tmpFilePath = $_FILES['attached_piece']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path
            $filename = unique_filename($path, $_FILES["attached_piece"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (!file_exists($path)) {
                mkdir($path);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI = & get_instance();
                $CI->db->where('id', $demandeId);
                $CI->db->update('tbldemandes', array(
                    'attached_piece' => $filename,
                    'attached_piece_type' => $_FILES["attached_piece"]["type"]
                ));

                return true;
            }
        }
    }

    return false;
}

/**
 * Check for marketing image
 * @param  mixed $idMarketing
 * @return boolean
 */
function handle_image_marketing_upload($idMarketing)
{
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
        $path = MARKETING_ATTACHED_PIECE_FOLDER . $idMarketing . '/';

        // Get the temp file path
        $tmpFilePath = $_FILES['image']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path
            $filename = unique_filename($path, $_FILES["image"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (!file_exists($path)) {
                mkdir($path);
            }
            
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI = & get_instance();
                $CI->db->where('id', $idMarketing);
                $CI->db->update('tblmarketing', array(
                    'image' => $filename
                ));

                return true;
            }
        }
    }

    return false;
}

function colis_client_excel($fileName, $colis)
{
    $CI = & get_instance();
    //load PHPExcel library
    $CI->load->library('excel');
    $fileName = str_replace("/", "-", $fileName);

    $CI->excel->setActiveSheetIndex(0);
    $number = 1;
    $sheet = $CI->excel->getActiveSheet();
    $sheet->setTitle('Liste Colis');
    $sheet->setCellValue('A' . $number, $fileName);
    $sheet->mergeCells('A' . $number . ':J' . $number);
    //Style Column Header
    $styleBorders = array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $styleColumnHeader = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(30);
    $sheet->getStyle('A' . $number)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array(
            'rgb' => 'CCCCCC'
        )
    ));

    $number++;
    $sheet->setCellValue('A' . $number, 'N°');
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('B' . $number, 'Code d\'envoi');
    $sheet->getStyle('B' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('C' . $number, 'Destinataire');
    $sheet->getStyle('C' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('D' . $number, 'Téléphone');
    $sheet->getStyle('D' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('E' . $number, 'CRBT');
    $sheet->getStyle('E' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('F' . $number, 'Frais');
    $sheet->getStyle('F' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('G' . $number, 'Ville');
    $sheet->getStyle('G' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('H' . $number, 'Statut');
    $sheet->getStyle('H' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('I' . $number, 'Date Ramassage');
    $sheet->getStyle('I' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('J' . $number, 'Date Livraison');
    $sheet->getStyle('J' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(25);

    //Style Column Header
    $styleColumnData = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    //Add Lines Colis
    $number++;
    foreach ($colis as $key => $item) {
        $sheet->getRowDimension($number)->setRowHeight(20);
        //Column Numéro
        $sheet->setCellValue('A' . $number, $key + 1);
        $sheet->getStyle('A' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('A')->setWidth(5);
        //Column Barcode
        $sheet->setCellValue('B' . $number, $item['code_barre']);
        $sheet->getStyle('B' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('B')->setWidth(25);
        //Column Full Name
        $sheet->setCellValue('C' . $number, $item['nom_complet']);
        $sheet->getStyle('C' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('C')->setWidth(25);
        //Column Phone number
        $sheet->setCellValue('D' . $number, $item['telephone']);
        $sheet->getStyle('D' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('D')->setWidth(20);
        //Column CRBT
        if ($item['anc_crbt'] > 0) {
            $item['crbt'] = $item['anc_crbt'];
        }
        $item['crbt'] = is_numeric($item['crbt']) ? $item['crbt'] : 0;
        $sheet->setCellValue('E' . $number, (float) number_format($item['crbt'], 2, ',', ''));
        $sheet->getStyle('E' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('E')->setWidth(12);
        //Column Frais
        $item['frais'] = is_numeric($item['frais']) ? $item['frais'] : 0;
        $sheet->setCellValue('F' . $number, (float) number_format($item['frais'], 2, ',', ''));
        $sheet->getStyle('F' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('F')->setWidth(12);
        //Column City
        $sheet->setCellValue('G' . $number, $item['ville']);
        $sheet->getStyle('G' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('G')->setWidth(15);
        //Column Status
        $sheet->setCellValue('H' . $number, $item['statut']);
        $sheet->getStyle('H' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('H')->setWidth(20);
        //Column Date ramassage
        $dateRamassage = '';
        if (!is_null($item['date_ramassage'])) {
            $dateRamassage = date(get_current_date_format(), strtotime($item['date_ramassage']));
        }
        $sheet->setCellValue('I' . $number, $dateRamassage);
        $sheet->getStyle('I' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('I')->setWidth(20);
        //Column Date livraison
        $dateLivraison = '';
        if (!is_null($item['date_livraison'])) {
            $dateLivraison = date(get_current_date_format(), strtotime($item['date_livraison']));
        }
        $sheet->setCellValue('J' . $number, $dateLivraison);
        $sheet->getStyle('J' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('J')->setWidth(20);
        $number++;
    }

    $fileName = str_replace("/", "-", $fileName) . '.xls';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . str_replace("/", "-", $fileName));
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

/**
 * Upload file for slider
 * @param  mixed $sliderId
 * @return boolean
 */
function handle_file_slider_upload($sliderId)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        $path = SLIDERS_FILE_FOLDER . $sliderId . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["file"]["name"]);
            $extension = $path_parts['extension'];
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
                'gif'
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }
            // Setup our new file path
            $filename = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI = & get_instance();

                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 800;
                $config['height'] = 800;
                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();

                $CI->db->where('id', $sliderId);
                $CI->db->update('tblsliders', array(
                    'file' => $filename,
                    'file_type' => $_FILES["file"]["type"]
                ));

                return true;
            }
        }
    }

    return false;
}

/**
 * Generate contract pdf
 * @param  object $contract All contract data
 * @return mixed object
 * */
function contract_pdf($contract)
{
    //Generate Name Contract PDF
    $contractName = $contract->subject;
    include(APPPATH . 'third_party/MPDF57/mpdf.php');
    $mpdf = new mPDF('utf-8', 'A4', '', 'freesans', 10, 10, 10, 25, 0, 0);
    $mpdf->SetDisplayMode('fullwidth');
    $mpdf->SetTitle($contractName);
    $mpdf->list_indent_first_level = 0;
    // Check if document layout body exist
    $data = '';
    if ($contract->body) {
        $data .= $contract->body;
    }
    $mpdf->WriteHTML($data);

    $mpdf->Output($contractName . '.pdf', 'D');
}

function export_colis_excel($filename, $colis, $colisFacturer, $save = false)
{
    $CI = & get_instance();
    //load PHPExcel library
    $CI->load->library('excel');
    $filename = str_replace("/", "-", $filename);

    $CI->excel->setActiveSheetIndex(0);
    $number = 1;
    $sheet = $CI->excel->getActiveSheet();
    $sheet->setTitle('Liste Colis');
    $sheet->setCellValue('A' . $number, $filename);
    if ($colisFacturer) {
        $sheet->mergeCells('A' . $number . ':R' . $number);
    } else {
        $sheet->mergeCells('A' . $number . ':Q' . $number);
    }
    //Style Column Header
    $styleBorders = array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $styleColumnHeader = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(30);
    $sheet->getStyle('A' . $number)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array(
            'rgb' => 'CCCCCC'
        )
    ));

    $number++;
    $sheet->setCellValue('A' . $number, 'N°');
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('B' . $number, 'Code d\'envoi');
    $sheet->getStyle('B' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('C' . $number, 'Numéro de commande');
    $sheet->getStyle('C' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('D' . $number, 'Client');
    $sheet->getStyle('D' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('E' . $number, 'Livreur / Point Relai');
    $sheet->getStyle('E' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('F' . $number, 'Ville');
    $sheet->getStyle('F' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('G' . $number, 'Destinataire');
    $sheet->getStyle('G' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('H' . $number, 'Téléphone');
    $sheet->getStyle('H' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('I' . $number, 'CRBT');
    $sheet->getStyle('I' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('J' . $number, 'Frais');
    $sheet->getStyle('J' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('K' . $number, 'Statut');
    $sheet->getStyle('K' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('K' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('L' . $number, 'Etat');
    $sheet->getStyle('L' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('L' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('M' . $number, 'Date Ramassage');
    $sheet->getStyle('M' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('M' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('N' . $number, 'Date Livraison');
    $sheet->getStyle('N' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('N' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('O' . $number, 'Bon de livraison');
    $sheet->getStyle('O' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('O' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('P' . $number, 'Etat Colis Livrer');
    $sheet->getStyle('P' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('P' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('Q' . $number, 'Facture');
    $sheet->getStyle('Q' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('Q' . $number)->getBorders()->applyFromArray($styleBorders);
    if ($colisFacturer) {
        $sheet->setCellValue('R' . $number, 'Statut Facture');
        $sheet->getStyle('R' . $number)->applyFromArray($styleColumnHeader);
        $sheet->getStyle('R' . $number)->getBorders()->applyFromArray($styleBorders);
    }
    $sheet->getRowDimension($number)->setRowHeight(25);

    //Style Column Header
    $styleColumnData = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );

    //Add Lines Colis
    $number++;
    foreach ($colis as $key => $item) {
        $sheet->getRowDimension($number)->setRowHeight(20);
        //Column Numéro
        $sheet->setCellValue('A' . $number, $key + 1);
        $sheet->getStyle('A' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('A')->setWidth(5);
        //Column Barcode
        $sheet->setCellValue('B' . $number, $item['code_barre']);
        $sheet->getStyle('B' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('B')->setWidth(25);
        //Column Numero de commande
        $sheet->setCellValue('C' . $number, $item['num_commande']);
        $sheet->getStyle('C' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('C')->setWidth(25);
        //Column Client
        $sheet->setCellValue('D' . $number, $item['nom']);
        $sheet->getStyle('D' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('D')->setWidth(25);
        //Column Full Name Delivery Men
        if ($item['type_livraison'] == 'a_domicile') {
            $sheet->setCellValue('E' . $number, $item['livreur']);
            $sheet->getColumnDimension('E')->setWidth(25);
        } else {
            $sheet->setCellValue('E' . $number, $item['point_relai']);
            $sheet->getColumnDimension('E')->setWidth(80);
        }
        $sheet->getStyle('E' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
        //Column City
        $sheet->setCellValue('F' . $number, $item['ville']);
        $sheet->getStyle('F' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('F')->setWidth(20);
        //Column Destinataire
        $sheet->setCellValue('G' . $number, $item['nom_complet']);
        $sheet->getStyle('G' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('G')->setWidth(25);
        //Column Phone number
        $sheet->setCellValue('H' . $number, $item['telephone']);
        $sheet->getStyle('H' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('H')->setWidth(15);
        //Column Crbt
        $sheet->setCellValue('I' . $number, (float) number_format($item['crbt'], 2, ',', ''));
        $sheet->getStyle('I' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('I')->setWidth(10);
        //Column Frais
        $sheet->setCellValue('J' . $number, (float) number_format($item['frais'], 2, ',', ''));
        $sheet->getStyle('J' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('J')->setWidth(10);
        //Column Statut
        $colorColumnStatus = $item['statutColor'];
        if (empty($colorColumnStatus)) {
            $colorColumnStatus = 'ffffff';
        } else {
            $colorColumnStatus = str_replace('#', '', $colorColumnStatus);
        }
        $sheet->getStyle('K' . $number)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => $colorColumnStatus
            )
        ));
        $sheet->setCellValue('K' . $number, $item['statutName']);
        $sheet->getStyle('K' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('K' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('K')->setWidth(20);
        //Column Etat
        if ($item['etatName'] == 'Non Payé') {
            $colorColumnEtat = 'fc2d42';
        } else if ($item['etatName'] == 'Payé') {
            $colorColumnEtat = '259b24';
        } else if ($item['etatName'] == 'Facturé') {
            $colorColumnEtat = '28b8da';
        } else {
            $colorColumnEtat = 'ffffff';
        }
        $sheet->getStyle('L' . $number)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => $colorColumnEtat
            )
        ));
        $sheet->setCellValue('L' . $number, $item['etatName']);
        $sheet->getStyle('L' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('L' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('L')->setWidth(15);
        //Column date ramassage
        $sheet->setCellValue('M' . $number, date(get_current_date_format(), strtotime($item['date_ramassage'])));
        $sheet->getStyle('M' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('M' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('M')->setWidth(20);
        //Column date livraison
        $date = '';
        if ($item['status_reel'] == 2) {
            if (!is_null($item['date_livraison'])) {
                $date = date(get_current_date_format(), strtotime($item['date_livraison']));
            }
        } else if ($item['status_reel'] == 2) {
            if (!is_null($item['date_livraison'])) {
                $date = date(get_current_date_format(), strtotime($item['date_retour']));
            }
        }
        $sheet->setCellValue('N' . $number, $date);
        $sheet->getStyle('N' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('N' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('N')->setWidth(20);
        //Column Bon de livraison
        $sheet->setCellValue('O' . $number, $item['bon_livraison']);
        $sheet->getStyle('O' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('O' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('O')->setWidth(25);
        //Column Etat colis livrer
        $sheet->setCellValue('P' . $number, $item['etat_colis_livrer']);
        $sheet->getStyle('P' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('P' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('P')->setWidth(25);
        //Column Nom Facture
        $sheet->setCellValue('Q' . $number, $item['facture']);
        $sheet->getStyle('Q' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('Q' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('Q')->setWidth(25);
        if ($colisFacturer) {
            //Column Statut Facture
            if ($item['statut_facture'] == 1) {
                $item['statut_facture'] = 'Non réglé';
                $colorColumnStatutFacture = 'fc2d42';
            } else if ($item['statut_facture'] == 2) {
                $item['statut_facture'] = 'Réglé';
                $colorColumnStatutFacture = '259b24';
            } else {
                $item['statut_facture'] = '';
                $colorColumnStatutFacture = 'ffffff';
            }
            $sheet->getStyle('R' . $number)->getFill()->applyFromArray(array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => $colorColumnStatutFacture
                )
            ));
            $sheet->setCellValue('R' . $number, $item['statut_facture']);
            $sheet->getStyle('R' . $number)->applyFromArray($styleColumnData);
            $sheet->getStyle('R' . $number)->getBorders()->applyFromArray($styleBorders);
            $sheet->getColumnDimension('R')->setWidth(15);
        }
        $number++;
    }

    $filename = str_replace("/", "-", $filename) . '.xls';
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    if ($save) {
        $path = TEMP_FOLDER . date('d-m-Y') . '/';
        if (!file_exists($path)) {
            mkdir($path);
            fopen($path . 'index.html', 'w');
        }
        $objWriter->save($path . str_replace("/", "-", $filename));
    } else {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . str_replace("/", "-", $filename));
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}


function export_demands_excel($filename, $colis, $save = false)
{
    $CI = & get_instance();
    //load PHPExcel library
    $CI->load->library('excel');
    $filename = str_replace("/", "-", $filename);

    $CI->excel->setActiveSheetIndex(0);
    $number = 1;
    $sheet = $CI->excel->getActiveSheet();
    $sheet->setTitle('Demandes ');
    $sheet->setCellValue('A' . $number, $filename);

    //Style Column Header
    $styleBorders = array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $styleColumnHeader = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(30);
    $sheet->getStyle('A' . $number)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array(
            'rgb' => 'CCCCCC'
        )
    ));

    $number++;
    $sheet->setCellValue('A' . $number, 'N°');
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('B' . $number, 'NOM');
    $sheet->getStyle('B' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('C' . $number, 'Type');
    $sheet->getStyle('C' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('D' . $number, 'Objet');
    $sheet->getStyle('D' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);

    $sheet->setCellValue('F' . $number, 'Priorité');
    $sheet->getStyle('F' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('G' . $number, 'Statut');
    $sheet->getStyle('G' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);

    $sheet->setCellValue('I' . $number, 'Client');
    $sheet->getStyle('I' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);

    $sheet->setCellValue('K' . $number, 'Message');
    $sheet->getStyle('K' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('K' . $number)->getBorders()->applyFromArray($styleBorders);

    $sheet->setCellValue('M' . $number, 'Date Creation');
    $sheet->getStyle('M' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('M' . $number)->getBorders()->applyFromArray($styleBorders);


    $sheet->getRowDimension($number)->setRowHeight(25);

    //Style Column Header
    $styleColumnData = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );

    //Add Lines Colis
    $number++;
    foreach ($colis as $key => $item) {
        $sheet->getRowDimension($number)->setRowHeight(20);
        //Column Numéro
        $sheet->setCellValue('A' . $number, $key + 1);
        $sheet->getStyle('A' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('A')->setWidth(5);
        //Column Barcode
        $sheet->setCellValue('B' . $number, $item['name']);
        $sheet->getStyle('B' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('B')->setWidth(25);
        //Column Numero de commande
        $sheet->setCellValue('C' . $number, $item['type']);
        $sheet->getStyle('C' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('C')->setWidth(25);
        //Column Client
        $sheet->setCellValue('D' . $number, $item['department']);
        $sheet->getStyle('D' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('D')->setWidth(25);
        //Column Full Name Delivery Men
     //   if ($item['type_livraison'] == 'a_domicile') {
         //   $sheet->setCellValue('E' . $number, $item['livreur']);
       //     $sheet->getColumnDimension('E')->setWidth(25);
      //  } else {
        //    $sheet->setCellValue('E' . $number, $item['point_relai']);
        //    $sheet->getColumnDimension('E')->setWidth(80);
       // }
        $sheet->getStyle('E' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
        //Column City
           $priorty = priorite_demande($item['priorite']);
        $sheet->setCellValue('F' . $number, $priorty);
        $sheet->getStyle('F' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('F')->setWidth(20);
        //Column Destinataire
        $stat =status_demande($item['status']);
        $sheet->setCellValue('G' . $number, $stat);
        $sheet->getStyle('G' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('G')->setWidth(25);
        //Column Phone number
     //   $sheet->setCellValue('H' . $number, $item['rating']);
      //  $sheet->getStyle('H' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
       // $sheet->getColumnDimension('H')->setWidth(15);
        //Column Client
        $sheet->setCellValue('I' . $number, $item['nom']);
        $sheet->getStyle('I' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('I')->setWidth(25);
        //Column Crbt
        //$sheet->setCellValue('I' . $number, (float) number_format($item['crbt'], 2, ',', ''));
        //$sheet->getStyle('I' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
        //$sheet->getColumnDimension('I')->setWidth(10);
        //Column Frais
       // $sheet->setCellValue('J' . $number, (float) number_format($item['frais'], 2, ',', ''));
        //$sheet->getStyle('J' . $number)->applyFromArray($styleColumnData);
       // $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
       // $sheet->getColumnDimension('J')->setWidth(10);
        //Column Statut
     //   $colorColumnStatus = $item['datecreated'];
      //  if (empty($colorColumnStatus)) {
     //       $colorColumnStatus = 'ffffff';
       // } else {
        //    $colorColumnStatus = str_replace('#', '', $colorColumnStatus);
      //  }
     //   $sheet->getStyle('K' . $number)->getFill()->applyFromArray(array(
        //    'type' => PHPExcel_Style_Fill::FILL_SOLID,
         //   'color' => array(
         //       'rgb' => $colorColumnStatus
         //   )
       // ));
        $sheet->setCellValue('K' . $number, $item['message']);
        $sheet->getStyle('K' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('K' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('K')->setWidth(80);
        //Column Etat
      //  if ($item['etatName'] == 'Non Payé') {
        //    $colorColumnEtat = 'fc2d42';
       // } else if ($item['etatName'] == 'Payé') {
        //    $colorColumnEtat = '259b24';
      //  } else if ($item['etatName'] == 'Facturé') {
        //    $colorColumnEtat = '28b8da';
       // } else {
        //    $colorColumnEtat = 'ffffff';
        //}
       // $sheet->getStyle('L' . $number)->getFill()->applyFromArray(array(
          //  'type' => PHPExcel_Style_Fill::FILL_SOLID,
          //  'color' => array(
           //     'rgb' => $colorColumnEtat
         //   )
       // ));
      //  $sheet->setCellValue('L' . $number, $item['etatName']);
       // $sheet->getStyle('L' . $number)->applyFromArray($styleColumnData);
      //  $sheet->getStyle('L' . $number)->getBorders()->applyFromArray($styleBorders);
     //   $sheet->getColumnDimension('L')->setWidth(15);
        //Column date ramassage
       $sheet->setCellValue('M' . $number, date(get_current_date_format(), strtotime($item['datecreated'])));
        $sheet->getStyle('M' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('M' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('M')->setWidth(20);
        //Column date livraison
      //  $date = '';
      //  if ($item['status_reel'] == 2) {
         //   if (!is_null($item['date_livraison'])) {
          //      $date = date(get_current_date_format(), strtotime($item['date_livraison']));
           // }
      //  } else if ($item['status_reel'] == 2) {
         //   if (!is_null($item['date_livraison'])) {
          //      $date = date(get_current_date_format(), strtotime($item['date_retour']));
          // }
      //  }
      //  $sheet->setCellValue('N' . $number, $date);
       // $sheet->getStyle('N' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('N' . $number)->getBorders()->applyFromArray($styleBorders);
       // $sheet->getColumnDimension('N')->setWidth(20);
        //Column Bon de livraison
     //   $sheet->setCellValue('O' . $number, $item['bon_livraison']);
      //  $sheet->getStyle('O' . $number)->applyFromArray($styleColumnData);
      //  $sheet->getStyle('O' . $number)->getBorders()->applyFromArray($styleBorders);
       // $sheet->getColumnDimension('O')->setWidth(25);
        //Column Etat colis livrer
      //  $sheet->setCellValue('P' . $number, $item['etat_colis_livrer']);
      //  $sheet->getStyle('P' . $number)->applyFromArray($styleColumnData);
      //  $sheet->getStyle('P' . $number)->getBorders()->applyFromArray($styleBorders);
     //   $sheet->getColumnDimension('P')->setWidth(25);
        //Column Nom Facture
       // $sheet->setCellValue('Q' . $number, $item['facture']);
      //  $sheet->getStyle('Q' . $number)->applyFromArray($styleColumnData);
      //  $sheet->getStyle('Q' . $number)->getBorders()->applyFromArray($styleBorders);
      //  $sheet->getColumnDimension('Q')->setWidth(25);
      //  if ($colisFacturer) {
            //Column Statut Facture
          //  if ($item['statut_facture'] == 1) {
              //  $item['statut_facture'] = 'Non réglé';
           //     $colorColumnStatutFacture = 'fc2d42';
          //  } else if ($item['statut_facture'] == 2) {
            //    $item['statut_facture'] = 'Réglé';
          //      $colorColumnStatutFacture = '259b24';
          //  } else {
             //   $item['statut_facture'] = '';
            //    $colorColumnStatutFacture = 'ffffff';
          //  }
         //   $sheet->getStyle('R' . $number)->getFill()->applyFromArray(array(
            //    'type' => PHPExcel_Style_Fill::FILL_SOLID,
           //     'color' => array(
            //        'rgb' => $colorColumnStatutFacture
          //      )
         //   ));
         //   $sheet->setCellValue('R' . $number, $item['statut_facture']);
         //   $sheet->getStyle('R' . $number)->applyFromArray($styleColumnData);
         //   $sheet->getStyle('R' . $number)->getBorders()->applyFromArray($styleBorders);
       //     $sheet->getColumnDimension('R')->setWidth(15);
       // }
        $number++;
    }

    $filename = str_replace("/", "-", $filename) . '.xls';
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    if ($save) {
        $path = TEMP_FOLDER . date('d-m-Y') . '/';
        if (!file_exists($path)) {
            mkdir($path);
            fopen($path . 'index.html', 'w');
        }
        $objWriter->save($path . str_replace("/", "-", $filename));
    } else {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . str_replace("/", "-", $filename));
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}


function export_bl_excel($filename, $colis, $save = false)
{
    $CI = & get_instance();
    //load PHPExcel library
    $CI->load->library('excel');
    $filename = str_replace("/", "-", $filename);

    $CI->excel->setActiveSheetIndex(0);
    $number = 1;
    $sheet = $CI->excel->getActiveSheet();
    $sheet->setTitle('Bon ');
    $sheet->setCellValue('A' . $number, $filename);

    //Style Column Header
    $styleBorders = array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $styleColumnHeader = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(30);
    $sheet->getStyle('A' . $number)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array(
            'rgb' => 'CCCCCC'
        )
    ));

    $number++;
    $sheet->setCellValue('A' . $number, 'N°');
    $sheet->getStyle('A' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('B' . $number, 'NOM');
    $sheet->getStyle('B' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('C' . $number, 'Type Livraison');
    $sheet->getStyle('C' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('D' . $number, 'Type');
    $sheet->getStyle('D' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);

    $sheet->setCellValue('F' . $number, 'Statut');
    $sheet->getStyle('F' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->setCellValue('G' . $number, 'Nbr de colis');
    $sheet->getStyle('G' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);

    $sheet->setCellValue('I' . $number, 'Livreur / Point Relai');
    $sheet->getStyle('I' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);


    $sheet->setCellValue('E' . $number, 'Date Creation');
    $sheet->getStyle('E' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);

    $sheet->setCellValue('H' . $number, 'Utilisateur');
    $sheet->getStyle('H' . $number)->applyFromArray($styleColumnHeader);
    $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
    $sheet->getRowDimension($number)->setRowHeight(25);

    //Style Column Header
    $styleColumnData = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        ),
        'font' => array(
            'name' => 'Arial',
            'size' => 11,
            'color' => array(
                'rgb' => '000000'
            )
        )
    );

    //Add Lines Colis
    $number++;
    foreach ($colis as $key => $item) {
        $sheet->getRowDimension($number)->setRowHeight(20);
        //Column Numéro
        $sheet->setCellValue('A' . $number, $key + 1);
        $sheet->getStyle('A' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('A' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('A')->setWidth(5);
        //Column Barcode
        $sheet->setCellValue('B' . $number, $item['nom']);
        $sheet->getStyle('B' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('B' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('B')->setWidth(25);
        //Column Numero de commande
        if ($item['type'] == 1) {
            $_daa = _l('delivery_note_type_output') ;
        } else if ($item['type']  == 2) {
            $_daa =  _l('delivery_note_type_returned') ;
        }
        $sheet->setCellValue('D' . $number, $_daa);
        $sheet->getStyle('D' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('D' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('D')->setWidth(25);
        //Column Client
        $aas = format_status_bl_export($item['status']);
        $sheet->setCellValue('F' . $number, $aas);
        $sheet->getStyle('F' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('F' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('F')->setWidth(25);
        //Column Full Name Delivery Men
        //   if ($item['type_livraison'] == 'a_domicile') {
        //   $sheet->setCellValue('E' . $number, $item['livreur']);
        //     $sheet->getColumnDimension('E')->setWidth(25);
        //  } else {
        //    $sheet->setCellValue('E' . $number, $item['point_relai']);
        //    $sheet->getColumnDimension('E')->setWidth(80);
        // }
       // $sheet->getStyle('E' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
        //Column City
        $aab =  render_nombre_colis_export($item['id_livreur']);
        $sheet->setCellValue('G' . $number, $aab);
        $sheet->getStyle('G' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('G' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('G')->setWidth(20);
        //Column Destinataire

        $sheet->setCellValue('E' . $number,  $item['date_created']);
        $sheet->getStyle('E' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('E' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('E')->setWidth(25);
        //user Staff
        $sheet->setCellValue('H' . $number,  $item['fullname_staff']);
        $sheet->getStyle('H' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('H')->setWidth(25);
        //Column Phone number
        //   $sheet->setCellValue('H' . $number, $item['rating']);
        //  $sheet->getStyle('H' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('H' . $number)->getBorders()->applyFromArray($styleBorders);
        // $sheet->getColumnDimension('H')->setWidth(15);
        //Column Client
        $sheet->setCellValue('C' . $number, $item['type_livraison']);
        $sheet->getStyle('C' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('C' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('c')->setWidth(25);
        //Column Crbt
        //$sheet->setCellValue('I' . $number, (float) number_format($item['crbt'], 2, ',', ''));
        //$sheet->getStyle('I' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
        //$sheet->getColumnDimension('I')->setWidth(10);
        //Column Frais
        // $sheet->setCellValue('J' . $number, (float) number_format($item['frais'], 2, ',', ''));
        //$sheet->getStyle('J' . $number)->applyFromArray($styleColumnData);
        // $sheet->getStyle('J' . $number)->getBorders()->applyFromArray($styleBorders);
        // $sheet->getColumnDimension('J')->setWidth(10);
        //Column Statut
        //   $colorColumnStatus = $item['datecreated'];
        //  if (empty($colorColumnStatus)) {
        //       $colorColumnStatus = 'ffffff';
        // } else {
        //    $colorColumnStatus = str_replace('#', '', $colorColumnStatus);
        //  }
        //   $sheet->getStyle('K' . $number)->getFill()->applyFromArray(array(
        //    'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //   'color' => array(
        //       'rgb' => $colorColumnStatus
        //   )
        // ));
      //  $sheet->setCellValue('K' . $number, $item['message']);
       // $sheet->getStyle('K' . $number)->applyFromArray($styleColumnData);
       // $sheet->getStyle('K' . $number)->getBorders()->applyFromArray($styleBorders);
       // $sheet->getColumnDimension('K')->setWidth(80);
        //Column Etat
        //  if ($item['etatName'] == 'Non Payé') {
        //    $colorColumnEtat = 'fc2d42';
        // } else if ($item['etatName'] == 'Payé') {
        //    $colorColumnEtat = '259b24';
        //  } else if ($item['etatName'] == 'Facturé') {
        //    $colorColumnEtat = '28b8da';
        // } else {
        //    $colorColumnEtat = 'ffffff';
        //}
        // $sheet->getStyle('L' . $number)->getFill()->applyFromArray(array(
        //  'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //  'color' => array(
        //     'rgb' => $colorColumnEtat
        //   )
        // ));
        //  $sheet->setCellValue('L' . $number, $item['etatName']);
        // $sheet->getStyle('L' . $number)->applyFromArray($styleColumnData);
        //  $sheet->getStyle('L' . $number)->getBorders()->applyFromArray($styleBorders);
        //   $sheet->getColumnDimension('L')->setWidth(15);
        //Column date ramassage
       // $sheet->setCellValue('M' . $number, date(get_current_date_format(), strtotime($item['datecreated'])));
        //$sheet->getStyle('M' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('M' . $number)->getBorders()->applyFromArray($styleBorders);
       // $sheet->getColumnDimension('M')->setWidth(20);
        //Column date livraison
        //  $date = '';
        //  if ($item['status_reel'] == 2) {
        //   if (!is_null($item['date_livraison'])) {
        //      $date = date(get_current_date_format(), strtotime($item['date_livraison']));
        // }
        //  } else if ($item['status_reel'] == 2) {
        //   if (!is_null($item['date_livraison'])) {
        //      $date = date(get_current_date_format(), strtotime($item['date_retour']));
        // }
        //  }
        //  $sheet->setCellValue('N' . $number, $date);
        // $sheet->getStyle('N' . $number)->applyFromArray($styleColumnData);
        //$sheet->getStyle('N' . $number)->getBorders()->applyFromArray($styleBorders);
        // $sheet->getColumnDimension('N')->setWidth(20);
        //Column Bon de livraison
          $sheet->setCellValue('I' . $number, $item['fullname_livreur']);
        $sheet->getStyle('I' . $number)->applyFromArray($styleColumnData);
        $sheet->getStyle('I' . $number)->getBorders()->applyFromArray($styleBorders);
        $sheet->getColumnDimension('I')->setWidth(25);
        //Column Etat colis livrer
        //  $sheet->setCellValue('P' . $number, $item['etat_colis_livrer']);
        //  $sheet->getStyle('P' . $number)->applyFromArray($styleColumnData);
        //  $sheet->getStyle('P' . $number)->getBorders()->applyFromArray($styleBorders);
        //   $sheet->getColumnDimension('P')->setWidth(25);
        //Column Nom Facture
        // $sheet->setCellValue('Q' . $number, $item['facture']);
        //  $sheet->getStyle('Q' . $number)->applyFromArray($styleColumnData);
        //  $sheet->getStyle('Q' . $number)->getBorders()->applyFromArray($styleBorders);
        //  $sheet->getColumnDimension('Q')->setWidth(25);
        //  if ($colisFacturer) {
        //Column Statut Facture
        //  if ($item['statut_facture'] == 1) {
        //  $item['statut_facture'] = 'Non réglé';
        //     $colorColumnStatutFacture = 'fc2d42';
        //  } else if ($item['statut_facture'] == 2) {
        //    $item['statut_facture'] = 'Réglé';
        //      $colorColumnStatutFacture = '259b24';
        //  } else {
        //   $item['statut_facture'] = '';
        //    $colorColumnStatutFacture = 'ffffff';
        //  }
        //   $sheet->getStyle('R' . $number)->getFill()->applyFromArray(array(
        //    'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //     'color' => array(
        //        'rgb' => $colorColumnStatutFacture
        //      )
        //   ));
        //   $sheet->setCellValue('R' . $number, $item['statut_facture']);
        //   $sheet->getStyle('R' . $number)->applyFromArray($styleColumnData);
        //   $sheet->getStyle('R' . $number)->getBorders()->applyFromArray($styleBorders);
        //     $sheet->getColumnDimension('R')->setWidth(15);
        // }
        $number++;
    }

    $filename = str_replace("/", "-", $filename) . '.xls';
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    if ($save) {
        $path = TEMP_FOLDER . date('d-m-Y') . '/';
        if (!file_exists($path)) {
            mkdir($path);
            fopen($path . 'index.html', 'w');
        }
        $objWriter->save($path . str_replace("/", "-", $filename));
    } else {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . str_replace("/", "-", $filename));
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}

 function search_coli_on_demand($colis)
{

    if ($colis != null)
    {

        $colis_barre ='';
        $CI = & get_instance();
            $CI->db->select('tblcolis.*');
            $CI->db->where('tblcolis.code_barre', $colis);
            $coli =$CI->db->get('tblcolis')->row();
            if ($coli != null)
                $colis_barre=  $coli->id;



        return $colis_barre ;
    }

    return '';
}
