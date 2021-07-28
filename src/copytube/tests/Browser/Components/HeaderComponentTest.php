<?php

namespace Tests\Browser\Components;

use App\User;
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
                    User::where(
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
                    User::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit(TestUtilities::$video_path_with_query)
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

    public function testRegisterLinkCanBeClicked()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit(TestUtilities::$login_path)
                ->waitForText("Login")
                ->clickLink("Register")
                ->assertPathIs(TestUtilities::$register_path);
        });
    }

    public function testUserOptionsLogoutButtonCanBeClicked()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function ($browser) {
            $browser
                ->loginAs(
                    User::where(
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
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    User::where(
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
            $browser->waitForLocation("/register", 10);
            $browser->assertPathIs(TestUtilities::$register_path);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testUserOptionsShowUsernameAndEmail()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function ($browser) {
            $browser
                ->loginAs(
                    User::where(
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

    public function testUserOptionsShowsProfilePicture()
    {
        TestUtilities::createTestUserInDb([
            "profile_picture" => "img/something_more.jpg",
        ]);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    User::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                )
                ->visit(TestUtilities::$home_path)
                ->assertpathIs(TestUtilities::$home_path);
            $accountOptions = $browser->element(
                $this->account_options_selector
            );
            $this->assertEquals(
                true,
                strpos(
                    $accountOptions->getAttribute("src"),
                    "img/something_more.jpg"
                )
            );
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function elements()
    {
        return [
            "profilePicture" => "li.profile-picture",
        ];
    }
}
