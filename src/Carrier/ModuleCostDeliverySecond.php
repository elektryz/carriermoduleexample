<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Carrier;

use inIT\CarrierModuleExample\Carrier\Base\BaseCarrier;
use inIT\CarrierModuleExample\Carrier\Interfaces\CarrierInterface;

/**
 * This carrier will always return 14.99 as a shipping cost.
 */
class ModuleCostDeliverySecond extends BaseCarrier implements CarrierInterface
{
    public function calculate(): float|false
    {
        return 14.99;
    }
}
