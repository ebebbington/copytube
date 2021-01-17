<?php

namespace Tests\Browse\Component;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NotifierComponentTest extends DuskTestCase
{
    public function testItsHiddenThenShowsWhenRequested()
    {
        // going to use login page as an example
        $this->browse(function (Browser $browser) {
            $browser->visit("/login")->assertPathIs("/login");
            $this->assertEquals(
                "",
                $browser->attribute("#notifier-container", "style")
            );
            $this->assertEquals(
                "",
                $browser->attribute("#notifier-title", "class")
            );
            $this->assertEquals(
                "",
                $browser->attribute("#notifier-title", "innerHTML")
            );
            $this->assertEquals(
                "",
                $browser->attribute("#notifier-message", "innerHTML")
            );
            $browser->press("Submit")->waitUntil('!$.active');
            $this->assertEquals(
                "Login",
                $browser->attribute("#notifier-title", "innerHTML")
            );
            $this->assertEquals(
                "Server Error",
                $browser->attribute("#notifier-message", "innerHTML")
            );
            $this->assertEquals(
                "visibility: visible;",
                $browser->attribute("#notifier-container", "style")
            );
            $this->assertEquals(
                "error",
                $browser->attribute("#notifier-container", "class")
            );
            sleep(5);
            $this->assertEquals(
                "visibility: hidden;",
                $browser->attribute("#notifier-container", "style")
            );
        });
    }
}
