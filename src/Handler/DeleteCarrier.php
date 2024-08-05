<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Handler;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;
use inIT\CarrierModuleExample\Helper\DeleteHelper;

class DeleteCarrier
{
    private \carriermoduleexample $module;

    public function __construct(\carriermoduleexample $module)
    {
        $this->module = $module;
    }

    public function handle(bool $hardDelete = false): bool
    {
        if (!ModuleConfiguration::DEV_MODE) {
            return false;
        }

        $carrierIds = ModuleConfiguration::getInstalledCarriers($hardDelete);

        if (empty($carrierIds)) {
            return false;
        }

        $deleteHelper = $this->module->get(DeleteHelper::class);
        $carrierIdsRemovable = $deleteHelper->getRemovableCarriers($carrierIds);

        $referencesIdRemovable = [];

        if (empty($carrierIdsRemovable)) {
            return false;
        }

        foreach ($carrierIdsRemovable as $id) {
            $carrier = new \Carrier($id);
            $referencesIdRemovable[] = $carrier->id_reference;
        }

        $tables = [
            'carrier',
            'carrier_group',
            'carrier_lang',
            'carrier_shop',
            'carrier_zone',
            'cart_rule_carrier',
            'carrier_tax_rules_group_shop',
        ];

        foreach ($tables as $table) {
            \Db::getInstance()->execute(
                'DELETE FROM '._DB_PREFIX_.$table.' WHERE id_carrier IN ('.implode(',', $carrierIdsRemovable).')'
            );
        }

        \Db::getInstance()->execute(
            'DELETE FROM '._DB_PREFIX_.'module_carrier WHERE id_reference IN ('.implode(',', $referencesIdRemovable).')'
        );

        return true;
    }
}