<?php

namespace Tests\Browser\Component;

use App\UserModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCommentComponentTest extends DuskTestCase
{
    use RefreshDatabase;

    private string $uri = "/video?requestedVideo=Something+More";

    private string $path = "/video";

    public function testCharacterCountWorksAndTextCanBeWritten()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(
            function (Browser $browser) {
                $browser->pause(10);
                $browser->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )
                        ->limit(1)
                        ->first()
                );
                $browser
                    ->pause(10)
                    ->visit("http://copytube_nginx:9002$this->uri")
                    ->assertpathIs($this->path)
                    ->type("new-comment", "hello");
                $count = $browser->attribute(
                    "#comment-character-count",
                    "innerHTML"
                );
                $comment = $browser->value("#add-comment-input");
                $this->assertEquals("5", $count);
                $this->assertEquals("hello", $comment);
                $browser->clear("new-comment");
            }
        );
    }

    public function testErrorWhenSendingWithNoText()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(
            function (Browser $browser) {
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
                    ->visit($this->uri)
                    ->assertpathIs($this->path);

                $loadingContainer = $browser->element("#loading-container");
                $loadingVisibility = $loadingContainer->getCSSValue("visibility");
                $this->assertEquals("hidden", $loadingVisibility);
                $notifyContainer = $browser->element("#notifier-container");
                $notifyVisibility = $notifyContainer->getCSSValue("visibility");
                $this->assertEquals("hidden", $notifyVisibility);

                $browser->click("#comment > button");

                $loadingContainer = $browser->element("#loading-container");
                $loadingVisibility = $loadingContainer->getCSSValue("visibility");
                $this->assertEquals("visible", $loadingVisibility);

                $browser->waitForText("The comment field is required", 10);

                $notifyContainer = $browser->element("#notifier-container");
                $notifyVisibility = $notifyContainer->getCSSValue("visibility");
                $this->assertEquals("visible", $notifyVisibility);

                $browser->assertPathIs($this->path);
            }
        );
    }
    //
    public function testSuccessWhenSendingWithComment()
    {
        $userId = TestUtilities::createTestUserInDb(
            [
            "profile_picture" => "img/sample.jpg",
            ]
        );
        $this->browse(
            function (Browser $browser) use ($userId) {
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
                    ->visit($this->uri)
                    ->assertpathIs($this->path)
                    ->type("new-comment", "hello")
                    ->click("#comment > button");
                $browser->waitForText("Success", 10);
                $browser->pause(2000);
                $selector = ".media[data-user-id=\"" . $userId . "\"]";
                $commentContainer = $browser->element($selector);
                $this->assertTrue($commentContainer !== null);
                $comment = $browser->element($selector . " p");
                $commentValue = $comment->getText();
                $this->assertEquals("hello", $commentValue);
            }
        );
    }
}
