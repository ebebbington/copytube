<?php

namespace Tests\Browse\Component;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component;
use Tests\DuskTestCase;

class LoadingComponentTest extends DuskTestCase
{
    public function testItShowsAndDisappears()
    {
        // Just going to use the login page for this
        $this->browse(function (Browser $browser) {
            $browser->visit('/login');
            $this->assertEquals('', $browser->attribute('#overlay-container', 'style'));
            $this->assertEquals('', $browser->attribute('#loading-container', 'style'));
            $this->assertEquals('', $browser->attribute('#loading-circle-one', 'style'));
            $this->assertEquals('', $browser->attribute('#loading-circle-two', 'style'));
            $this->assertEquals('', $browser->attribute('#loading-circle-three', 'style'));
            $this->assertEquals('', $browser->attribute('#loading-circle-four', 'style'));
            $this->assertEquals('', $browser->attribute('#loading-circle-five', 'style'));
            $browser->press('Submit');
            $this->assertEquals('visibility: visible;', $browser->attribute('#overlay-container', 'style'));
            $this->assertEquals('visibility: visible;', $browser->attribute('#loading-container', 'style'));
            $this->assertEquals('animation: 1.5s ease 0s infinite normal none running pulse;', $browser->attribute('#loading-circle-one', 'style'));
            $this->assertEquals('animation: 1.5s ease 0.2s infinite normal none running pulse;', $browser->attribute('#loading-circle-two', 'style'));
            $this->assertEquals('animation: 1.5s ease 1.2s infinite normal none running pulse;', $browser->attribute('#loading-circle-three', 'style'));
            $browser->press('Submit');
            $this->assertEquals('animation: 1.5s ease 0.4s infinite normal none running pulse;', $browser->attribute('#loading-circle-four', 'style'));
            $this->assertEquals('animation: 1.5s ease 0.8s infinite normal none running pulse;', $browser->attribute('#loading-circle-five', 'style'));
        });
    }
}
