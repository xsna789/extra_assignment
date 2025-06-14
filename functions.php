<?php
// Core extraction functions
function extractEmails($text) {
    preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}\b/i', $text, $matches);
    return array_unique($matches[0]);
}

function extractPhoneNumbers($text) {
    // preg_match_all('/(?:\+?\d{1,3}[-.\s]?)?\(?\d{2,3}\)?[-.\s]?\d{3}[-.\s]?\d{4}\b/', $text, $matches);
    // return array_unique($matches[0]);
    
    $patterns = [
        // International format (e.g., +1 234 567 8901, +442079460000)
        '/\+[\d]{1,4}[\s\-]?[\d]{2,4}[\s\-]?[\d]{3,4}[\s\-]?[\d]{3,4}/',

        // Standard formats with separators
        // '/\(?\d{3}\)?[\s\-.]?\d{3}[\s\-.]?\d{4}/',  // US/Canada (123) 456-7890
        // '/\d{4}[\s\-.]?\d{3}[\s\-.]?\d{3}/',        // Other formats (1234 567 890)
        // '/\d{2}[\s\-.]?\d{4}[\s\-.]?\d{4}/',        // Common in some countries (12 3456 7890)

        // Toll-free numbers
        '/\b(?:800|855|866|877|888)[\s\-.]?\d{3}[\s\-.]?\d{4}\b/',

        // Compact formats (when no separators are used)
        // '/\b\d{7,12}\b/',
        # International numbers
        // '/\+[\d]{1,4}[-\s]?[\d]{2,4}[-\s]?[\d]{3,4}[-\s]?[\d]{3,4}/',

        # US/Canada numbers
        // '/\(?[2-9][0-9]{2}\)?[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}/',

        # General phone numbers (7-15 digits)
        // '/\b[\d]{7,15}\b/',
        #Cambodia
        '/\(\+855\)?\d{2,3}[-\s]?\d{3}[-\s]?\d{3}/',

        # With extensions
        '/(?:phone|tel|telephone)[:\s]*([+\d][\d\s\-().]{7,})(?:\s*(?:ext|extension|x)[:\s]*(\d+))?/i'
    ];
      
    $phones = [];
    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $text, $matches);
        foreach ($matches[0] as $number) {
            // Clean the number (keep only digits and leading +)
            $cleaned = preg_replace('/[^\+\d]/', '', $number);

            // Additional validation
            $valid = false;

            // Check international numbers
            if (str_starts_with($cleaned, '+')) {
                $valid = (strlen($cleaned) >= 9 && strlen($cleaned) <= 16);
            }
            // Check US/Canada numbers
            elseif (preg_match('/^1?\d{10}$/', $cleaned)) {
                $valid = true;
                // Add country code if missing
                if (strlen($cleaned) === 10) {
                    $cleaned = '1' . $cleaned;
                }
            }
            // Check other numbers
            else {
                $valid = (strlen($cleaned) >= 8 && strlen($cleaned) <= 15);
            }

            if ($valid) {
                $phones[] = $cleaned;
            }
        }
    }

    // Remove duplicates and reindex array
    return array_values(array_unique($phones));

    }

   
function extractImageUrls($html, $baseUrl) {
    $images = [];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tif', 'tiff', 'bmp', 'svg'];

    // Extract from <img src="">
    preg_match_all('/<img[^>]+src\s*=\s*[\'"]([^\'">]+)[\'"]/i', $html, $matches);
    $images = array_merge($images, $matches[1]);

    // Extract from inline style="background-image:url(...)"
    preg_match_all('/style\s*=\s*"[^"]*background(?:-image)?\s*:\s*url\([\'"]?([^"\')]+)[\'"]?\)[^"]*"/i', $html, $matches2);
    $images = array_merge($images, $matches2[1]);

    // Extract from <style> tags
    preg_match_all('/background(?:-image)?\s*:\s*url\([\'"]?([^"\')]+)[\'"]?\)/i', $html, $matches3);
    $images = array_merge($images, $matches3[1]);

    // Filter and resolve URLs
    $filteredImages = [];
    foreach ($images as $src) {
        $src = trim($src, "\"' ");

        // Skip data URIs and invalid URLs
        if (preg_match('/^data:/i', $src)) {
            continue;
        }

        // Resolve relative URLs
        $fullUrl = filter_var($src, FILTER_VALIDATE_URL) ? $src : resolveUrl($src, $baseUrl);

        // Check for allowed image extensions
        $path = parse_url($fullUrl, PHP_URL_PATH);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if (in_array(strtolower($ext), $allowedExtensions)) {
            $filteredImages[] = $fullUrl;
        }
    }

    return array_values(array_unique($filteredImages));
}

function resolveUrl($relative, $base)
{
    if (empty($relative)) {
        return '';
    }

    // Handle protocol-relative URLs
    if (strpos($relative, '//') === 0) {
        $parsedBase = parse_url($base);
        return ($parsedBase['scheme'] ?? 'http') . ':' . $relative;
    }

    // Handle absolute URLs
    if (filter_var($relative, FILTER_VALIDATE_URL)) {
        return $relative;
    }

    $parsedBase = parse_url($base);
    $scheme = $parsedBase['scheme'] ?? 'http';
    $host = $parsedBase['host'] ?? '';
    $basePath = $parsedBase['path'] ?? '/';

    // Handle root-relative URLs
    if (strpos($relative, '/') === 0) {
        return "$scheme://$host$relative";
    }

    // Handle relative URLs
    $baseDir = rtrim(dirname($basePath), '/');
    return "$scheme://$host$baseDir/" . ltrim($relative, '/');
}

// function extractImageUrls($text) {
//     preg_match_all('/<img[^>]+src="([^">]+)"|(https?:\/\/[^"\s]+\.(jpg|jpeg|png|gif|webp))/i', $text, $matches);
//     return array_unique(array_filter(array_merge($matches[1], $matches[2])));
// }

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