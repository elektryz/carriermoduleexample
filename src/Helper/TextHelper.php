<?php

declare(strict_types=1);

namespace inIT\CarrierModuleExample\Helper;

class TextHelper
{
    public static function convertToPascalCase(string $string): string
    {
        $string = strtolower($string);
        $string = str_replace('-', '_', $string);

        return ucfirst(str_replace('_', '', ucwords($string, '_')));
    }
}