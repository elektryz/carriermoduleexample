<?php

use inIT\CarrierModuleExample\Helper\VersionHelper;
use inIT\CarrierModuleExample\Installer\Install;

function upgrade_module_1_1_0($object)
{
    return (new Install($object, VersionHelper::getInstalledVersion($object)))->install();
}