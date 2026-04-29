<?php
// Save as fix.php and visit: https://dkaaa-production.up.railway.app/fix.php
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    // Fix common redirect issues
    $env = preg_replace('/FORCE_HTTPS=true/', 'FORCE_HTTPS=false', $env);
    $env = preg_replace('/APP_ENV=local/', 'APP_ENV=production', $env);
    $env = preg_replace('/APP_DEBUG=true/', 'APP_DEBUG=false', $env);
    file_put_contents('.env', $env);
    echo "✅ .env fixed!";
}