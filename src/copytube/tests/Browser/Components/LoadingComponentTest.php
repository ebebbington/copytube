<?php

namespace Tests\Browser\Component;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoadingComponentTest extends DuskTestCase
{
    public function testItShowsAndDisappears()
    {
        // Just going to use the login page for this
        $this->browse(function (Browser $browser) {
            $browser->visit("/login");
            $this->assertEquals(
                "",
                $browser->attribute("#overlay-container", "style")
            );
            $this->assertEquals(
                "",
                $browser->attribute("#loading-container", "style")
            );
            $browser->press("Submit");
            $this->assertEquals(
                "visibility: visible;",
                $browser->attribute("#overlay-container", "style")
            );
            $this->assertEquals(
                "visibility: visible;",
                $browser->attribute("#loading-container", "style")
            );
            $this->assertEquals(
                "animation: 1.5s ease 0s infinite normal none running pulse;",
                $browser->attribute("#loading-circle", "style")
            );
        });
    }
}
