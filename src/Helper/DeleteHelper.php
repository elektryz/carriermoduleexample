<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Helper;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;

class DeleteHelper
{
    /**
     * @param array $carriers - array of all carriers IDs without 'id_carrier' key, like [1, 2, 3, ...]
     * @return array
     */
    public function getRemovableCarriers(array $carriers): array
    {
        return array_diff($carriers, self::getNonRemovableCarriers());
    }

    private static function getNonRemovableCarriers(): array
    {
        $ids = ModuleConfiguration::getInstalledCarriers(true);
        $nonRemovableCarriers = [];

        if (empty($ids)) {
            return [];
        }

        foreach ($ids as $item) {
            if (self::checkOccurrence('cart', (int)$item) ||
                self::checkOccurrence('orders', (int)$item) ||
                self::checkOccurrence('order_carrier', (int)$item)
            ) {
                if (!in_array($item, $nonRemovableCarriers)) {
                    $nonRemovableCarriers[] = $item;
                }
            }
        }

        return $nonRemovableCarriers;
    }

    private static function checkOccurrence(string $table, int $idCarrier): bool
    {
        return (bool)\Db::getInstance()->getValue(
            'SELECT count(*) FROM ' . _DB_PREFIX_ . $table . ' WHERE id_carrier=' . $idCarrier
        );
    }
}