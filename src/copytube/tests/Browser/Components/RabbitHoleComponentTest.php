<?php

namespace Tests\Browser\Components;

use App\User;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class RabbitHoleComponentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testDataShows()
    {
        Cache::flush();
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    User::where(
                        "email_address",
                        "=",
                        "TestEmail@hotmail.com"
                    )->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->assertPathIs("/video");
            //->assertSee('Lava Sample')
            //->assertSee('An Iceland Venture')
            //->assertPresent('.rabbit-hole-video-holder > video[src="videos/lava_sample.mp4"]')
            //->assertPresent('.rabbit-hole-video-holder > video[src="videos/an_iceland_venture.mp4"]');
            $value = $browser->attribute(
                '.rabbit-hole-video-holder > video[src="videos/lava_sample.mp4"] + p',
                "innerHTML"
            );
            $this->assertEquals("Lava Sample", $value);
            $value = $browser->attribute(
                '.rabbit-hole-video-holder > video[src="videos/an_iceland_venture.mp4"] + p',
                "innerHTML"
            );
            $this->assertEquals("An Iceland Venture", $value);
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testVideoClick()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            // login
            $browser
                ->loginAs(
                    User::where(
                        "email_address",
                        "=",
                        "TestEmail@hotmail.com"
                    )->first()
                )
                ->visit("/video?requestedVideo=Something+More")
                ->assertPathIs("/video");
            // change path
            $browser
                ->click(
                    '.rabbit-hole-video-holder > video[src="videos/lava_sample.mp4"]'
                )
                ->waitUntil('!$.active');
            // check rabbit hole videos
            $this->assertEquals(
                "videos/lava_sample.mp4",
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
            $browser
                ->assertPresent(
                    '.rabbit-hole-video-holder > video[src="videos/something_more.mp4"]'
                )
                ->assertPresent(
                    '.rabbit-hole-video-holder > video[src="videos/an_iceland_venture.mp4"]'
                );
            $value = $browser->attribute(
                '.rabbit-hole-video-holder > video[src="videos/something_more.mp4"] + p',
                "innerHTML"
            );
            $this->assertEquals("Something More", $value);
            $value = $browser->attribute(
                '.rabbit-hole-video-holder > video[src="videos/an_iceland_venture.mp4"] + p',
                "innerHTML"
            );
            $this->assertEquals("An Iceland Venture", $value);
            TestUtilities::removeTestUsersInDb();
        });
    }
}
