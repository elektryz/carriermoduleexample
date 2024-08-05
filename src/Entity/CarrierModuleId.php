<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Entity;

class CarrierModuleId extends \ObjectModel
{
    public string $carrier_module_id;
    public int $id_reference;

    public static $definition = [
        'table' => 'carrier_module_id',
        'primary' => 'id_carrier_module_id',
        'fields' => [
            'carrier_module_id' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'id_reference' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
        ],
    ];

    public static function getInstanceByModuleId(string $moduleId): CarrierModuleId
    {
        $value = (int)\Db::getInstance()->getValue(
            'SELECT id_carrier_module_id 
            FROM ' . _DB_PREFIX_ . 'carrier_module_id 
            WHERE carrier_module_id = \'' . pSQL($moduleId) . '\''
        );

        return $value > 0 ? new self($value) : new self();
    }

    public static function getCarrierModuleIdByReference(int $reference): string
    {
        return (string)\Db::getInstance()->getValue(
            'SELECT carrier_module_id 
            FROM ' . _DB_PREFIX_ . 'carrier_module_id 
            WHERE id_reference = ' . $reference
        );
    }
}