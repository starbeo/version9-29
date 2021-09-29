<?php

$data  = '';

$data .= '<div style="border: 3px solid #000; height: auto;">';

$data .= '<div>
            <div style="float: left; width: 45%; height: 10%; padding: 15px 10px 5px 10px;">
                <img src="'.logo_pdf_url().'" style="width: 70%; height: 85px;">
            </div>
            <div style="float: right; width: 49%; height: 10%; line-height: 18px; padding-left: 10px; font-weight: bold;">
                <p>Date du : '.date('d/m/Y', strtotime($facture_interne->date_created)).'</p>
                <p>Nombre de factures : '.count($facture_interne->items).'</p>
            </div>
          </div>
         ';

$data .= '<div style="font-weight: bold; font-size: 25px; text-align: center;">
            Virements Clients
          </div>
         ';

$data .= '<div style="font-size: 20px; text-align: center;">
            Facture Interne N&deg; : '.$invoice_number.'
          </div>';

$data .= "  <table border=1 style='width: 100%; margin: 10px 5px; 20px 5px;'>
                <thead>
                    <tr>
                        <td align=center width='5%'>N&deg;</td>
                        <td align=center width='15%'>Facture</td>
                        <td align=center width='15%'>Responsable</td>
                        <td align=center width='15%'>Client</td>
                        <td align=center width='15%'>Banque</td>
                        <td align=center width='22%'>RIB</td>
                        <td align=center width='13%'>Total NET</td>
                    </tr>
                </thead>
                <tbody>";
$cpt = 1;  
foreach ($facture_interne->items as $item) {
    $data .= "  <tr>
                    <td align=center>".$cpt."</td>
                    <td align=center>".$item['nom']."</td>
                    <td align=center style='height:40px'>".strtoupper($item['client_contact'])."</td>
                    <td align=center>".strtoupper($item['client'])."</td>
                    <td align=center>".strtoupper($item['client_banque'])."</td>
                    <td align=center>".$item['client_rib']."</td>
                    <td align=right style='padding-right:10px'>".number_format($item['total_net'], 2, ',', ' ')."</td>
                </tr>"; 
    $cpt++;
}
$data .= "      <tr>
                    <td align='right' colspan='6' style='padding-right:10px; height:40px;'>Total Globale</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_net, 2, ',', ' ') ."</td>
                </tr>
            </tbody>
        </table>";

$data .= '</div>';

