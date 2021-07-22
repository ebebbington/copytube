<?php

namespace Tests\Browser\Pages;

use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterPageTest extends DuskTestCase
{
    use RefreshDatabase;

    public function testUserCanSuccessfullyRegister()
    {
        $this->browse(
            function ($browser) {
                $browser
                    ->visit("/register")
                    ->type("username", TestUtilities::$validUsername)
                    ->type("email", TestUtilities::$validEmail)
                    ->type("password", TestUtilities::$validPassword)
                    ->press("Submit")
                    ->waitUntil('!$.active')
                    ->assertSee("Created an account");
            }
        );
    }

    public function testUserWillSeeErrors()
    {
        $this->browse(
            function ($browser) {
                $browser
                    ->visit("/register")
                    ->type("username", "hello")
                    ->type("email", "hello")
                    ->type("password", "hello")
                    ->press("Submit")
                    ->waitUntil('!$.active')
                    ->assertSee("Error");
            }
        );
    }
}
