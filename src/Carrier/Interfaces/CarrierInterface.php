<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Carrier\Interfaces;

interface CarrierInterface
{
    /**
     * Return false if you want to disable the carrier for some reason (ex. API is not responding)
     * Otherwise, just return the float value with shipping cost for given carrier.
     */
    public function calculate(): float|false;
}