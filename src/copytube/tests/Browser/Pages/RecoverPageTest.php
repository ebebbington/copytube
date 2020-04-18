<?php

namespace Tests\Browser\Pages;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class RecoverPageTest extends DuskTestCase
{

    public function testUserCanSuccessfullyRecover()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb(['recover_token' => 'test_token', 'login_attempts' => 0]);
        $this->browse(function ($browser) {
            $browser->visit('/recover?token=test_token')
                ->assertPathIs('/recover')
                ->type('email', TestUtilities::$validEmail)
                ->type('password', TestUtilities::$validPassword)
                ->press('Submit')
                ->waitUntil('!$.active');
            var_dump($browser);
            $browser
                ->assertSee('Successfully Reset Your Password');
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserWillSeeErrorsWithInvalidCreds ()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb(['recover_token' => 'test_token', 'login_attempts' => 0]);
        $this->browse(function ($browser) {
            $browser->visit('/register?token=test_token')
                ->type('email', 'hello')
                ->type('password', 'hello')
                ->press('Submit')
                ->waitUntil('!$.active')
                ->assertSee('Unable to authenticate');
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserWillSeeErrorsWithInvalidToken ()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb(['recover_token' => 'test_token', 'login_attempts' => 0]);
        $this->browse(function ($browser) {
            $browser->visit('/register?token=invalid')
                ->type('email', TestUtilities::$validEmail)
                ->type('password', TestUtilities::$validPassword)
                ->press('Submit')
                ->waitUntil('!$.active')
                ->assertSee('Token does not match');
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserIsRedirectedWhenNoToken ()
    {
        TestUtilities::removeTestUsersInDb();
        $this->browse(function ($browser) {
            $browser->visit('/register');
            $browser->waitUntil('!$.active')
                ->pause(10)
                ->assertPathIs('/register');
        });
    }
}
