<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Feature\TestUtilities;

/***
 * Class HomePage
 *
 * @package Tests\Browser\Pages
 *
 * Not needed as our component tests cover this page
 */
// TODO Could expand upon this
class HomePage extends Page
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
    public function assertVideosDisplay(Browser $browser)
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        $this->browse(function ($browser) use ($user) {
            $browser
                ->visit("/home")
                ->assertPathIs("/home")
                ->assertSee("Something More")
                ->assertSee("Lava Sample")
                ->assertSee("An Iceland Venture");
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
