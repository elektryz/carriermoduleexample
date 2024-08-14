<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use inIT\CarrierModuleExample\Factory\CarrierFactory;
use inIT\CarrierModuleExample\Helper\VersionHelper;
use inIT\CarrierModuleExample\Installer\Install;
use inIT\CarrierModuleExample\Presenter\ConfigurationPagePresenter;

class carriermoduleexample extends CarrierModule
{
    // This property is set by PrestaShop - it's helpful when you want to know which carrier is currently used,
    // especially if you have multiple carriers in one module
    public $id_carrier;
    public array $shippingCosts = [];

    public function __construct()
    {
        $this->name = 'carriermoduleexample';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'inIT Kamil Góralczyk';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans(
            'Carrier module example',
            [],
            'Modules.Carriermoduleexample.Admin'
        );

        $this->description = $this->trans(
            'Help developers to understand how to create carrier module.',
            [],
            'Modules.Carriermoduleexample.Admin'
        );

        $this->ps_versions_compliancy = ['min' => '8.1.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        $installedVersion = VersionHelper::getInstalledVersion($this);
        return parent::install()
            // && $this->registerHook(self::HOOKS)
            && (new Install($this, $installedVersion))->install();
    }

    public function uninstall()
    {
        $installedVersion = VersionHelper::getInstalledVersion($this);
        return parent::uninstall()
            && (new Install($this, $installedVersion))->uninstall();
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function getContent()
    {
        return (new ConfigurationPagePresenter($this))->present();
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        if (!isset($this->shippingCosts[$this->id_carrier])) {
            $this->shippingCosts[$this->id_carrier] = (new CarrierFactory($params, $this->id_carrier))->calculate();
        }

        return $this->shippingCosts[$this->id_carrier] ?? false;
    }

    public function getOrderShippingCostExternal($params)
    {
        return $this->getOrderShippingCost($params, null);
    }

    /* This method will override "getOrderShippingCost"
    public function getPackageShippingCost($cart, $shipping_cost, $products)
    {
        return $this->getOrderShippingCost($cart, $shipping_cost);
    }
    */
}