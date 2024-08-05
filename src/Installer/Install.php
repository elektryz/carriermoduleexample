<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Installer;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;
use inIT\CarrierModuleExample\Handler\InsertCarrier;

class Install
{
    private \carriermoduleexample $module;
    private float $installedVersion;

    public function __construct(\carriermoduleexample $module, float $installedVersion)
    {
        $this->module = $module;
        $this->installedVersion = $installedVersion;
    }

    public function uninstall(): bool
    {
        return \Db::getInstance()->update(
            'carrier',
            [
                'deleted' => 1,
            ],
            'external_module_name = "'.ModuleConfiguration::MODULE_NAME.'"'
        );
    }

    public function install()
    {
        return $this->installDatabase() && $this->installCarriers();
    }

    private function installCarriers(): bool
    {
        return (new InsertCarrier($this->module, $this->installedVersion))->handle();
    }

    private function installDatabase(): bool
    {
        if (!file_exists(ModuleConfiguration::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = file_get_contents(ModuleConfiguration::INSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace(
            ['PREFIX_', 'ENGINE_TYPE', 'MODULE_NAME'],
            [_DB_PREFIX_, _MYSQL_ENGINE_, ModuleConfiguration::MODULE_NAME],
            $sql
        );

        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

        foreach ($sql as $query) {
            if (!\Db::getInstance()->execute(trim($query))) {
                return false;
            }
        }

        return true;
    }
}