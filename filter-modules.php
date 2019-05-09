<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
$composerJsonPath = __DIR__ . DIRECTORY_SEPARATOR . $argv[1];

$rootComposerJsonContent = file_get_contents($composerJsonPath);
$rootComposerJsonData = json_decode($rootComposerJsonContent, true);

$addToRequire = [];
foreach ($rootComposerJsonData['replace'] as $moduleName => $version) {
    if (strpos($moduleName, '-admin-ui') !== false) {
        $addToRequire[$moduleName] = '*';
        unset($rootComposerJsonData['replace'][$moduleName]);
        unset($rootComposerJsonData['replace'][str_replace('-admin-ui', '', $moduleName)]);
    }
    if ((strpos($moduleName, '-ui') !== false && strpos($moduleName, '-admin-ui') === false)
        || strpos($moduleName, 'magento/') === 0) {
        unset($rootComposerJsonData['replace'][$moduleName]);
    }
}

$rootComposerJsonData['require'] = array_merge($rootComposerJsonData['require'], $addToRequire);

file_put_contents($composerJsonPath, json_encode($rootComposerJsonData, JSON_PRETTY_PRINT));
