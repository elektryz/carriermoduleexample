<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Configuration;

class ModuleConfiguration
{
    // - GENERAL -
    public const DEV_MODE = false;
    public const MODULE_NAME = 'carriermoduleexample';
    public const CONFIGURATION_CARRIER_INSERT_LOCK = 'CARRIER_MODULE_EXAMPLE_INSERT_LOCK';

    // - FIELDS -
    public const CARRIER_MODULE_ID_FIELD = 'carrier_module_id';
    public const CARRIER_REQUIRED_FIELDS = [
        'name',
        self::CARRIER_MODULE_ID_FIELD
    ];
    public const CARRIER_FORBIDDEN_FIELDS = [
        'delay',
        'id_carrier',
        'id_reference',
        'is_module',
        'external_module_name',
        'deleted',
        self::CARRIER_MODULE_ID_FIELD
    ];

    // - PATHS -
    public const INSTALL_SQL_FILE = _PS_MODULE_DIR_ . self::MODULE_NAME . '/sql/install.sql';
    public const CARRIER_YAML_DIRECTORY_PATH =  _PS_MODULE_DIR_ . self::MODULE_NAME . '/config/carrier';
    public const CARRIER_LOGO_DIRECTORY_PATH = _PS_MODULE_DIR_ . self::MODULE_NAME . '/config/logo';

    public static function getCarrierFilePath(string $version): string
    {
        $version = str_replace('.yml', '', $version);
        $version = str_replace('.', '_', $version);

        return self::CARRIER_YAML_DIRECTORY_PATH . '/' . $version . '.yml';
    }

    public static function getInstalledCarriers(bool $ignoreDeleted = false): array
    {
        $column = 'id_carrier';
        $deletedText = $ignoreDeleted ? '' : ' AND deleted = 0';

        $query = \Db::getInstance()->executeS(
            'SELECT `' . $column . '` FROM `' . _DB_PREFIX_ . 'carrier` c
            WHERE `external_module_name` = "' . pSQL(self::MODULE_NAME) . '"' . $deletedText
        );

        if (count($query) > 0) {
            return array_column($query, $column);
        }

        return [];
    }
}