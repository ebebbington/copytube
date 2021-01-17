<?php

namespace Tests\Browse\Component;

use App\UserModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class VideoSearchComponentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testTextCanBeInserted()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        "TestEmail@hotmail.com"
                    )->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->type("#search-bar", "Something More");
            $value = $browser->attribute("#search-bar", "value");
            $this->assertEquals("Something More", $value);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testDropdownCorrectlyShows()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        "TestEmail@hotmail.com"
                    )->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->type("#search-bar", "Something More");
            $value = $browser->attribute(
                "#search-bar-matching-dropdown > li",
                "innerHTML"
            );
            $this->assertEquals("Loading...", $value);
            $browser->waitUntil('!$.active');
            $value = $browser->attribute(
                "#search-bar-matching-dropdown > li",
                "innerHTML"
            );
            $this->assertEquals("Something More", $value);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testVideosChangeOnSubmit()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        "TestEmail@hotmail.com"
                    )->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->type("#search-bar", "Lava Sample")
                ->click("#search-button")
                ->waitUntil('!$.active');
            $this->assertEquals(
                "http://copytube_nginx:9002/videos/lava_sample.mp4",
                $browser->attribute("#main-video-holder > video", "src")
            );
            $this->assertEquals(
                "Lava Sample",
                $browser->attribute("#main-video-holder > h2", "innerHTML")
            );
            $this->assertEquals(
                "Watch this lava flow through the earth, burning and sizzling as it progresses",
                $browser->attribute("#main-video-holder > p", "innerHTML")
            );
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testClickOfDropdownValue()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        "TestEmail@hotmail.com"
                    )->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->type("#search-bar", "Lava Sample")
                ->waitUntil('!$.active');
            $browser
                ->click("#search-bar-matching-dropdown > li")
                ->waitUntil('!$.active');
            $this->assertEquals(
                "http://copytube_nginx:9002/videos/lava_sample.mp4",
                $browser->attribute("#main-video-holder > video", "src")
            );
            $this->assertEquals(
                "Lava Sample",
                $browser->attribute("#main-video-holder > h2", "innerHTML")
            );
            $this->assertEquals(
                "Watch this lava flow through the earth, burning and sizzling as it progresses",
                $browser->attribute("#main-video-holder > p", "innerHTML")
            );
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testItIsStuckOnScroll()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        "TestEmail@hotmail.com"
                    )->first()
                )
                ->visit("/video?requestedVideo=Something+More");
            $this->assertEquals(
                "input-group",
                $browser->attribute("#search", "class")
            );
            $browser->driver->executeScript("window.scrollTo(0, 500);");
            $this->assertEquals(
                "input-group stick",
                $browser->attribute("#search", "class")
            );
            TestUtilities::removeTestUsersInDb();
        });
    }
}
