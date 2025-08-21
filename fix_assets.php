<?php
/**
 * Fix AdminLTE assets loading issue
 * Upload this to your server root (same level as public_html) and run it
 */

echo "=== AdminLTE Assets Fix Script ===\n\n";

$publicPath = __DIR__ . '/public_html';
$vendorPath = $publicPath . '/vendor';

echo "Checking asset paths...\n";
echo "Public path: $publicPath\n";
echo "Vendor path: $vendorPath\n\n";

// Check if vendor folder exists in public_html
if (!is_dir($vendorPath)) {
    echo "❌ Vendor folder not found in public_html!\n";
    
    // Check if vendor assets exist in public folder (old structure)
    $oldVendorPath = __DIR__ . '/public/vendor';
    if (is_dir($oldVendorPath)) {
        echo "Found vendor assets in old public folder\n";
        echo "Copying vendor assets to public_html...\n";
        
        // Create vendor directory in public_html
        mkdir($vendorPath, 0755, true);
        
        // Copy vendor assets
        $cmd = "cp -r $oldVendorPath/* $vendorPath/";
        shell_exec($cmd);
        
        echo "✓ Vendor assets copied to public_html/vendor\n";
    } else {
        echo "⚠️  No vendor assets found to copy\n";
        echo "You may need to run: php artisan vendor:publish --tag=adminlte-assets\n";
    }
} else {
    echo "✓ Vendor folder exists in public_html\n";
    
    // Check for AdminLTE specifically
    $adminltePath = $vendorPath . '/adminlte';
    if (is_dir($adminltePath)) {
        echo "✓ AdminLTE assets found\n";
        
        // List AdminLTE contents
        $adminlteFiles = scandir($adminltePath);
        echo "AdminLTE contents: " . implode(', ', array_slice($adminlteFiles, 2, 5)) . "...\n";
    } else {
        echo "❌ AdminLTE assets not found in vendor folder\n";
    }
}

// Check CSS and JS folders
$cssPath = $publicPath . '/css';
$jsPath = $publicPath . '/js';

echo "\nChecking other assets:\n";
echo (is_dir($cssPath) ? "✓" : "❌") . " CSS folder: $cssPath\n";
echo (is_dir($jsPath) ? "✓" : "❌") . " JS folder: $jsPath\n";

// Check if .htaccess is configured correctly
$htaccessPath = $publicPath . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "\n✓ .htaccess file exists\n";
} else {
    echo "\n❌ .htaccess file not found\n";
}

// Create a test HTML file to verify asset loading
$testFile = $publicPath . '/test_assets.html';
$testContent = '<!DOCTYPE html>
<html>
<head>
    <title>Asset Test</title>
    <link rel="stylesheet" href="/vendor/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/vendor/fontawesome-free/css/all.min.css">
</head>
<body>
    <h1>AdminLTE Asset Test</h1>
    <p>If this page has styling, AdminLTE CSS is loading correctly.</p>
    <div class="card">
        <div class="card-body">
            <i class="fas fa-check"></i> Test Card
        </div>
    </div>
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/vendor/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>';

file_put_contents($testFile, $testContent);
echo "\n✓ Created test file: https://smm-followerss.com/test_assets.html\n";

// Try to fix Laravel configuration
echo "\nAttempting to fix Laravel configuration...\n";

// Update public path in index.php if needed
$indexPath = $publicPath . '/index.php';
if (file_exists($indexPath)) {
    $indexContent = file_get_contents($indexPath);
    
    // Check if paths are correct
    if (strpos($indexContent, "require __DIR__.'/../vendor/autoload.php'") !== false) {
        echo "✓ index.php paths are correct\n";
    } else {
        echo "⚠️  index.php may have incorrect paths\n";
    }
}

// Clear Laravel caches
echo "\nClearing Laravel caches...\n";
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan route:clear'
];

foreach ($commands as $command) {
    $output = shell_exec("cd " . __DIR__ . " && $command 2>&1");
    echo "  $command: " . ($output ?: "OK") . "\n";
}

echo "\n=== Recommendations ===\n";
echo "1. Visit https://smm-followerss.com/test_assets.html to test if assets load\n";
echo "2. Check browser console for 404 errors on asset files\n";
echo "3. If assets still don't load, run: php artisan vendor:publish --tag=adminlte-assets\n";
echo "4. Ensure APP_URL in .env is set to: https://smm-followerss.com\n";

echo "\n=== Script Complete ===\n";
echo "You can delete this file now for security.\n";
