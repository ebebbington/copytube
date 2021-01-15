<?php

namespace Tests\Browser\Component;

use App\User;
use App\UserModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class HeaderComponentTest extends DuskTestCase
{
    public function testHomeLinkCanBeClicked()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function ($browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->waitForText("Something More")
                ->clickLink("Home")
                ->assertPathIs("/home");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testLoginLinkCanBeClickedWhenLoggedIn()
    {
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->waitForText("Chat")
                ->assertPathIs("/video")
                ->clickLink("Login")
                ->pause(5)
                ->assertPathIs("/home");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testLoginLinkCanBeClickedWhenNotLoggedIn()
    {
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function ($browser) use ($user) {
            $browser
                ->visit("/register")
                ->assertPathIs("/register")
                ->clickLink("Login")
                ->pause(5)
                ->assertPathIs("/login");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testChatLinkCanBeClicked()
    {
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->waitForText("Something More")
                ->assertPathIs("/video")
                ->clickLink("Chat")
                ->assertPathIs("/chat");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testRegisterLinkCanBeClicked()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit("/register")
                ->waitForText("Login")
                ->clickLink("Login")
                ->assertPathIs("/login");
        });
    }

    public function testUserOptionsLogoutButtonCanBeClicked()
    {
        TestUtilities::createTestUserInDb();
        $user = User::find(1);
        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->assertPathIs("/video");
            $browser
                ->press("#account-options")
                ->clickLink("Logout")
                ->pause(5)
                ->assertPathIs("/login");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserOptionsDeleteButtonCanBeClicked()
    {
        TestUtilities::createTestUserInDb();
        $user = User::find(1);
        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->assertPathIs("/video");
            $browser->press("#account-options");
            $this->assertEquals(
                "gear-dropdown",
                $browser->attribute(".gear-dropdown", "class")
            );
            $browser->click("#delete-account-trigger");
            $browser->acceptDialog();
            $browser
                ->waitUntil('!$.active')
                ->pause(10)
                ->assertPathIs("/register");
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserOptionsShowUsernameAndEmail()
    {
        TestUtilities::createTestUserInDb();
        $user = User::find(1);
        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->assertpathIs("/video");
            $this->assertEquals(
                "hide gear-dropdown",
                $browser->attribute(".gear-dropdown", "class")
            );
            $browser->press("#account-options");
            $this->assertEquals(
                "gear-dropdown",
                $browser->attribute(".gear-dropdown", "class")
            );
            $browser
                ->assertSee(TestUtilities::$validEmail)
                ->assertSee(TestUtilities::$validUsername);
            TestUtilities::removeTestUsersInDb();
        });
    }

    // TODO :: Test img src for img for contact options is correct

    public function elements()
    {
        return [
            "profilePicture" => "li.profile-picture",
        ];
    }
}
