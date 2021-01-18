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
    private string $account_options_selector = "#account-options";

    private string $account_options_dropdown_selector = ".gear-dropdown";

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
                ->visit(TestUtilities::$video_path_with_query)
                ->waitForText("Something More")
                ->clickLink("Home")
                ->assertPathIs(TestUtilities::$home_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testLoginLinkCanBeClickedWhenLoggedIn()
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
                ->visit(TestUtilities::$video_path_with_query)
                ->waitForText("Chat")
                ->assertPathIs(TestUtilities::$video_path)
                ->clickLink("Login")
                ->pause(5)
                ->assertPathIs(TestUtilities::$home_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testLoginLinkCanBeClickedWhenNotLoggedIn()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function ($browser) {
            $browser
                ->visit(TestUtilities::$register_path)
                ->assertPathIs(TestUtilities::$register_path)
                ->clickLink("Login")
                ->pause(5)
                ->assertPathIs(TestUtilities::$login_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testChatLinkCanBeClicked()
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
                ->visit(TestUtilities::$video_path_with_query)
                ->waitForText("Something More")
                ->assertPathIs(TestUtilities::$video_path)
                ->clickLink("Chat")
                ->assertPathIs(TestUtilities::$chat_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testRegisterLinkCanBeClicked()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit(TestUtilities::$register_path)
                ->waitForText("Login")
                ->clickLink("Login")
                ->assertPathIs(TestUtilities::$login_path);
        });
    }

    public function testUserOptionsLogoutButtonCanBeClicked()
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
                ->visit(TestUtilities::$video_path_with_query)
                ->assertPathIs(TestUtilities::$video_path);
            $browser
                ->press($this->account_options_selector)
                ->clickLink("Logout")
                ->pause(5)
                ->assertPathIs(TestUtilities::$login_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserOptionsDeleteButtonCanBeClicked()
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
                ->visit(TestUtilities::$video_path_with_query)
                ->assertPathIs(TestUtilities::$video_path);
            $browser->press($this->account_options_selector);
            $this->assertEquals(
                "gear-dropdown",
                $browser->attribute(
                    $this->account_options_dropdown_selector,
                    "class"
                )
            );
            $browser->click("#delete-account-trigger");
            $browser->acceptDialog();
            $browser
                ->waitUntil(TestUtilities::$active)
                ->pause(10)
                ->assertPathIs(TestUtilities::$register_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserOptionsShowUsernameAndEmail()
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
                ->visit(TestUtilities::$video_path_with_query)
                ->assertpathIs(TestUtilities::$video_path);
            $this->assertEquals(
                "hide gear-dropdown",
                $browser->attribute(
                    $this->account_options_dropdown_selector,
                    "class"
                )
            );
            $browser->press($this->account_options_selector);
            $this->assertEquals(
                "gear-dropdown",
                $browser->attribute(
                    $this->account_options_dropdown_selector,
                    "class"
                )
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
