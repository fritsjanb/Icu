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
use Symfony\Component\Intl\ResourceBundle\LocaleBundle;
use Symfony\Component\Intl\ResourceBundle\Reader\StructuredBundleReaderInterface;
use Symfony\Component\Intl\Util\IcuVersion;

/**
 * An ICU-specific implementation of {@link \Symfony\Component\Intl\ResourceBundle\LocaleBundleInterface}.
 *
 * This class normalizes the data of the ICU .res files to satisfy the contract
 * defined in {@link \Symfony\Component\Intl\ResourceBundle\LocaleBundleInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class IcuLocaleBundle extends LocaleBundle
{
    private $version;

    public function __construct(StructuredBundleReaderInterface $reader)
    {
        parent::__construct(realpath(IcuData::getResourceDirectory() . '/locales'), $reader);

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
    public function getLocaleNames($locale = null)
    {
        if (IcuVersion::compare($this->version, '4.0', '<', $precision = 1)) {
            return parent::getLocaleNames($locale);
        }

        if (null === $locale) {
            $locale = \Locale::getDefault();
        }

        $locales = parent::getLocaleNames($locale);

        $collator = new \Collator($locale);
        $collator->asort($locales);

        return $locales;
    }
}
