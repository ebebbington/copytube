<?php

namespace Tests\Browser\Pages;

use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class RecoverPageTest extends DuskTestCase
{
    public function testUserCanSuccessfullyRecover()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb([
            "recover_token" => "test_token",
            "login_attempts" => 0,
            "profile_picture" => "something.jpg",
        ]);
        $this->browse(function ($browser) {
            $browser
                ->visit("/recover?token=test_token")
                ->assertPathIs("/recover")
                ->type("email", TestUtilities::$validEmail)
                ->type("password", TestUtilities::$validPassword)
                ->press("Submit")
                ->waitUntil(TestUtilities::$active);
            $browser->assertSee("Successfully Reset Your Password");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserWillSeeErrorsWithInvalidCreds()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb([
            "recover_token" => "test_token",
            "login_attempts" => 0,
        ]);
        $this->browse(function ($browser) {
            $browser
                ->visit("/recover?token=test_token")
                ->type("email", "hello")
                ->type("password", "hello")
                ->press("Submit")
                ->waitUntil(TestUtilities::$active)
                ->assertSee("Unable to authenticate");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserWillBeRedirectedWithInvalidToken()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb([
            "recover_token" => "test_token",
            "login_attempts" => 0,
        ]);
        $this->browse(function ($browser) {
            $browser
                ->visit("/recover?token=invalid")
                ->type("email", TestUtilities::$validEmail)
                ->type("password", TestUtilities::$validPassword)
                ->press("Submit")
                ->waitUntil(TestUtilities::$active, 20)
                ->assertPathIs("/login");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserIsRedirectedWhenNoToken()
    {
        TestUtilities::removeTestUsersInDb();
        $this->browse(function ($browser) {
            $browser->visit("/register");
            $browser
                ->waitUntil(TestUtilities::$active)
                ->pause(10)
                ->assertPathIs("/register");
        });
    }
}
