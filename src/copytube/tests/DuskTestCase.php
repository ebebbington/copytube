<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--ignore-certificate-errors',
        ]);

        return RemoteWebDriver::create(
            "http://selenium:4444/wd/hub",
            // TODO :: Try use ::firefox() instead too
            // todo :: then try https://github.com/derekmd/laravel-dusk-firefox
            // todo :: then try https://laravel.com/docs/8.x/dusk#running-tests-on-github-actions (maybe means we remove selenium container?)
            // TODO :: Then maybee get into a scenario where we don't have to use a selenium image? (might coincide with running using github actions)
            // TODO :: Then try follow https://laravel.com/docs/5.8/dusk#using-other-browsers, maybe we dont need selenium for this but we need to find what the default url for dusk tets cas eis
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
                ->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true)
                ->setCapability('acceptInsecureCerts', true)
        );
    }
}
