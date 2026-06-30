<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use FFPI\FfpiNodecounter\Controller\NodeController;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;

defined('TYPO3') || die('Access denied.');

ExtensionUtility::configurePlugin(
    'FfpiNodecounter',
    'Counter',
    [
        NodeController::class => 'count,cachedCount,jsonCount',

    ],
    // non-cacheable actions
    [
        NodeController::class => 'count,jsonCount',

    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
ExtensionUtility::configurePlugin(
    'FfpiNodecounter',
    'JsonCounter',
    [
        NodeController::class => 'jsonCount',

    ],
    // non-cacheable actions
    [
        NodeController::class => 'jsonCount',

    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
ExtensionUtility::configurePlugin(
    'FfpiNodecounter',
    'CachedCounter',
    [
        NodeController::class => 'cachedCount,count,jsonCount',

    ],
    // non-cacheable actions
    [
        NodeController::class => 'count,jsonCount',

    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result']) || !is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result'] = [];
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_nodecounter_result']['backend'] = Typo3DatabaseBackend::class;
}
