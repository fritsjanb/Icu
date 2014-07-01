<?php

namespace Symfony\Component\Icu;

use ReflectionException;
use ReflectionExtension;

class IcuVersion
{
    public static function getVersion()
    {
        static $version;

        if ($version) {
            return $version;
        }

        try {
            $info = new ReflectionExtension('intl');
            $constants = $info->getConstants();

            $version = (float) $constants['INTL_ICU_VERSION'];

            return $version;
        } catch(ReflectionException $e) {
            return null;
        }
    }
}
