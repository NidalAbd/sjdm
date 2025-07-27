<?php

// Storage Setup Script for Shared Hosting
// Run this script to create storage directories in public_html

echo "Setting up storage directories...\n";

// Define paths
$publicHtmlPath = __DIR__ . '/../public_html';
$storagePath = $publicHtmlPath . '/storage';
$appPublicPath = $storagePath . '/app/public';
$paymentMethodsPath = $appPublicPath . '/payment-methods';
$imagesPath = $publicHtmlPath . '/images/payment-methods';

// Create directories
$directories = [
    $storagePath,
    $storagePath . '/framework/cache',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/testing',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
    $appPublicPath,
    $paymentMethodsPath,
    $imagesPath,
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "Created directory: $dir\n";
        } else {
            echo "Failed to create directory: $dir\n";
        }
    } else {
        echo "Directory already exists: $dir\n";
    }
}

// Set permissions
echo "Setting permissions...\n";
system("chmod -R 755 $storagePath");
system("chmod -R 777 $paymentMethodsPath");
system("chmod -R 755 $imagesPath");

// Create default logo
$defaultLogo = $imagesPath . '/default.svg';
if (!file_exists($defaultLogo)) {
    $svgContent = '<svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="64" height="64" rx="8" fill="#f8f9fa"/>
  <rect x="12" y="20" width="40" height="24" rx="4" fill="#6c757d"/>
  <rect x="16" y="24" width="32" height="16" rx="2" fill="#ffffff"/>
  <rect x="20" y="28" width="8" height="8" rx="2" fill="#6c757d"/>
  <rect x="32" y="28" width="12" height="8" rx="2" fill="#6c757d"/>
  <rect x="20" y="40" width="24" height="2" rx="1" fill="#6c757d"/>
</svg>';
    
    if (file_put_contents($defaultLogo, $svgContent)) {
        echo "Created default logo: $defaultLogo\n";
    } else {
        echo "Failed to create default logo: $defaultLogo\n";
    }
} else {
    echo "Default logo already exists: $defaultLogo\n";
}

// Create test file
$testFile = $paymentMethodsPath . '/test.txt';
if (!file_exists($testFile)) {
    if (file_put_contents($testFile, 'test')) {
        echo "Created test file: $testFile\n";
    } else {
        echo "Failed to create test file: $testFile\n";
    }
} else {
    echo "Test file already exists: $testFile\n";
}

echo "\nStorage setup completed!\n";
echo "Test URL: " . (isset($_SERVER['HTTP_HOST']) ? "https://{$_SERVER['HTTP_HOST']}/storage/app/public/payment-methods/test.txt" : "yourdomain.com/storage/app/public/payment-methods/test.txt") . "\n"; 