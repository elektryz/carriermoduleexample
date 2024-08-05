<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Presenter;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;
use inIT\CarrierModuleExample\Entity\CarrierModuleId;

class CarrierPresenter
{
    public function present(): array
    {
        $carriers = ModuleConfiguration::getInstalledCarriers(ModuleConfiguration::DEV_MODE);
        $data = [];

        if (empty($carriers)) {
            return [];
        }

        foreach ($carriers as $idCarrier) {
            $carrierObject = new \Carrier($idCarrier);

            if (!\Validate::isLoadedObject($carrierObject)) {
                continue;
            }

            $data[$idCarrier] = (array) $carrierObject;
            $data[$idCarrier]['carrier_module_id'] = CarrierModuleId::getCarrierModuleIdByReference(
                (int)$carrierObject->id_reference
            );
        }

        return $data;
    }
}
