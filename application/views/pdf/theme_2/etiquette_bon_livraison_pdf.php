<?php
include(APPPATH . 'third_party/Barcode/BarcodeGenerator.php');
include(APPPATH . 'third_party/Barcode/BarcodeGeneratorPNG.php');
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();

$i = 0;
$data .= "<div style='min-height: 100px; height: auto;'>";
foreach ($bon_livraison->items as $key => $item) {
    $float = '';
    if ($i % 2 == 1) {
        $float = 'right';
    } else if ($i % 2 == 0) {
        $float = 'left';
    }
    $i++;

    $date_ramassage = '';
    if (!is_null($item['date_ramassage'])) {
        $date_ramassage = date(get_current_date_format(), strtotime($item['date_ramassage']));
    }
    $date_creation = date(get_current_date_format(), strtotime(date('Y-m-d')));
    $quartier = '';
    if (!is_null($item['quartier'])) {
        $quartier = "(" . $item['quartier'] . ")";
    }
    $ville = '';
    if (!is_null($item['ville'])) {
        $ville = $item['ville'];
    }
    $logo_html = '';
    if (!is_null($item['logo'])) {
        $url_logo = base_url_logo_client() . $item['expediteur_id'] . '/thumb_' . $item['logo'];
        $logo_html = "<img src='" . $url_logo . "' style='width: 80%; height: 50px;'>";
    }
    $telephone = $item['telephone'];
    if (!is_null($item['telephone']) && !empty($item['telephone']) && strlen($item['telephone']) == 10) {
        $telephone = str_replace("\r\n", " ", chunk_split($item['telephone'], 2));
    }
    $adresse = strip_tags($item['adresse']);
    if (!is_null($item['adresse']) && !empty($item['adresse']) && strlen($item['adresse']) > 70) {
        $adresse = substr($item['adresse'], 0, 70) . '...';
    }
    $ouverture = 'Non';
    if ($item['ouverture'] == 1) {
        $ouverture = 'Oui';
    }
    if (get_option('show_phone_number_in_etiquette_bon_livraison') == 1 && !empty(get_option('phone_number_etiquette'))) {
        $infos = get_option('website') . ' // ' . get_option('phone_number_etiquette');
    } else {
        $infos = get_option('website');
    }
    $data .= "<div style='float: " . $float . "; width: 49.5%; height: 360px; margin-bottom: 5px; line-height: 10px;'>
                <div style='border: 1px dashed #000; width: 100%;'>
                    <div style='border-right: 1px solid #000; width: 49%; float: left; margin: 0;'>
                        <p style='border-bottom: 2px solid #000; margin: 0 0 10px 0; padding: 5px; font-size: 15px; font-weight: bold; text-align: center;'>Client</p>
             ";
    if($bon_livraison->type_livraison == 'a_domicile') {
    $data .= "          <table style='width: 100%;'>
                            <tbody>
                                <tr>
                                    <td colspan=2 style='padding: 5px; text-align: center; height: 50px;'>" . $logo_html . "</td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Client :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 14px;'>" . strtoupper($item['nom']) . "</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Tél :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 14px;'>" . $item['telephone_expediteur'] . "</p>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>";
    } else {
        $data .= "      <table style='width: 100%;'>
                            <tbody>
                                <tr>
                                    <td colspan=2 style='padding: 5px; text-align: center; height: 50px;'>
                                        <img src='" . logo_pdf_url() . "' style='width: 90%; height: 50px;'>
                                    </td>
                                </tr>
                            </tbody>
                        </table>";
    }
    $data .= "          <p style='border-top: 2px solid #000; border-bottom: 2px solid #000; margin: 0 0 10px 0; padding: 5px; font-size: 13px; font-weight: bold; text-align: center;'>Destinataire</p>
                        <table style='width: 100%;'>
                            <tbody>
                                <tr>
                                    <td style='font-size: 11px;'>Nom Complet :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 12px;'>" . $item['nom_complet'] . "</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Tél :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 12px;'>" . $telephone . "</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Ville :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 12px; font-weight: bold;'>" . strtoupper($ville) . "</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Adresse :</td>
                                    <td>
                                        <p style='margin: 0; max-height: 200px; font-size: 12px;'>" . trim($adresse) . "</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p style='border-top: 2px solid #000; margin: 0; padding: 0;'></p>
                        <table style='width: 100%;'>
                            <tbody>
                                <tr>
                                    <td style='font-size: 11px;'>Ouverture Colis :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 12px;'>" . $ouverture . "</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Commentaire :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 12px;'>" . $item['commentaire'] . "</p>
                                    </td>
                                </tr>";
    if (get_option('show_poids_coli_in_etiquette') == 1) {
        $data .= "                  <tr>
                                    <td style='font-size: 11px;'>Poids :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 12px;'>" . get_option('poids_coli') . "</p>
                                    </td>
                                </tr>";
    }
    $data .= "              </tbody>
                        </table>
                    </div>
                    <div style='width: 49%; margin: 0;'>
                        <p style='border-bottom: 2px solid #000; margin: 0 0 10px 0; padding: 5px; font-size: 15px; font-weight: bold; text-align: center;'>Commande</p>
                        <table style='border-left: 1px solid #000; width: 100%;'>
                            <tbody>
                                <tr>
                                    <td style='font-size: 11px;'>Code d'envoi :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 14px;'>" . $item['code_barre'] . "</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Numéro de commande :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 14px;'>" . $item['num_commande'] . "</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 11px;'>Date d'expedition :</td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 14px;'>" . $date_creation . "</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p style='border-left: 1px solid #000; border-top: 2px solid #000; margin: 0; padding: 5px 0 10px 5px; font-size: 11px;'>MNT CRBT a payer en espece:</p>
                        <p style='border-left: 1px solid #000; margin: 0; padding: 5px; font-size: 15px; text-align: center; font-weight: bold;'>" . $item['crbt'] . " DH</p>
                        <p style='border-left: 1px solid #000; border-top: 2px solid #000; margin: 0; padding: 10px; text-align: center;'>
                            <img src='data:image/png;base64," . base64_encode($generator->getBarcode($item['code_barre'], $generator::TYPE_CODE_128)) . "' style='width:80%; height:0.8cm;' />
                            <h6 style='font-size: 12px; letter-spacing: 2px; margin: 5px; text-align: center;'>* " . $item['code_barre'] . " *</h6>
                        </p>
                        <p style='border-left: 1px solid #000; border-top: 2px solid #000; margin: 0; padding: 5px; font-size: 11px;'>Instruction:</p>
                        <p style='border-left: 1px solid #000; margin: 0; padding: 5px; font-size: 13px; text-align: center; font-weight: bold;'>Ou toute personne qui se presente</p>
                        <p style='border-left: 1px solid #000; margin: 0; padding: 5px; text-align: center; font-size: 11px; color : #428bca;'>" . $infos . "</p>
                        <p style='border-left: 1px solid #000; margin: 0; padding: 5px; text-align: center;'>
                            <img src='" . logo_pdf_url() . "' style='width: 90%; height: 50px;'>
                        </p>
                    </div>
                </div>
              </div>";
}
$data .= '</div>';
