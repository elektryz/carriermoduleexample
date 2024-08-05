<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Carrier\Base;

class BaseCarrier
{
    protected \Cart $cart;
    protected float $cartTotal;

    public function __construct(\Cart $cart)
    {
        $this->cart = $cart;
        $this->cartTotal = (float)$cart->getOrderTotal(true, \CartCore::ONLY_PRODUCTS);
    }
}