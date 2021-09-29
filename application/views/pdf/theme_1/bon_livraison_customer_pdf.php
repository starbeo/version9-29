<?php
include(APPPATH . 'third_party/Barcode/BarcodeGenerator.php');
include(APPPATH . 'third_party/Barcode/BarcodeGeneratorPNG.php');
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();

$data .= '<div style="border: 3px solid #000; height: auto;">';

$data .= '<div style="min-height: 100px; height: auto;">
            <div style="float: left; width: 45%; height: 15%; padding: 15px 10px 5px 10px;">
                <img src="' . logo_pdf_url() . '" style="width: 70%; height: 85px;">
            </div>
            <div style="float: right; width: 49%; height: 15%; line-height: 18px; padding-left: 10px; font-weight: bold;">
                <p><u>' . $bon_livraison->nom_expediteur . '</u></p>
                <p>Nombre colis : ' . count($bon_livraison->items) . '</p>
                <p>Date : ' . date(get_current_date_format(), strtotime($bon_livraison->date_created)) . '</p>
            </div>
          </div>
         ';

$data .= '<div style="font-weight: bold; font-size: 30px; text-align: center;  min-height: 80px; margin-bottom: 20px height: auto;">
            Bon Livraison N&deg; : ' . $bon_livraison_number . '
          </div>
         ';

$data .= "  <table border=1 style='width: 100%; margin: 0px 10px; 0px 0px;'>
                <tr>
                    <td align=center width='7%'>N&deg;</td>
                    <td align=center width='20%'>Code d'envoi</td>
                    <td align=center width='20%'>N° commande</td>
                    <td align=center width='10%'>Ouverture Colis</td>
                    <td align=center width='20%'>Destinataire</td>
                    <td align=center width='16%'>Telephone</td>
                    <td align=center width='16%'>Ville</td>
                    <td align=center width='16%'>crbt</td>
                </tr>
                <tbody>";
$cpt = 1;
$total = 0;
foreach ($bon_livraison->items as $item) {
    $date_ramassage = '';
    if (!is_null($item['date_ramassage'])) {
        $date_ramassage = date('d/m/Y', strtotime($item['date_ramassage']));
    }
    $ouverture = 'Non';
    if ($item['ouverture'] == 1) {
        $ouverture = 'Oui';
    }

    $data .= "  <tr>
                    <td align=center>" . $cpt . "</td>
                    <td align=center>" . $item['code_barre'] . "</td>
                    <td align=center>" . $item['num_commande'] . "</td>
                    <td align=center><b>" . $ouverture . "</b></td>
                    <td align=center>" . $item['nom_complet'] . "</td>
                    <td align=center>" . $item['telephone'] . "</td>
                    <td align=center>" . $item['ville'] . "</td>
                    <td align=center>" . number_format($item['crbt'], 2, ',', ' ') . "</td>
                </tr>";
    $total += $item['crbt'];
    $cpt++;
}
$data .= "      </tbody>
            </table>
         ";

$data .= "  <table border=1 style='width: 40%; margin: 20px 10px 10px auto;'>
                <tr>
                    <td align='center'>Total</td>
                    <td align='right' style='padding-right:10px'>" . number_format($total, 2, ',', ' ') . "</td>
                </tr>
            </table>";

if (get_option('show_message_after_signature_bon_livraison') == 1) {
    $data .= '<div style="min-height: 70px; height: auto;">
            <div style="padding: 10px 10px 5px 10px; font-weight: bold;">Les informations données par l\'expéditeur engagent sa responsabilité quant à la nature et la valeur réelle de la marchandise.</div>
          </div>';
}

$data .= '<div style="min-height: 70px; height: auto;">
            <div style="float: left; width: 45%; padding: 10px 10px 5px 10px; font-weight: bold;">
                <p>Signature Client :</p>
            </div>
            <div style="float: right; width: 49%; padding: 10px 10px 5px 10px; font-weight: bold;">
                <p>Signature Ramasseur :</p>
            </div>
          </div>
         ';

$data .= '</div>';

