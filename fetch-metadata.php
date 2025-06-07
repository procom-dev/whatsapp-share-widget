<?php
session_start();
header('Content-Type: application/json');

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");

// CSRF Protection
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid request. Please refresh the page and try again.']);
    exit;
}

// Rate Limiting (2 seconds between requests)
$lastRequest = $_SESSION['last_metadata_request'] ?? 0;
if (time() - $lastRequest < 2) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Please wait a moment before making another request.']);
    exit;
}
$_SESSION['last_metadata_request'] = time();

if (!isset($_POST['url']) || empty(trim($_POST['url']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'URL not provided']);
    exit;
}

// Validate URL length
if (strlen($_POST['url']) > 2048) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'URL too long. Maximum 2048 characters allowed.']);
    exit;
}

$url = filter_var($_POST['url'], FILTER_SANITIZE_URL);

// Enhanced URL validation to prevent SSRF attacks
function isValidExternalUrl($url) {
    // Basic URL validation
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    $parsed = parse_url($url);
    
    // Only allow HTTP and HTTPS
    if (!$parsed || !in_array($parsed['scheme'], ['http', 'https'])) {
        return false;
    }
    
    // Get IP address of the host
    $ip = gethostbyname($parsed['host']);
    
    // Block private and reserved IP ranges
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    
    // Block localhost variations
    $blocked_hosts = ['localhost', '127.0.0.1', '::1', '0.0.0.0'];
    if (in_array($parsed['host'], $blocked_hosts)) {
        return false;
    }
    
    return true;
}

if (!isValidExternalUrl($url)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or restricted URL. Please use a public HTTP/HTTPS URL.']);
    exit;
}


$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 8,
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5'
        ]
    ]
]);

$html = @file_get_contents($url, false, $context);

if ($html === false) {
    $error = error_get_last();
    if ($error && strpos($error['message'], 'timed out') !== false) {
        http_response_code(408);
        echo json_encode(['success' => false, 'message' => 'Request timed out. The website is taking too long to respond.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch URL. The website may be unavailable or blocking requests.']);
    }
    exit;
}

// Validate content type
$headers = get_headers($url, 1);
if ($headers && isset($headers['Content-Type'])) {
    $contentType = is_array($headers['Content-Type']) ? end($headers['Content-Type']) : $headers['Content-Type'];
    if (strpos($contentType, 'text/html') === false && strpos($contentType, 'application/xhtml') === false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'URL does not point to a valid HTML page.']);
        exit;
    }
}

libxml_use_internal_errors(true);
$doc = new DOMDocument();
@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
$xpath = new DOMXPath($doc);

// Initialize variables
$title = '';
$description = '';
$thumbnail = '';
$language = '';

// Get Open Graph metadata
$ogTitle = $xpath->query("//meta[@property='og:title']/@content");
$ogDesc = $xpath->query("//meta[@property='og:description']/@content");
$ogImage = $xpath->query("//meta[@property='og:image']/@content");

// Title: OG Title > Twitter Title > Regular Title
if ($ogTitle->length > 0) {
    $title = $ogTitle->item(0)->nodeValue;
} else {
    $twitterTitle = $xpath->query("//meta[@name='twitter:title']/@content");
    if ($twitterTitle->length > 0) {
        $title = $twitterTitle->item(0)->nodeValue;
    } else {
        $titleTag = $xpath->query("//title");
        if ($titleTag->length > 0) {
            $title = $titleTag->item(0)->nodeValue;
        }
    }
}

// Description: OG Description > Twitter Description > Meta Description
if ($ogDesc->length > 0) {
    $description = $ogDesc->item(0)->nodeValue;
} else {
    $twitterDesc = $xpath->query("//meta[@name='twitter:description']/@content");
    if ($twitterDesc->length > 0) {
        $description = $twitterDesc->item(0)->nodeValue;
    } else {
        $metaDesc = $xpath->query("//meta[@name='description']/@content");
        if ($metaDesc->length > 0) {
            $description = $metaDesc->item(0)->nodeValue;
        }
    }
}

// Image: OG Image > Twitter Image > First image
if ($ogImage->length > 0) {
    $thumbnail = $ogImage->item(0)->nodeValue;
} else {
    $twitterImage = $xpath->query("//meta[@name='twitter:image']/@content");
    if ($twitterImage->length > 0) {
        $thumbnail = $twitterImage->item(0)->nodeValue;
    } else {
        $firstImage = $xpath->query("//img[@src]");
        if ($firstImage->length > 0) {
            $thumbnail = $firstImage->item(0)->getAttribute('src');
            if (strpos($thumbnail, 'http') !== 0) {
                if (strpos($thumbnail, '//') === 0) {
                    $thumbnail = 'https:' . $thumbnail;
                } else {
                    $thumbnail = rtrim($url, '/') . '/' . ltrim($thumbnail, '/');
                }
            }
        }
    }
}

// Language detection
$langAttr = $xpath->query("//html/@lang");
if ($langAttr->length > 0) {
    $language = $langAttr->item(0)->nodeValue;
} else {
    // Check meta http-equiv content-language
    $metaLang = $xpath->query("//meta[@http-equiv='content-language']/@content");
    if ($metaLang->length > 0) {
        $language = $metaLang->item(0)->nodeValue;
    } else {
        // Check meta name language
        $metaNameLang = $xpath->query("//meta[@name='language']/@content");
        if ($metaNameLang->length > 0) {
            $language = $metaNameLang->item(0)->nodeValue;
        } else {
            // Check og:locale
            $ogLocale = $xpath->query("//meta[@property='og:locale']/@content");
            if ($ogLocale->length > 0) {
                $language = $ogLocale->item(0)->nodeValue;
            }
        }
    }
}

// Clean and normalize language code
$language = strtolower(trim($language));
if (strpos($language, '-') !== false) {
    $language = explode('-', $language)[0]; // Get just the language part (e.g., 'en' from 'en-US')
}

$domain = parse_url($url, PHP_URL_HOST);

libxml_clear_errors();

echo json_encode([
    'success' => true,
    'data' => [
        'title' => strip_tags($title),
        'description' => strip_tags($description),
        'thumbnail' => filter_var($thumbnail, FILTER_SANITIZE_URL),
        'domain' => $domain,
        'language' => $language
    ]
]);