<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
$directory = __DIR__ . DIRECTORY_SEPARATOR . $argv[1];

function getExtractedModuleDir($sourceModuleDir, $suffix)
{
    $uiModuleName = basename($sourceModuleDir) . $suffix;
    $uiModulePath = $sourceModuleDir . $suffix;
    if (!file_exists($uiModulePath)) {
        echo "Creating $uiModuleName in $uiModulePath\n";
        mkdir($uiModulePath);
        copy($sourceModuleDir . '/composer.json', $uiModulePath . '/composer.json');
        file_put_contents(
            $uiModulePath . '/composer.json',
            preg_replace('/name": "([^\"]*)"/', 'name": "$1-' . from_camel_case($suffix) . '"', file_get_contents($uiModulePath . '/composer.json'))
        );
        copy($sourceModuleDir . '/LICENSE.txt', $uiModulePath . '/LICENSE.txt');
        copy($sourceModuleDir . '/LICENSE_AFL.txt', $uiModulePath . '/LICENSE_AFL.txt');
    }
    return $uiModulePath;
}

function from_camel_case($input) {
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('-', $ret);
}

function extractModule($directory, $suffix, $extractedFiles, $ignoreModules)
{
    foreach (new DirectoryIterator($directory) as $moduleDir) {
        if ($moduleDir->isDir() && !$moduleDir->isDot()) {
            foreach ($ignoreModules as $pattern) {
                if (preg_match($pattern, $moduleDir->getFilename())) {
                    echo "duplication " . $moduleDir->getFilename() . " \n";
                    continue 2;
                }
            }
            echo "Reading " . $moduleDir->getPathname() . "\n";
            foreach ($extractedFiles as $extractedFile) {
                if (file_exists($moduleDir->getPathname() . DIRECTORY_SEPARATOR . $extractedFile)) {
                    $extractedModuleDir = getExtractedModuleDir($moduleDir->getPathname(), $suffix);
                    echo "Moving $extractedFile to $extractedModuleDir \n";
                    if (!file_exists(dirname($extractedModuleDir . DIRECTORY_SEPARATOR . $extractedFile))) {
                        mkdir(dirname($extractedModuleDir . DIRECTORY_SEPARATOR . $extractedFile), 0777, true);
                    }
                    rename($moduleDir->getPathname() . DIRECTORY_SEPARATOR . $extractedFile, $extractedModuleDir . DIRECTORY_SEPARATOR . $extractedFile);
                }
            }
        }
    }
}

//extractModule($directory, 'AdminUi', ['Block/Adminhtml', 'Controller/Adminhtml', 'view/adminhtml', 'etc/adminhtml'], ['/.*AdminUi$/', '/.*Ui$/']);
//extractModule($directory, 'Ui', ['Block', 'Controller', 'view', 'ViewModel', 'Ui', 'etc/frontend'], ['/.*AdminUi$/', '/.*Ui$/']);
extractModule($directory, 'Webapi', ['etc/webapi.xml', 'etc/webapi_rest', 'etc/webapi_soap'], ['/.*Webapi$/']);
