<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Factory;

use inIT\CarrierModuleExample\Entity\CarrierModuleId;
use inIT\CarrierModuleExample\Helper\TextHelper;

class CarrierFactory
{
    private int $idCarrier;
    private \Cart $cart;

    public function __construct(\Cart $cart, int $idCarrier)
    {
        $this->cart = $cart;
        $this->idCarrier = $idCarrier;
    }

    public function calculate(): float|false
    {
        $carrier = new \Carrier($this->idCarrier);

        if (!\Validate::isLoadedObject($carrier) || !\Validate::isLoadedObject($this->cart)) {
            return false;
        }

        if (!$carrier->shipping_external) {
            return false;
        }

        $carrierModule = CarrierModuleId::getCarrierModuleIdByReference((int)$carrier->id_reference);

        if (strlen(trim($carrierModule)) == 0) {
            return false;
        }

        $className = TextHelper::convertToPascalCase($carrierModule);
        $fcqn = 'inIT\\CarrierModuleExample\\Carrier\\' . $className;

        if (!class_exists($fcqn)) {
            return false;
        }

        $object = new $fcqn($this->cart);

        if (!$object) {
            return false;
        }

        if (!method_exists($object, 'calculate')) {
            return false;
        }

        return $object->calculate();
    }
}