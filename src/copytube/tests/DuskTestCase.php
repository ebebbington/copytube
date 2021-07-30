<?php

namespace Tests;

use App\Comment;
use App\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\TestCase as BaseTestCase;
use Laravel\Dusk\Browser;

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
        if (!static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments([
            "--disable-gpu",
            "--headless",
            "--window-size=1920,1080",
            "--no-sandbox",
        ]);

        return RemoteWebDriver::create(
            "http://selenium:4444/wd/hub",
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    protected function doLogin(Browser $browser, string $username = "Edward Home")
    {
        $browser->loginAs(User::where('username', $username)->limit(1)->first());
        //$browser->visit('http://copytube_nginx:9002');
        //$browser->type('#email', $user->email_address)->type('#password', 'Welcome1');
        //$browser->press('#login-button')->storeConsoleLog('bar');
        //$browser->pause(5000);
        //$browser->dump();
        return $browser;
    }

    protected function clean()
    {
        DB::table('comments')->where('id', '>', '3')->delete();
    }
}
