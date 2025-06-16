/*************  ✨ Windsurf Command ⭐  *************/
<?php

require_once('tcpdf/tcpdf.php');

function handlePdfAction() {
    require_once 'tcpdf.php';
    $type = $_POST['type'] ?? '';
    $data = $_SESSION[$type] ?? [];
    if (!empty($data)) {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Find Image, Email & Phone Numbers');
        $pdf->SetTitle("{$type}_".date('Ymd-His'));
        $pdf->SetSubject('');
        $pdf->SetKeywords('');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();
        $html = '<h1>'.ucfirst($type).'</h1><ul>';
        foreach ($data as $item) {
            $html .= "<li>{$item}</li>";
        }
        $html .= '</ul>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output("{$type}_".date('Ymd-His').'.pdf', 'D');
    }
}
/*******  20c79578-2b7d-4173-8fc4-26f0cf0af2b8  *******/