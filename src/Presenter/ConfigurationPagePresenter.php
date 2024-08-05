<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Presenter;

use inIT\CarrierModuleExample\Configuration\ModuleConfiguration;
use inIT\CarrierModuleExample\Handler\DeleteCarrier;

class ConfigurationPagePresenter
{
    private \carriermoduleexample $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function present(): string
    {
        $isDevMode = ModuleConfiguration::DEV_MODE;
        $twigVariables = [
            'module_carriers' => $this->module->get(CarrierPresenter::class)->present(),
            'dev_mode' => $isDevMode,
            'delete_url' => \Context::getContext()->link->getAdminLink(
                'AdminModules', true, [], ['configure' => $this->module->name, 'deletecarriers' => 1]
            ),
            'delete_all_url' => \Context::getContext()->link->getAdminLink(
                'AdminModules', true, [], ['configure' => $this->module->name, 'deleteallcarriers' => 1]
            ),
        ];

        if (\Tools::getIsset('deletecarriers') || \Tools::getIsset('deleteallcarriers')) {
            $delete = $this->module->get(DeleteCarrier::class);
            $delete->handle((bool)\Tools::getIsset('deleteallcarriers'));

            \Tools::redirectAdmin(
                \Context::getContext()->link->getAdminLink(
                    'AdminModules', true, [], ['configure' => $this->module->name]
                )
            );
        }

        $twig = $this->module->get('twig');

        return $twig->render(
            '@Modules/'.$this->module->name.'/views/templates/admin/configuration.html.twig', $twigVariables
        );
    }
}