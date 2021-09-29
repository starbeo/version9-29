<?php

include(APPPATH . 'third_party/Barcode/BarcodeGenerator.php');
include(APPPATH . 'third_party/Barcode/BarcodeGeneratorPNG.php');
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();

$i     = 0;
$data .= "<div style='min-height: 100px; height: auto;'>";
foreach ($bon_livraison->items as $key => $item) {
    $float = '';
    if ($i % 2 == 1){ $float = 'right'; }
    else if ($i % 2 == 0){ $float = 'left'; }
    $i++;

    $date_creation = '';
    if(!is_null($item['date_creation'])){
        $date_creation = date('d/m/Y', strtotime($item['date_creation']));
    }
    $quartier = '';
    if(!is_null($item['quartier'])){
        $quartier = "(".$item['quartier'].")";
    }
    $ville = '';
    if(!is_null($item['ville'])){
        $ville = $item['ville'];
    }
    $logo_html = '';
    if(!is_null($bon_livraison->logo)){
        $url_logo = base_url_logo_client() . get_expediteur_user_id() . '/thumb_'.$bon_livraison->logo;
        $logo_html = "<img src='".$url_logo."' style='width: 90%; height: 80px;'>";
    }
    $telephone = $item['telephone'];
    if(!is_null($item['telephone']) && !empty($item['telephone']) && strlen($item['telephone']) == 10){
        $telephone = str_replace("\r\n", " ", chunk_split($item['telephone'], 2));
    }
    $adresse = strip_tags($item['adresse']);
    if(!is_null($item['adresse']) && !empty($item['adresse']) && strlen($item['adresse']) > 70){
        $adresse = substr($item['adresse'], 0, 70).'...';
    }

    $data .= "<div style='float: ".$float."; width: 49.5%; height: 360px; margin-bottom: 5px; line-height: 10px;'>
                <div style='width: 60%; float: left; margin: 0; text-align: center; padding-top: 5px;'>
                    <img src='data:image/png;base64,".base64_encode($generator->getBarcode($item['code_barre'], $generator::TYPE_CODE_128))."' style='width:80%; height:0.7cm;' />
                    <h6 style='font-size: 9px; letter-spacing: 5px; margin: 0;'>* ".$item['code_barre']." *</h6>
                </div>
                <div style='width: 39%; float: right; margin: 0;  text-align: center; padding-top: 5px;'>
                    <img src='".logo_pdf_url()."' style='width: 50%; height: 33px;'>
                </div>
                <div style='border: 1px dashed #000; width: 100%; '>
                    <div style='border-right: 1px solid #000; width: 49%; float: left; margin: 0;'>
                        <p style='border-bottom: 2px solid #000; margin: 0; padding: 5px; font-size: 15px; font-weight: bold;'>Expéditeur:</p>
                        <p style='border-bottom: 2px solid #000; margin: 0; padding: 5px; font-size: 13px; font-weight: bold; text-align: center;'>LUCIDO</p>
                        <table style='width: 100%;'>
                            <tbody>
                                <tr>
                                    <td style='font-size: 10px;'>Adresse: </td>
                                    <td>
                                        <p style='margin: 0; max-height: 40px; font-size: 11px;'>RT MOHAMMED CENTRE LEERAC ETAGE 1 APPT 04</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 10px;'>Tel: </td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; font-size: 11px;'>+212623947826</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 10px;'>Ville: </td>
                                    <td style='border: 1px dashed #000; text-align: center; '>
                                        <p style='margin: 0; font-weight: bold; font-size: 12px;'>CASABLANCA</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='font-size: 10px;'>Site web: </td>
                                    <td>
                                        <p style='margin: 0; font-weight: bold; color: cornflowerblue; text-decoration: underline; font-size: 11px;'>" . get_option('website') . "</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p style='border-top: 2px solid #000; border-bottom: 2px solid #000; margin: 0; padding: 5px; font-size: 15px; font-weight: bold;'>Destinataire:</p>
                        <p style='margin: 0; padding: 5px; font-size: 12px; text-align: center;'>" . $item['nom_complet'] . "</p>
                        <p style='margin: 0; padding: 5px; font-size: 15px; line-height: 17px; height: 55px; text-align: center;'>" . trim($adresse) . "</p>
                        <p style='margin: 0; padding: 5px; font-size: 15px;'><span style='width: 25%; display: inline-block;'>Tel:</span> <span style='font-weight: bold;'>" . $telephone . "</span></p>
                        <p style='margin: 0; padding: 5px; font-size: 15px;'>Ville:</p>
                        <p style='border-top: 2px solid #000; margin: 0; padding: 10px; font-size: 13px; font-weight: bold; text-align: center;'>" . $ville . "</p>
                    </div>
                    <div style='width: 48%; margin: 0;'>
                        <p style='border-bottom: 2px solid #000; margin: 0; padding: 5px; font-size: 15px; font-weight: bold;'>Date d'expedition : ".$date_creation."</p>
                        <p style='margin: 0; padding: 5px 0 10px; font-size: 11px;'>MNT CRBT a payer en espece:</p>
                        <p style='margin: 0; border-bottom: 2px solid #000; padding: 5px; font-size: 18px; text-align: center; font-weight: bold;'>".$item['crbt']." DH</p>
                        <p style='margin: 0; padding: 5px; font-size: 11px;'>CCP N:</p>
                        <p style='margin: 0; border-bottom: 2px solid #000; padding: 5px; font-size: 14px; text-align: center; font-weight: bold;'>93 708 81</p>
                        <p style='margin: 0; padding: 5px; font-size: 11px;'>Paiement:</p>
                        <p style='margin: 0; padding: 5px; font-size: 14px; text-align: center; font-weight: bold;'>Cash a la livraison</p>
                        <p style='margin: 0; padding: 5px; font-size: 11px;'>Commande client:</p>
                        <p style='margin: 0; border-bottom: 2px solid #000; padding: 5px; font-size: 14px; text-align: center; font-weight: bold;'>" . $item['nom'] . "</p>
                        <p style='margin: 0; padding: 5px; font-size: 11px;'>Instruction:</p>
                        <p style='margin: 0; padding: 5px; font-size: 14px; text-align: center; font-weight: bold;'>Ou toute personne qui se presente</p>
                        <p style='margin: 0; padding: 5px; text-align: center;'>
                            <img src='".site_url('assets/images/defaults/amana.png')."' style='width: 40%; height: 49px;'>
                        </p>
                    </div>
                </div>
              </div>";         
}
$data .= '</div>';

