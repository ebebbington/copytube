<?php

namespace Tests\Browser\Pages;

use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class RegisterPageTest extends DuskTestCase
{
    public function testUserCanSuccessfullyRegister()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit("/register")
                ->type("username", TestUtilities::$validUsername)
                ->type("email", TestUtilities::$validEmail)
                ->type("password", TestUtilities::$validPassword)
                ->press("Submit")
                ->waitUntil('!$.active')
                ->assertSee("Created an account");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserWillSeeErrors()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit("/register")
                ->type("username", "hello")
                ->type("email", "hello")
                ->type("password", "hello")
                ->press("Submit")
                ->waitUntil('!$.active')
                ->assertSee("Error");
        });
    }
}
