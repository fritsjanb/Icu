<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Icu\Tests\V0;

use Symfony\Component\Icu\IcuCurrencyBundle;
use Symfony\Component\Icu\IcuLanguageBundle;
use Symfony\Component\Icu\IcuLocaleBundle;
use Symfony\Component\Icu\IcuRegionBundle;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\ResourceBundle\Reader\PhpBundleReader;
use Symfony\Component\Intl\ResourceBundle\Reader\StructuredBundleReader;
use Symfony\Component\Intl\Util\IcuVersion;

/**
 * Verifies that the data files can actually be read.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class IcuIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (IcuVersion::compare(Intl::getIcuVersion(), '4.0', '>=', $precision = 1)) {
            $this->markTestSkipped('This test requires ICU version < 4.0');
        }
    }

    public function testCurrencyBundle()
    {
        $bundle = new IcuCurrencyBundle(new StructuredBundleReader(new PhpBundleReader()));

        $this->assertSame('€', $bundle->getCurrencySymbol('EUR', 'en'));
        $this->assertSame(array('en'), $bundle->getLocales());
    }

    public function testLanguageBundle()
    {
        $bundle = new IcuLanguageBundle(new StructuredBundleReader(new PhpBundleReader()));

        $this->assertSame('German', $bundle->getLanguageName('de', null, 'en'));
        $this->assertSame(array('en'), $bundle->getLocales());
    }

    public function testLocaleBundle()
    {
        $bundle = new IcuLocaleBundle(new StructuredBundleReader(new PhpBundleReader()));

        $this->assertSame('Azerbaijani', $bundle->getLocaleName('az', 'en'));
        $this->assertSame(array('en'), $bundle->getLocales());
    }

    public function testRegionBundle()
    {
        $bundle = new IcuRegionBundle(new StructuredBundleReader(new PhpBundleReader()));

        $this->assertSame('United Kingdom', $bundle->getCountryName('GB', 'en'));
        $this->assertSame(array('en'), $bundle->getLocales());
    }
}
