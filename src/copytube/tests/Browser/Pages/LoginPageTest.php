<?php

namespace Tests\Browser\Pages;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class LoginPageTest extends DuskTestCase
{
    private $uri = "/login";

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
                ->visit($this->uri)
                ->type("email", $user->email_address)
                ->type("password", "Welcome1")
                ->press("Submit")
                ->waitUntil(TestUtilities::$active)
                ->assertPathIs("/home");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUnsuccessfulLogin()
    {
        TestUtilities::removeTestUsersInDb();
        $this->browse(function ($browser) {
            $browser
                ->visit($this->uri)
                ->type("email", "Hello")
                ->type("password", "Hello")
                ->press("Submit")
                ->waitUntil(TestUtilities::$active)
                ->assertPathIs($this->uri);
        });
    }

    public function testCannotSeeLoginIfLoggedIn()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function ($browser) use ($user) {
            $browser
                ->visit($this->uri)
                ->type("email", $user->email_address)
                ->type("password", "Welcome1")
                ->press("Submit")
                ->waitUntil(TestUtilities::$active)
                ->assertPathIs("/home");
            $browser->visit($this->uri)->assertPathIs("/home");
            TestUtilities::removeTestUsersInDb();
        });
    }
}
