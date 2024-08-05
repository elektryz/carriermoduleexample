CREATE TABLE IF NOT EXISTS `PREFIX_carrier_module_id` (
    `id_carrier_module_id` INT unsigned NOT NULL AUTO_INCREMENT,
    `carrier_module_id` VARCHAR(128) NOT NULL,
    `id_reference` INT unsigned NOT NULL,
    PRIMARY KEY (`id_carrier_module_id`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;