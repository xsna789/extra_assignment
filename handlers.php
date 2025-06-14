<?php
session_start();
require_once __DIR__ . '/functions.php';  // Fixed path with slash

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

    if ($content !== false) {
        $_SESSION['emails'] = extractEmails($content);
        $_SESSION['phones'] = extractPhoneNumbers($content);
        $_SESSION['images'] = extractImageUrls($content, $input);
        $_SESSION['source'] = $isUrl ? "URL: $input" : "Text Input";
    } else {
        throw new Exception("Could not fetch URL content");
    }
}
function handleclear_result_form() {

  $input = trim($_POST['input'] ?? '');
    $isUrl = validateUrl($input);

    $content = $isUrl ? fetchUrlContent($input) : $input;

    if ($content !== false) {
        $_SESSION['emails'] = extractEmails($content);
        $_SESSION['phones'] = extractPhoneNumbers($content);
        $_SESSION['images'] = extractImageUrls($content, $input);
        $_SESSION['source'] = $isUrl ? "URL: $input" : "Text Input";

        session_destroy();
        session_unset();
    }
  // Unset all session variables
  
  
  // Destroy the session
  
  
  // Redirect to the form page
    header('Location: index.php');
  exit;
}
?>
