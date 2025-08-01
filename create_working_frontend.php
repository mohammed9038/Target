<?php
// Create a working frontend file by copying from the local fixed version

$file = 'resources/views/targets/index.blade.php';

// First backup the current file
copy($file, $file . '.broken_backup');

// Read the working version from our local files
$workingContent = file_get_contents('/c%3A/Target/Target/resources/views/targets/index.blade.php');

// Write the working content to the server
file_put_contents($file, $workingContent);

echo "Working frontend file uploaded successfully!\n";
?>
