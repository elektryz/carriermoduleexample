<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Handler;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;
use inIT\CarrierModuleExample\Entity\CarrierModuleId;
use inIT\CarrierModuleExample\Parser\CarrierYamlParser;

class InsertCarrier
{
    private \carriermoduleexample $module;
    private float $installedVersion;

    public function __construct(\carriermoduleexample $module, float $installedVersion)
    {
        $this->module = $module;
        $this->installedVersion = $installedVersion;
    }

    public function handle(): bool
    {
        $carrierYamlParser = new CarrierYamlParser($this->module, $this->installedVersion);
        $data = $carrierYamlParser->parse();

        if (empty($data)) {
            return true;
        }

        $result = true;

        foreach ($data as $item) {
            if (!isset($item['carriers'])) {
                throw new \Exception('Carrier file does not have valid structure.');
            }

            foreach ($item['carriers'] as $carrier) {
                $result &= (bool)$this->insertCarrier($carrier);
            }
        }

        return (bool)$result;
    }

    private function insertCarrier(array $carrierArray): bool
    {
        $requiredFields = ModuleConfiguration::CARRIER_REQUIRED_FIELDS;

        foreach ($requiredFields as $requiredField) {
            if (!isset($carrierArray[$requiredField])) {
                throw new \Exception('Carrier defined in carriers.yml does not have required field: ' . $requiredField);
            }
        }

        $carrierModuleId = (string)$carrierArray['carrier_module_id'];

        $carrier = self::getCarrierInstance($carrierModuleId);
        $carrier->is_module = true;
        $carrier->active = true;
        $carrier->deleted = false;
        $carrier->external_module_name = $this->module->name;
        $carrier->need_range = true; // If using module carrier this is required to be true

        foreach (\Language::getLanguages(false) as $item) {
            $carrier->delay[$item['id_lang']] = '...';
        }

        foreach ($carrierArray as $property => $value) {
            if (!in_array($property, ModuleConfiguration::CARRIER_FORBIDDEN_FIELDS)) {
                if (property_exists($carrier, $property)) {
                    $carrier->{$property} = $value;
                }
            }
        }

        $carrierIdFound = $carrier->id;
        $result = $carrier->save();

        if (\Validate::isLoadedObject($carrier) && !$carrierIdFound) {
            $this->insertCarrierModuleId($carrier, $carrierModuleId);
            $this->insertGroups($carrier);
            $this->insertRanges($carrier);
            $this->insertZones($carrier);
            $this->insertLogo($carrier, $carrierModuleId);
        }

        return $result;
    }

    private static function getCarrierInstance(string $carrierModuleId): \Carrier
    {
        $carrierModule = CarrierModuleId::getInstanceByModuleId($carrierModuleId);

        if (\Validate::isLoadedObject($carrierModule)) {
            $carrier = \Carrier::getCarrierByReference($carrierModule->id_reference);

            if (\Validate::isLoadedObject($carrier)) {
                return $carrier;
            }
        }

        return new \Carrier();
    }

    private function insertCarrierModuleId(\Carrier $carrier, string $carrierModuleId): void
    {
        $reference = $carrier->id_reference ?? $carrier->id;

        $carrierModule = CarrierModuleId::getInstanceByModuleId($carrierModuleId);
        $carrierModule->carrier_module_id = $carrierModuleId;
        $carrierModule->id_reference = (int)$reference;
        $carrierModule->save();
    }

    private function insertGroups(\Carrier $carrier): void
    {
        $groups_ids = array();
        $groups = \Group::getGroups(\Context::getContext()->language->id);
        foreach ($groups as $group) {
            $groups_ids[] = $group['id_group'];
        }

        $carrier->setGroups($groups_ids);
    }

    private function insertRanges(\Carrier $carrier): void
    {
        $range_price = new \RangePrice();
        $range_price->id_carrier = $carrier->id;
        $range_price->delimiter1 = '0';
        $range_price->delimiter2 = '99999';
        $range_price->add();

        $range_weight = new \RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '0';
        $range_weight->delimiter2 = '99999';
        $range_weight->add();
    }

    private function insertZones(\Carrier $carrier): void
    {
        $zones = \Zone::getZones();

        foreach ($zones as $zone) {
            $carrier->addZone($zone['id_zone']);
        }
    }

    private function insertLogo(\Carrier $carrier, string $carrierModuleId): void
    {
        if (file_exists(ModuleConfiguration::CARRIER_LOGO_DIRECTORY_PATH . '/' . $carrierModuleId . '.jpg')) {
            $logoPath = ModuleConfiguration::CARRIER_LOGO_DIRECTORY_PATH . '/' . $carrierModuleId . '.jpg';
        } elseif (file_exists(ModuleConfiguration::CARRIER_LOGO_DIRECTORY_PATH . '/' . $carrierModuleId . '.png')) {
            $logoPath = ModuleConfiguration::CARRIER_LOGO_DIRECTORY_PATH . '/' . $carrierModuleId . '.png';
        }

        if (isset($logoPath)) {
            copy($logoPath, _PS_SHIP_IMG_DIR_ . (int)$carrier->id . '.jpg');

            foreach (\Language::getLanguages(false) as $lang) {
                copy($logoPath, _PS_TMP_IMG_DIR_ . 'carrier_mini_' . (int)$carrier->id . '_' . $lang['id_lang'] . '.png');
            }
        }
    }
}