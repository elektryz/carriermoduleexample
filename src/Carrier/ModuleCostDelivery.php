<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Carrier;

use inIT\CarrierModuleExample\Carrier\Base\BaseCarrier;
use inIT\CarrierModuleExample\Carrier\Interfaces\CarrierInterface;

/**
 * This carrier will return 10% of the cart total as a shipping cost.
 */
class ModuleCostDelivery extends BaseCarrier implements CarrierInterface
{
    public function calculate(): float|false
    {
        return $this->cartTotal * 0.1;
    }
}
