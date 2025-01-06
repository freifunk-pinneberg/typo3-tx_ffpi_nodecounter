<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FfpiNodecounter',
    'Counter',
    [
        \FFPI\FfpiNodecounter\Controller\NodeController::class => 'count,cachedCount,jsonCount',

    ],
    // non-cacheable actions
    [
        \FFPI\FfpiNodecounter\Controller\NodeController::class => 'count,jsonCount',

    ]
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FfpiNodecounter',
    'JsonCounter',
    [
        \FFPI\FfpiNodecounter\Controller\NodeController::class => 'jsonCount',

    ],
    // non-cacheable actions
    [
        \FFPI\FfpiNodecounter\Controller\NodeController::class => 'jsonCount',

    ]
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FfpiNodecounter',
    'CachedCounter',
    [
        \FFPI\FfpiNodecounter\Controller\NodeController::class => 'cachedCount,count,jsonCount',

    ],
    // non-cacheable actions
    [
        \FFPI\FfpiNodecounter\Controller\NodeController::class => 'count,jsonCount',

    ]
);

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result'] = [];
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result']['backend'] = \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class;
}
