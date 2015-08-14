<?php

class PHP5_Migration_Sniffs_TraceObjectVariant_SearchAllowSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'PHP',
    );

    /**
     * Show debug output for this sniff.
     *
     * @var bool
     */
    private $_debug = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        if (defined('PHP_CODESNIFFER_IN_TESTS') === true) {
            $this->_debug = false;
        }

        return array(T_OBJECT_OPERATOR);
    }

    private function assignObjectOnLine($tokens, $startPtr)
    {
        // 期待しているトークンの並びを列挙
        $expectedTypes = array(
            array('T_EQUAL', 'T_NEW', 'T_STRING'),
            array('T_AND_EQUAL', 'T_NEW', 'T_STRING'),
            array('T_EQUAL', 'T_BITWISE_AND', 'T_NEW', 'T_STRING'),
        );
        reset($expectedTypes);

        foreach ($expectedTypes as $expectedType) {
            // 対象トークン～セミコロンまでのトークンを検査し、期待しているトークンの並びになっているか確認する
            for ($i = ($startPtr + 1); $tokens[$i]['type'] != 'T_SEMICOLON'; $i++) {
                // スペース文字は無視する
                if ($tokens[$i]['type'] === 'T_WHITESPACE') {
                    continue;
                } else if ($tokens[$i]['type'] == current($expectedType)) {
                    if (next($expectedType) === false) {
                        // 期待しているトークンのarrayの末尾まで達した＝期待したトークンの並びであったとしてtrueを返す
                        return true;
                    }
                } else {
                    break;
                }
            }
        }

        return false;
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens         = $phpcsFile->getTokens();
        $arrow_token    = $tokens[$stackPtr];
        $check_variable = $tokens[$stackPtr - 1]['content'];

        // 検査対象のトークンが'$this'であり、かつClass内部のスコープであれば問題ないので無視する
        if ($check_variable == '$this') {
            $conditions = $arrow_token['conditions'];

            // conditions末尾からループを回し、'T_CLASS'がないか確認する
            if (end($conditions) !== false) {
                for (; prev($conditions) !== false;) {
                    $conditionsOwnerPtr = key($conditions);
                    if ($tokens[$conditionsOwnerPtr]['type'] == 'T_CLASS') {
                        return;
                    }
                }
            }
        }

        // ローカルスコープ内部であれば、所属しているローカルスコープのownerトークンからスコープ範囲を取得し、検査範囲に指定する
        if (end($arrow_token['conditions']) !== false) {
            $ownerPtr = key($arrow_token['conditions']);

            if (isset($tokens[$ownerPtr]['scope_opener']) && isset($tokens[$ownerPtr]['scope_closer'])) {
                $start = $tokens[$ownerPtr]['scope_opener'];
                //$end = $tokens[$ownerPtr]['scope_closer'];
                $end = $stackPtr;
            }
            else {
                // ownerトークンからスコープ範囲が取得できないので、エラー
                return false;
            }
        }
        // グローバルスコープ内部であれば、検査範囲を全体に指定する
        else {
            $start = 0;
            //$end = $phpcsFile->numTokens;
            $end = $stackPtr;
        }

        // 検査範囲を検査する
        for ($i = $start; $i < $end; $i++) {
            // 途中で別スコープのトークンが出てきても無視する
            if ($arrow_token['conditions'] != $tokens[$i]['conditions']) {
                continue;
            }

            // 検査対象と同名のトークンを発見したら、new演算子による初期化が行われているか確認する
            if ($check_variable == $tokens[$i]['content']) {
                if ($this->assignObjectOnLine($tokens, $i) === true) {
                    // 同一スコープ内でnew演算子による初期化が行われていれば問題ないため検査を終了する
                    return;
                }
            }
        }

        // new演算子による初期化が行われていないobject型の変数を発見したとして、警報をあげる
        $warning = "${check_variable} is not initialized. Object type variable is assigned by reference over PHP5.0.x.";
        $phpcsFile->addWarning($warning, ($stackPtr - 1), 'NotInitializedObject');
    }
}
