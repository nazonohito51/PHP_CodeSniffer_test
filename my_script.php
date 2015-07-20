<?php

$change_files = null;
$ret = exec('git diff --name-only master...HEAD', $change_files);

foreach($change_files as $file) {
    //$path = realpath($file);
    $path = $file;
    $cmd = "php .\PHP_CodeSniffer\scripts\phpcs --standard=PSR2 --encoding=utf-8 --extensions=php ${path}";
    $ret = exec($cmd, $output, $ret);
    var_dump($output);
    //var_dump($ret);
    $output = null;
}

?>
