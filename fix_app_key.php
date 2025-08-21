<?php
/**
 * Emergency script to fix missing APP_KEY error
 * Upload this to your server root (same level as public_html) and run it
 */

echo "=== Laravel APP_KEY Fix Script ===\n\n";

// Check if .env file exists
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    die("ERROR: .env file not found at $envPath\n");
}

echo "Found .env file at: $envPath\n";

// Read .env file
$envContent = file_get_contents($envPath);
$lines = explode("\n", $envContent);

// Check if APP_KEY exists and has a value
$appKeyFound = false;
$appKeyHasValue = false;
$newLines = [];

foreach ($lines as $line) {
    if (strpos($line, 'APP_KEY=') === 0) {
        $appKeyFound = true;
        $value = substr($line, 8); // Get everything after 'APP_KEY='
        if (!empty(trim($value)) && strpos($value, 'base64:') === 0) {
            $appKeyHasValue = true;
            $newLines[] = $line;
            echo "✓ APP_KEY already exists and has a valid value\n";
        } else {
            // Generate new key
            $key = 'base64:' . base64_encode(random_bytes(32));
            $newLines[] = "APP_KEY=$key";
            echo "✓ Generated new APP_KEY: $key\n";
        }
    } else {
        $newLines[] = $line;
    }
}

// If APP_KEY wasn't found at all, add it
if (!$appKeyFound) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $newLines[] = "APP_KEY=$key";
    echo "✓ Added new APP_KEY: $key\n";
}

// Write back to .env file
if (!$appKeyHasValue) {
    file_put_contents($envPath, implode("\n", $newLines));
    echo "\n✓ .env file updated successfully!\n";
    
    // Try to clear Laravel cache
    echo "\nClearing Laravel caches...\n";
    $commands = [
        'php artisan config:clear',
        'php artisan cache:clear',
        'php artisan config:cache'
    ];
    
    foreach ($commands as $command) {
        $output = shell_exec("cd " . __DIR__ . " && $command 2>&1");
        echo "  $command: " . ($output ?: "OK") . "\n";
    }
    
    echo "\n✅ APP_KEY has been set successfully!\n";
    echo "Your application should now work properly.\n";
} else {
    echo "\n✅ No changes needed - APP_KEY is already set correctly.\n";
}

echo "\n=== Script Complete ===\n";
echo "You can delete this file now for security.\n";
