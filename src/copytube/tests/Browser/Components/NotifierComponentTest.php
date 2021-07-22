<?php

namespace Tests\Browse\Component;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NotifierComponentTest extends DuskTestCase
{
    private string $notifier_container = "#notifier-container";
    private string $notifier_title = "#notifier-title";
    public function testItsHiddenThenShowsWhenRequested()
    {
        // going to use login page as an example
        $this->browse(
            function (Browser $browser) {
                $browser->visit("/login")->assertPathIs("/login");
                $this->assertEquals(
                    "",
                    $browser->attribute($this->notifier_container, "style")
                );
                $this->assertEquals(
                    "",
                    $browser->attribute($this->notifier_title, "class")
                );
                $this->assertEquals(
                    "",
                    $browser->attribute($this->notifier_title, "innerHTML")
                );
                $this->assertEquals(
                    "",
                    $browser->attribute("#notifier-message", "innerHTML")
                );
                $browser->press("Submit")->waitUntil('!$.active');
                $this->assertEquals(
                    "Login",
                    $browser->attribute($this->notifier_title, "innerHTML")
                );
                $this->assertEquals(
                    "Server Error",
                    $browser->attribute("#notifier-message", "innerHTML")
                );
                $this->assertEquals(
                    "visibility: visible;",
                    $browser->attribute($this->notifier_container, "style")
                );
                $this->assertEquals(
                    "error",
                    $browser->attribute($this->notifier_container, "class")
                );
                sleep(5);
                $this->assertEquals(
                    "visibility: hidden;",
                    $browser->attribute($this->notifier_container, "style")
                );
            }
        );
    }
}
