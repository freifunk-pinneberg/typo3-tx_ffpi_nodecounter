<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'FFPI.' . $_EXTKEY,
	'Counter',
	'counter'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Nodecounter');

#\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ffpinodecounter_domain_model_node', 'EXT:ffpi_nodecounter/Resources/Private/Language/locallang_csh_tx_ffpinodecounter_domain_model_node.xlf');
#\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ffpinodecounter_domain_model_node');
