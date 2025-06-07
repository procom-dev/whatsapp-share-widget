<?php
header('Content-Type: application/json');

if (!isset($_POST['url'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'URL not provided']);
    exit;
}

$url = filter_var($_POST['url'], FILTER_SANITIZE_URL);

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid URL']);
    exit;
}

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5'
        ]
    ]
]);

$html = @file_get_contents($url, false, $context);

if ($html === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch URL']);
    exit;
}

libxml_use_internal_errors(true);
$doc = new DOMDocument();
@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
$xpath = new DOMXPath($doc);

// Initialize variables
$title = '';
$description = '';
$thumbnail = '';

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

$domain = parse_url($url, PHP_URL_HOST);

libxml_clear_errors();

echo json_encode([
    'success' => true,
    'data' => [
        'title' => strip_tags($title),
        'description' => strip_tags($description),
        'thumbnail' => filter_var($thumbnail, FILTER_SANITIZE_URL),
        'domain' => $domain
    ]
]);