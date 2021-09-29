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

    $date_ramassage = '';
    if(!is_null($item['date_ramassage'])){
        $date_ramassage = date('d/m/Y', strtotime($item['date_ramassage']));
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
    if(!is_null($item['logo'])){
        $url_logo = base_url_logo_client() . $item['expediteur_id'] . '/thumb_'.$item['logo'];
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
    $ouverture = 'Non';
    if($item['ouverture'] == 1){
        $ouverture = 'Oui';
    }

    $data .= "<div style='border: 1px solid #000; float: ".$float."; display: inline-block; width: 48%; height: 360px; padding: 5px; margin-bottom: 5px;  line-height: 10px;'>
                    <table width=100%;>
                        <tbody>
                            <tr>
                                <td><b>Expéditeur :</b> ".$item['nom']."</td>
                            </tr>
                            <tr>
                                <td><b>Date :</b> ".$date_ramassage."</td>
                            </tr>
                        </tbody>
                    </table>
                    <table width=100%;>
                        <tbody>
                            <tr>
                                <td width=45%;>
                                    <img src='".logo_pdf_url()."' style='width: 90%; height: 80px;'>
                                </td>
                                <td width=10%;></td>
                                <td width=45%;>".$logo_html."</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style='float: left; display: inline-block; width: 47%; height: 180px; padding: 5px; margin: 10px 0px; line-height: 10px;'>
                        <h3>Destinataire : </h3>
                        <p style='line-height: 15px;'><b>Nom & Prénom : </b>".$item['nom_complet']."</p>
                        <p style='line-height: 20px;'><b>Adresse : </b>".trim($adresse)." (".$ville.")</p>
                        <p><b>N° Tel : </b>".$telephone."</p>
                        <p><b>Ouverture Colis : </b>".$ouverture."</p>
                        <h3>Crbt : ".$item['crbt']." Dhs</h3>";
                        if(get_option('show_poids_coli_in_etiquette') == 1) {
                            $data .= "<p><b>Poids : </b>".get_option('poids_coli')."</p>";
                        }
    $data .= " </div>
                    <div style='float: right; display: inline-block; width: 47%; height: 180px; padding: 5px; margin: 10px 0px; line-height: 10px; text-align: center; '>
                        <img src='data:image/png;base64,".base64_encode($generator->getBarcode($item['code_barre'], $generator::TYPE_CODE_128))."' style='width:100%; height:1.8cm;' />
                        <h3>".$item['code_barre']."</h3></br></br>
                        <p align=left style='font-size: 12px; text-align : left;'><b>N° commande : </b>".$item['num_commande']."</p>
                        <p align=left style='font-size: 12px; text-align : left;'><b>Site Web : </b><span style='color : #428bca;'>".get_option('website')."</span></p>";
if(get_option('show_phone_number_in_etiquette_bon_livraison') == 1) {
$data .= "              <p align=left style='font-size: 12px; text-align : left;'><b>N° Téléphone : </b>".get_option('phone_number_etiquette')."</p>";
} else {
$data .= "              <p align=left style='font-size: 12px; text-align : left;'><b>N° Téléphone : </b>".$item['telephone_expediteur']."</p>";
}
$data .= "          </div>
                </div>
             ";
}
$data .= '</div>';

