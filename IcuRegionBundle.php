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
use Symfony\Component\Intl\ResourceBundle\Reader\StructuredBundleReaderInterface;
use Symfony\Component\Intl\ResourceBundle\RegionBundle;
use Symfony\Component\Intl\Util\IcuVersion;

/**
 * An ICU-specific implementation of {@link \Symfony\Component\Intl\ResourceBundle\RegionBundleInterface}.
 *
 * This class normalizes the data of the ICU .res files to satisfy the contract
 * defined in {@link \Symfony\Component\Intl\ResourceBundle\RegionBundleInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class IcuRegionBundle extends RegionBundle
{
    private $version;

    public function __construct(StructuredBundleReaderInterface $reader)
    {
        parent::__construct(realpath(IcuData::getResourceDirectory() . '/region'), $reader);

        $this->version = Intl::getIcuVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        if (IcuVersion::compare($this->version, '4.0', '<', $precision = 1)) {
            return array('en');
        }

        return $this->readEntry('misc', array('Locales'));
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryName($country, $locale = null)
    {
        if (IcuVersion::compare($this->version, '4.0', '<', $precision = 1)) {
            return parent::getCountryName($country, $locale);
        }

        if ('ZZ' === $country || ctype_digit((string) $country)) {
            return null;
        }

        return parent::getCountryName($country, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryNames($locale = null)
    {
        if (IcuVersion::compare($this->version, '4.0', '<', $precision = 1)) {
            return parent::getCountryNames($locale);
        }

        if (null === $locale) {
            $locale = \Locale::getDefault();
        }

        $countries = parent::getCountryNames($locale);

        // "ZZ" is the code for unknown country
        unset($countries['ZZ']);

        // Global countries (f.i. "America") have numeric codes
        // Countries have alphabetic codes
        foreach ($countries as $code => $name) {
            // is_int() does not work, since some numbers start with '0' and
            // thus are stored as strings.
            // The (string) cast is necessary since ctype_digit() returns false
            // for integers.
            if (ctype_digit((string) $code)) {
                unset($countries[$code]);
            }
        }

        $collator = new \Collator($locale);
        $collator->asort($countries);

        return $countries;
    }
}
