<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'FfpiNodecounter',
    'Counter',
    'counter',
    null,
    'list'
);

ExtensionUtility::registerPlugin(
    'FfpiNodecounter',
    'CachedCounter',
    'counter (cached)',
    null,
    'list'
);
