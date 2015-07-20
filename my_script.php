<?php

$change_files = null;
$ret = exec('git diff --name-only master...HEAD', $change_files);

foreach($change_files as $file) {
    $path = realpath($file);
    $cmd = "php .\PHP_CodeSniffer\scripts\phpcs ${path}";
    $ret = exec($cmd, $output);
    var_dump($output);
    $output = null;
}

?>