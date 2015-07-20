<?php

$output = null;
$ret = exec('git diff --name-only master...HEAD', $output);
var_dump($output);

?>