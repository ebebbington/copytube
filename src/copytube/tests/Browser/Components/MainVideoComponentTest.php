<?php

namespace Tests\Browse\Component;

use App\UserModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MainVideoComponentTest extends DuskTestCase
{
    use RefreshDatabase;

    public function testVideoAndTitleAndDescriptionAreCorrect()
    {
        TestUtilities::createTestUserInDb(
            [
            "profile_picture" => "img/sample.jpg",
            ]
        );
        $this->browse(
            function (Browser $browser) {
                $browser
                    ->loginAs(
                        UserModel::where(
                            "email_address",
                            "=",
                            "TestEmail@hotmail.com"
                        )->first()
                    )
                    ->visit(TestUtilities::$video_path_with_query)
                    ->assertPathIs(TestUtilities::$video_path);
                $this->assertEquals(
                    "http://copytube_nginx:9002/videos/something_more.mp4",
                    $browser->attribute("#main-video-holder > video", "src")
                );
                $this->assertEquals(
                    "Something More",
                    $browser->attribute("#main-video-holder > h2", "innerHTML")
                );
                $this->assertEquals(
                    "Watch this inspirational video as we look at all of the beautiful things inside this world",
                    $browser->attribute("#main-video-holder > p", "innerHTML")
                );
            }
        );
    }

    public function testMainVideoDataChanges()
    {
        TestUtilities::createTestUserInDb(
            [
            "profile_picture" => "img/sample.jpg",
            ]
        );
        $this->browse(
            function (Browser $browser) {
                $browser
                    ->loginAs(
                        UserModel::where(
                            "email_address",
                            "=",
                            "TestEmail@hotmail.com"
                        )->first()
                    )
                    ->visit("/video?requestedVideo=Something+More")
                    ->assertPathIs(TestUtilities::$video_path)
                    ->visit("/video?requestedVideo=Lava+Sample")
                    ->assertPathIs(TestUtilities::$video_path);
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
            }
        );
    }
}
