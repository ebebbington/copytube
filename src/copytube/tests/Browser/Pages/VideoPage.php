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
class VideoPage extends DuskTestCase
{
    private string $something_more_title = "Something More";
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return "/video";
    }

    public function testPageDisplaysAllContent()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb([
            "profile_picture" => "img/sample.jpg",
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
                ->visit($this->url() . "?requestedVideo=Something+More")
                ->assertpathIs($this->url());
            $browser->assertSee($this->something_more_title);
            $browser->assertSee(
                "Watch this inspirational video as we look at all of the beautiful things inside this world"
            );
            $browser->assertSee("Lava Sample");
            $browser->assertSee("An Iceland Venture");
            $video = $browser->element("#main-video-holder > video");
            $this->assertEquals(
                $this->something_more_title,
                $video->getAttribute("title")
            );
            $this->assertEquals(
                true,
                strpos($video->getAttribute("poster"), "img/something_more.jpg")
            );
            $this->assertEquals(
                true,
                strpos($video->getAttribute("src"), "videos/something_more.mp4")
            );
            $title = $browser->element("#main-video-holder h2");
            $this->assertEquals($this->something_more_title, $title->getText());
            $rabbitHole = $browser->elements(".rabbit-hole-video-holder");
            $this->assertEquals(2, count($rabbitHole));
            $browser->assertPresent("#add-comment-input");
            $browser->assertPresent("#comment-character-count");
            $browser->assertPresent("#comment-list");
            $browser->assertPresent("#account-options");
            TestUtilities::removeTestUsersInDb();
        });
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    //    public function assert(Browser $browser)
    //    {
    //        //
    //    }

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
