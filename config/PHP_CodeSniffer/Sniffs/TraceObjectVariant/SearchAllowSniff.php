<?php

class PHP_CodeSniffer_Sniffs_TraceObjectVariant_SearchAllowSniff implements PHP_CodeSniffer_Sniff
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
        // ���҂��Ă���g�[�N���̕��т��
        $expectedTypes = array(
            array('T_EQUAL', 'T_NEW', 'T_STRING'),
            array('T_AND_EQUAL', 'T_NEW', 'T_STRING'),
            array('T_EQUAL', 'T_BITWISE_AND', 'T_NEW', 'T_STRING'),
        );
        reset($expectedTypes);

        foreach ($expectedTypes as $expectedType) {
            // �Ώۃg�[�N���`�Z�~�R�����܂ł̃g�[�N�����������A���҂��Ă���g�[�N���̕��тɂȂ��Ă��邩�m�F����
            for ($i = ($startPtr + 1); $tokens[$i]['type'] != 'T_SEMICOLON'; $i++) {
                // �X�y�[�X�����͖�������
                if ($tokens[$i]['type'] === 'T_WHITESPACE') {
                    continue;
                } else if ($tokens[$i]['type'] == current($expectedType)) {
                    if (next($expectedType) === false) {
                        // ���҂��Ă���g�[�N����array�̖����܂ŒB���������҂����g�[�N���̕��тł������Ƃ���true��Ԃ�
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

        // �����Ώۂ̃g�[�N����'$this'�ł���A����Class�����̃X�R�[�v�ł���Ζ��Ȃ��̂Ŗ�������
        if ($check_variable == '$this') {
            $conditions = $arrow_token['conditions'];

            // conditions�������烋�[�v���񂵁A'T_CLASS'���Ȃ����m�F����
            if (end($conditions) !== false) {
                for (; prev($conditions) !== false;) {
                    $conditionsOwnerPtr = key($conditions);
                    if ($tokens[$conditionsOwnerPtr]['type'] == 'T_CLASS') {
                        return;
                    }
                }
            }
        }

        // ���[�J���X�R�[�v�����ł���΁A�������Ă��郍�[�J���X�R�[�v��owner�g�[�N������X�R�[�v�͈͂��擾���A�����͈͂Ɏw�肷��
        if (end($arrow_token['conditions']) !== false) {
            $ownerPtr = key($arrow_token['conditions']);

            if (isset($tokens[$ownerPtr]['scope_opener']) && isset($tokens[$ownerPtr]['scope_closer'])) {
                $start = $tokens[$ownerPtr]['scope_opener'];
                //$end = $tokens[$ownerPtr]['scope_closer'];
                $end = $stackPtr;
            }
            else {
                // owner�g�[�N������X�R�[�v�͈͂��擾�ł��Ȃ��̂ŁA�G���[
                return false;
            }
        }
        // �O���[�o���X�R�[�v�����ł���΁A�����͈͂�S�̂Ɏw�肷��
        else {
            $start = 0;
            //$end = $phpcsFile->numTokens;
            $end = $stackPtr;
        }

        // �����͈͂���������
        for ($i = $start; $i < $end; $i++) {
            // �r���ŕʃX�R�[�v�̃g�[�N�����o�Ă��Ă���������
            if ($arrow_token['conditions'] != $tokens[$i]['conditions']) {
                continue;
            }

            // �����ΏۂƓ����̃g�[�N���𔭌�������Anew���Z�q�ɂ�鏉�������s���Ă��邩�m�F����
            if ($check_variable == $tokens[$i]['content']) {
                if ($this->assignObjectOnLine($tokens, $i) === true) {
                    // ����X�R�[�v����new���Z�q�ɂ�鏉�������s���Ă���Ζ��Ȃ����ߌ������I������
                    return;
                }
            }
        }

        // new���Z�q�ɂ�鏉�������s���Ă��Ȃ�object�^�̕ϐ��𔭌������Ƃ��āA�x���������
        $warning = "${check_variable} is not initialized. Object type variable is assigned by reference over PHP5.0.x.";
        $phpcsFile->addWarning($warning, ($stackPtr - 1), 'NotInitializedObject');
    }
}
