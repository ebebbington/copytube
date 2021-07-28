<?php

namespace Tests\Browser\Pages;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

/***
 * Class HomePage
 *
 * @package Tests\Browser\Pages
 */
class HomePageTest extends DuskTestCase
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return "/";
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function testVideosDisplay()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(
                User::where("email_address", "=", TestUtilities::$validEmail)
                    ->limit(1)
                    ->first()
            );
            $browser
                ->visit("/home")
                ->assertPathIs("/home")
                ->assertSee("Something More")
                ->assertSee("Lava Sample")
                ->assertSee("An Iceland Venture");
            $browser->assertPresent("#account-options");
            $rabbitHole = $browser->elements(".rabbit-hole-video-holder");
            $this->assertEquals(3, count($rabbitHole));
            TestUtilities::removeTestUsersInDb();
        });
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            "@element" => "#selector",
        ];
    }
}
