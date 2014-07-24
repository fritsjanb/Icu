<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Icu;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\ResourceBundle\Reader\BinaryBundleReader;
use Symfony\Component\Intl\ResourceBundle\Reader\PhpBundleReader;
use Symfony\Component\Intl\Util\IcuVersion;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class IcuData
{
    /**
     * Returns the version of the bundled ICU data.
     *
     * @return string The version string.
     */
    public static function getVersion()
    {
        return trim(file_get_contents(__DIR__ . self::getResourceDirectory() . '/version.txt'));
    }

    /**
     * Returns whether the ICU data is stubbed.
     *
     * @return Boolean Returns true if the ICU data is stubbed, false if it is
     *         loaded from ICU .res files.
     */
    public static function isStubbed()
    {
        if (IcuVersion::compare(Intl::getIcuVersion(), '4.0', '<', $precision = 1)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the path to the directory where the resource bundles are stored.
     *
     * @return string The absolute path to the resource directory.
     */
    public static function getResourceDirectory()
    {
        if (IcuVersion::compare(Intl::getIcuVersion(), '4.0', '<', $precision = 1)) {
            return realpath(__DIR__ . '/Resources/1.0/data');
        }
        if (IcuVersion::compare(Intl::getIcuVersion(), '4.4', '<', $precision = 1)) {
            return realpath(__DIR__ . '/Resources/1.1/data');
        }

        return realpath(__DIR__ . '/Resources/1.2/data');
    }

    /**
     * Returns a reader for reading resource bundles in this component.
     *
     * @return \Symfony\Component\Intl\ResourceBundle\Reader\BundleReaderInterface
     */
    public static function getBundleReader()
    {
        if (IcuVersion::compare(Intl::getIcuVersion(), '4.0', '<', $precision = 1)) {
            return new PhpBundleReader();
        }

        return new BinaryBundleReader();
    }

    /**
     * This class must not be instantiated.
     */
    private function __construct() {}
}
