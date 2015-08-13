<?php
$exit_val = 0;
$script_path = '.\PHP_CodeSniffer\scripts';
$ruleset_path = '.\config/PHP_CodeSniffer/ruleset.xml';
$encoding = 'utf-8';
$exec_extensions = array('php');

$script = 'phpcs';
if ($argv[1] == 1) {
    $script = 'phpcbf';
}

// masterブランチから変更されたファイルのみを抽出する
$change_files = null;
$ret = exec('git diff --name-only master...HEAD', $change_files);

foreach ($change_files as $file) {
    $path = $file;
    $path_info = pathinfo($file);

    // 処理対象のextensionでなければ無視する
    if (!in_array($path_info['extension'], $exec_extensions)) {
        continue;
    }

    // phpcsの実行
    $cmd_cs = "php ${script_path}\\${script} --standard=${ruleset_path} --encoding=${encoding} ${path}";
    passthru($cmd_cs, $ret);
    $exit_val |= $ret;
    
    // 文法チェックの実行
    $cmd_syntax_check = "php -l ${path}";
    passthru($cmd_syntax_check, $ret);
    $exit_val |= $ret;
}

return($exit_val);
