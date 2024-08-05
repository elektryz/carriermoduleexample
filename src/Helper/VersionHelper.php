<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Helper;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;

class VersionHelper
{
    public static function getVersionsToApply(\carriermoduleexample $module, float $installedVersion): array
    {
        $ymlFiles = array_diff(scandir(ModuleConfiguration::CARRIER_YAML_DIRECTORY_PATH), ['..', '.']);

        if (empty($ymlFiles)) {
            throw new \Exception('No carrier files found.');
        }

        $result = array_map(function ($item) {
            return str_replace('.yml', '', str_replace('_', '.', $item));
        }, $ymlFiles);

        $versions = self::findVersionsToApply($result, $installedVersion, (float)$module->version);

        if (empty($versions)) {
            return [];
        }

        return $versions;
    }

    private static function findVersionsToApply(array $numbers, float $lowerLimit, float $upperLimit): array
    {
        return array_filter($numbers, function ($number) use ($lowerLimit, $upperLimit) {
            return (float)$number > $lowerLimit && (float)$number <= $upperLimit;
        });
    }

    public static function getInstalledVersion($module): float
    {
        return (float)\Db::getInstance()->getValue(
            'SELECT `version` 
            FROM `' . _DB_PREFIX_ . 'module` 
            WHERE `name` = "' . pSQL($module->name) . '"'
        );
    }
}