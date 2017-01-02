<?php

namespace PSR2R\Sniffs\PHP;

use PHP_CodeSniffer_File;

/**
 * Always use simple casts instead of method invocation.
 *
 * @author Mark Scherer
 * @license MIT
 */
class PreferCastOverFunctionSniff extends \PSR2R\Tools\AbstractSniff {

	/**
	 * @var array
	 */
	protected static $matching = [
		'strval' => 'string',
		'intval' => 'int',
		'floatval' => 'float',
		'boolval' => 'bool',
	];

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_STRING];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$wrongTokens = [T_FUNCTION, T_OBJECT_OPERATOR, T_NEW, T_DOUBLE_COLON];

		$tokens = $phpcsFile->getTokens();

		$tokenContent = $tokens[$stackPtr]['content'];
		$key = strtolower($tokenContent);
		if (!isset(self::$matching[$key])) {
			return;
		}

		$previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if (!$previous || in_array($tokens[$previous]['code'], $wrongTokens)) {
			return;
		}

		$openingBraceIndex = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
		if (!$openingBraceIndex || $tokens[$openingBraceIndex]['type'] !== 'T_OPEN_PARENTHESIS') {
			return;
		}

		$closingBraceIndex = $tokens[$openingBraceIndex]['parenthesis_closer'];

		// We must ignore when commas are encountered
		if ($this->contains($phpcsFile, 'T_COMMA', $openingBraceIndex + 1, $closingBraceIndex - 1)) {
			return;
		}

		$error = $tokenContent . '() found, should be ' . self::$matching[$key] . ' cast.';

		$fix = $phpcsFile->addFixableError($error, $stackPtr);
		if ($fix) {
			$this->fixContent($phpcsFile, $stackPtr, $key, $openingBraceIndex, $closingBraceIndex);
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @param string $key
	 * @param int $openingBraceIndex
	 * @param int $closingBraceIndex
	 * @return void
	 */
	protected function fixContent(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $key, $openingBraceIndex, $closingBraceIndex) {
		$needsBrackets = $this->needsBrackets($phpcsFile, $openingBraceIndex, $closingBraceIndex);

		$tokens = $phpcsFile->getTokens();

		$cast = '(' . self::$matching[$key] . ')';

		$phpcsFile->fixer->replaceToken($stackPtr, $cast);
		if (!$needsBrackets) {
			$phpcsFile->fixer->replaceToken($openingBraceIndex, '');
			$phpcsFile->fixer->replaceToken($closingBraceIndex, '');
		}
	}

}
