<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FFPI.' . $_EXTKEY,
    'Counter',
    [
        'Node' => 'count',

    ],
    // non-cacheable actions
    [
        'Node' => 'count',

    ]
);

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result'] = [];
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result']['backend'] = \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class;
}
