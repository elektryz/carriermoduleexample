<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Parser;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;
use inIT\CarrierModuleExample\Helper\VersionHelper;
use Symfony\Component\Yaml\Yaml;

class CarrierYamlParser
{
    private \carriermoduleexample $module;
    private float $installedVersion;

    public function __construct(\carriermoduleexample $module, float $installedVersion)
    {
        $this->module = $module;
        $this->installedVersion = $installedVersion;
    }

    public function parse(): array
    {
        $versions = VersionHelper::getVersionsToApply($this->module, $this->installedVersion);

        if (empty($versions)) {
            return [];
        }

        $data = [];

        foreach ($versions as $version) {
            $filePath = ModuleConfiguration::getCarrierFilePath($version);
            if (file_exists($filePath)) {
                $data[] = Yaml::parseFile($filePath);
            }
        }

        return $data;
    }
}