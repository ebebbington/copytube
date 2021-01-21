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
                ->visit(TestUtilities::$login_path)
                ->type("email", $user->email_address)
                ->type("password", "Welcome1")
                ->press("Submit")
                ->waitUntil(TestUtilities::$active)
                ->assertPathIs(TestUtilities::$home_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUnsuccessfulLogin()
    {
        TestUtilities::removeTestUsersInDb();
        $this->browse(function ($browser) {
            $browser
                ->visit(TestUtilities::$login_path)
                ->type("email", "Hello")
                ->type("password", "Hello")
                ->press("Submit")
                ->waitUntil(TestUtilities::$active)
                ->assertPathIs(TestUtilities::$login_path);
        });
    }

    public function testCannotSeeLoginIfLoggedIn()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->visit(TestUtilities::$login_path)
                ->type("email", $user->email_address)
                ->type("password", "Welcome1")
                ->press("Submit")
                ->waitUntil(TestUtilities::$active, 20)
                ->assertPathIs("/home");
            $browser
                ->visit(TestUtilities::$login_path)
                ->assertPathIs(TestUtilities::$home_path);
            TestUtilities::removeTestUsersInDb();
        });
    }
}
