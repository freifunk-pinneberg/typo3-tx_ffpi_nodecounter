<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'FFPI.' . $_EXTKEY,
	'Counter',
	array(
		'Node' => 'count',
		
	),
	// non-cacheable actions
	array(
		'Node' => '',
		
	)
);
