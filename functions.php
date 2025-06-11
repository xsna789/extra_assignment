<?php
// Core extraction functions
function extractEmails($text) {
    preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}\b/i', $text, $matches);
    return array_unique($matches[0]);
}

function extractPhoneNumbers($text) {
    preg_match_all('/(?:\+?\d{1,3}[-.\s]?)?\(?\d{2,3}\)?[-.\s]?\d{3}[-.\s]?\d{4}\b/', $text, $matches);
    return array_unique($matches[0]);
}

function extractImageUrls($text) {
    preg_match_all('/<img[^>]+src="([^">]+)"|(https?:\/\/[^"\s]+\.(jpg|jpeg|png|gif|webp))/i', $text, $matches);
    return array_unique(array_filter(array_merge($matches[1], $matches[2])));
}

// File handling functions
function saveToFile($filename, $data, $format = 'txt') {
    if (!is_dir('downloads')) mkdir('downloads');
    $path = "downloads/" . cleanFilename($filename) . ".$format";
    
    if ($format === 'csv') {
        $fp = fopen($path, 'w');
        foreach ($data as $item) fputcsv($fp, [$item]);
        fclose($fp);
    } else {
        file_put_contents($path, implode("\n", $data));
    }
    
    return $path;
}

function createZipArchive($files, $zipname) {
    $zip = new ZipArchive();
    $path = "downloads/" . cleanFilename($zipname) . ".zip";
    
    if ($zip->open($path, ZipArchive::CREATE) === TRUE) {
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        return $path;
    }
    return false;
}

function cleanFilename($name) {
    return preg_replace('/[^A-Za-z0-9\-]/', '_', $name);
}

// URL handling
function fetchUrlContent($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'DataExtractor/1.0',
        CURLOPT_TIMEOUT => 15
    ]);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content ?: false;
}

function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $url);
}
?>