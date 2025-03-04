<?php

$directory = '.';

if (is_dir($directory)) {
    $files = scandir($directory);
    echo "<h1>Daftar File dan Directory di '$directory':</h1>";
    echo "<ul>";
    foreach ($files as $file) {
        echo "<li>$file</li>";
    }
    echo "</ul>";
}else{
    echo 'Directory not found';
}

?>