<?php
session_start();
require_once 'functions.php';  // Fixed path with slash

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'process';

    try {
        switch ($action) {
            case 'clear':
                handleclear_result_form();
                break;

            case 'save':
                handleSaveAction();
                break;

            case 'csv':
                handleCsvAction();
                break;

            case 'zip':
                handleZipAction();
                break;

            case 'pdf':
                handlePdfAction();
                break;

            case 'process':
            default:
                handleProcessAction();
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header('Location: index.php');
    exit;

    switch ($action) {
        case 'emails':
            $_SESSION['tab_order'] = ['emails', 'phones', 'images'];
            break;
    
        case 'phones':
            $_SESSION['tab_order'] = ['phones', 'emails', 'images'];
            break;
    
        case 'images':
            $_SESSION['tab_order'] = ['images', 'emails', 'phones'];
            break;
    
        default:
            // Handle default tab order or error
            break;
    }
}

function handleSaveAction() {
    $type = $_POST['type'] ?? '';
    $data = $_SESSION[$type] ?? [];
    if (!empty($data)) {
        $_SESSION['download_link'] = saveToFile("{$type}_" . date('Ymd-His'), $data);
    }
}

function handleCsvAction() {
    $type = $_POST['type'] ?? '';
    $data = $_SESSION[$type] ?? [];
    if (!empty($data)) {
        $_SESSION['download_link'] = saveToFile("{$type}_" . date('Ymd-His'), $data, 'csv');
    }
}

function handlePdfAction() {
    require_once 'TCPDF-main/tcpdf.php';
    $type = $_POST['type'] ?? '';
    $data = $_SESSION[$type] ?? [];
    
    if (!empty($data)) {
        $_SESSION['download_link'] = saveToFile("{$type}_" . date('Ymd-His'), $data, 'pdf');
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Data Extractor');
        $pdf->SetAuthor('Data Extractor');
        $pdf->SetTitle(ucfirst($type) . ' Extracted Data');
        $pdf->SetSubject('Extracted ' . ucfirst($type));
        
        // Set default header data
        $pdf->SetHeaderData('', 0, 'Extracted ' . ucfirst($type), 'Generated on ' . date('Y-m-d H:i:s'));
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 12);
        
        // Add title
        $pdf->Cell(0, 10, 'Extracted ' . ucfirst($type), 0, 1, 'C');
        $pdf->Ln(5);
        
        // Add data
        if (is_array($data)) {
            foreach ($data as $item) {
                $pdf->Cell(0, 10, '- ' . $item, 0, 1);
            }
        } else {
            $pdf->MultiCell(0, 10, $data);
        }
        
        // Generate file name
        $filename = "{$type}_data_" . date('Ymd-His') . '.pdf';
        
        // Close and output PDF document
        $pdf->Output($filename, 'D');
        exit;
    } else {
        throw new Exception("No data available to generate PDF");
    }

}

function handleZipAction() {
    $files = [];
    $timestamp = date('Ymd-His');
    foreach (['emails', 'phones', 'images', 'videos'] as $type) {
        if (!empty($_SESSION[$type])) {
            $files[] = saveToFile("{$type}_{$timestamp}", $_SESSION[$type]);
        }
    }
    if (!empty($files)) {
        $_SESSION['download_link'] = createZipArchive($files, "extracted_data_{$timestamp}");
    }
}

function handleProcessAction() {
    $input = trim($_POST['input'] ?? '');
    $isUrl = validateUrl($input);

    $content = $isUrl ? fetchUrlContent($input) : $input;
    // $_SESSION['tab-list'] = $content !== false ? ['emails', 'phones', 'images'] : [];

    if ($content !== false) {
        $_SESSION['emails'] = extractEmails($content);
        $_SESSION['phones'] = extractPhoneNumbers($content);
        $_SESSION['images'] = extractImageUrls($content, $input);
        $_SESSION['source'] = $isUrl ? "URL: $input" : "URL Input";
        $_SESSION['tab-list'] = ['emails', 'phones', 'images'];
    } else {
        throw new Exception("Could not fetch URL content");
    }
}

function handleclear_result_form() {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the form page
    header('Location: index.php');
    exit;
}
?>
