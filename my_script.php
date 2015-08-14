<?php
$exit_val = 0;
$script_path = '.\PHP_CodeSniffer\scripts';
$ruleset_path = '.\config\PHP_CodeSniffer\PSR2_Custom\ruleset.xml';
$encoding = 'utf-8';
$exec_extensions = array('php');
$php5_migration_ruleset_path = '.\config\PHP_CodeSniffer\PHP5_Migration\ruleset.xml';

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

    // PSR2_Customのチェック
    $cmd_psr2_cs = "php ${script_path}\\${script} --standard=${ruleset_path} --encoding=${encoding} ${path}";var_dump($cmd_psr2_cs);
    passthru($cmd_psr2_cs, $ret);
    $exit_val |= $ret;

    // PHP5_Migrationのチェック
    $cmd_php5_cs = "php ${script_path}\\${script} --standard=${php5_migration_ruleset_path} --encoding=${encoding} ${path}";var_dump($cmd_php5_cs);
    passthru($cmd_php5_cs, $ret);
    $exit_val |= $ret;
    
    // 文法チェックの実行
    $cmd_syntax_check = "php -l ${path}";
    passthru($cmd_syntax_check, $ret);
    $exit_val |= $ret;
}

return($exit_val);
