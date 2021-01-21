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
            // TODO :: Comment out line in prepare
            // TODO :: Try use ::firefox() instead too
            // todo :: then try https://github.com/derekmd/laravel-dusk-firefox
            // todo :: then try https://laravel.com/docs/8.x/dusk#running-tests-on-github-actions
            // TODO :: Then try set os in workflow to macos-latest, and install docker by: brew install docker-machine docker (if errors, maybe https://github.community/t/is-it-possible-to-install-and-configure-docker-on-macos-runner/16981/8 will help)
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
                ->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true)
                ->setCapability('acceptInsecureCerts', true)
        );
    }
}
