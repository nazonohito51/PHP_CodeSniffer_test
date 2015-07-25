<?php
$extensions = array('php');

$change_files = null;
$ret = exec('git diff --name-only master...HEAD', $change_files);

foreach ($change_files as $file) {
    //$path = realpath($file);
    $path = $file;
    
    $pathinfo = pathinfo($file);
    if (!in_array($pathinfo['extension'], $extensions)) {
        continue;
    }
    
    $cmd = "php .\PHP_CodeSniffer\scripts\phpcs --standard=config/PHP_CodeSniffer/ruleset.xml --encoding=utf-8 --extensions=php ${path}";
    $ret = passthru($cmd, $ret);
    //var_dump($output);
    //var_dump($ret);
    $output = null;
}

?>
