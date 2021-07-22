<?php

namespace Tests\Browse\Component;

use App\UserModel;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class VideoSearchComponentTest extends DuskTestCase
{
    private string $something_more_title = "Something More";

    private string $search_bar_id = "#search-bar";

    private string $lava_sample_title = "Lava Sample";

    private string $search_bar_results_selector = "#search-bar-matching-dropdown > li";

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
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit(TestUtilities::$video_path_with_query)
                ->type($this->search_bar_id, $this->something_more_title);
            $value = $browser->attribute($this->search_bar_id, "value");
            $this->assertEquals($this->something_more_title, $value);
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
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit(TestUtilities::$video_path_with_query)
                ->type($this->search_bar_id, $this->something_more_title);
            $value = $browser->attribute(
                $this->search_bar_results_selector,
                "innerHTML"
            );
            $this->assertEquals("Loading...", $value);
            $browser->waitUntilMissingText("Loading...", 20);
            $value = $browser->attribute(
                $this->search_bar_results_selector,
                "innerHTML"
            );
            $this->assertEquals($this->something_more_title, $value);
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
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit(TestUtilities::$video_path_with_query)
                ->type($this->search_bar_id, $this->lava_sample_title)
                ->click("#search-button")
                ->waitUntil(TestUtilities::$active);
            $this->assertEquals(
                "http://copytube_nginx:9002/videos/lava_sample.mp4",
                $browser->attribute("#main-video-holder > video", "src")
            );
            $this->assertEquals(
                $this->lava_sample_title,
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
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit(TestUtilities::$video_path_with_query)
                ->type($this->search_bar_id, $this->lava_sample_title)
                ->waitUntil(TestUtilities::$active, 20);
            $browser
                ->click($this->search_bar_results_selector)
                ->waitUntil(TestUtilities::$active, 20);
            $this->assertEquals(
                "http://copytube_nginx:9002/videos/lava_sample.mp4",
                $browser->attribute("#main-video-holder > video", "src")
            );
            $this->assertEquals(
                $this->lava_sample_title,
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
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit(TestUtilities::$video_path_with_query);
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
