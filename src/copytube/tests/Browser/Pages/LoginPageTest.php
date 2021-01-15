<?php

namespace Tests\Browser\Pages;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class LoginPageTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testSuccessfulLogin()
    {
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function ($browser) use ($user) {
            $browser
                ->visit("/login")
                ->type("email", $user->email_address)
                ->type("password", "Welcome1")
                ->press("Submit")
                ->waitUntil('!$.active')
                ->assertPathIs("/home");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUnsuccessfulLogin()
    {
        TestUtilities::removeTestUsersInDb();
        $this->browse(function ($browser) {
            $browser
                ->visit("/login")
                ->type("email", "Hello")
                ->type("password", "Hello")
                ->press("Submit")
                ->waitUntil('!$.active')
                ->assertPathIs("/login");
        });
    }

    public function testCannotSeeLoginIfLoggedIn()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function ($browser) use ($user) {
            $browser
                ->visit("/login")
                ->type("email", $user->email_address)
                ->type("password", "Welcome1")
                ->press("Submit")
                ->waitUntil('!$.active')
                ->assertPathIs("/home");
            $browser->visit("/login")->assertPathIs("/home");
            TestUtilities::removeTestUsersInDb();
        });
    }
}
