<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AddCommentComponentTest extends DuskTestCase
{
    private string $uri = "/video?requestedVideo=Something+More";

    public function testCharacterCountWorksAndTextCanBeWritten()
    {
        // dd(env('DB_DATABASE'));
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser);
            $browser
                 ->visit("http://copytube_nginx:9002$this->uri")
                ->type("#add-comment-input", "hello");
            $count = $browser->text('#comment-character-count');
            $this->assertEquals("5", $count);
            $comment = $browser->inputValue("#add-comment-input");
            $this->assertEquals("hello", $comment);
            $browser->clear("new-comment");
        });
    }

    public function testErrorWhenSendingWithNoText()
    {
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser);
            $browser
                ->visit($this->uri);
            $loadingContainer = $browser->element("#loading-container");
            $loadingVisibility = $loadingContainer->getCSSValue("visibility");
            $this->assertEquals("hidden", $loadingVisibility);
            $notifyContainer = $browser->element("#notifier-container");
            $notifyVisibility = $notifyContainer->getCSSValue("visibility");
            $this->assertEquals("hidden", $notifyVisibility);

            $browser->scrollIntoView('#comment > button');
            $browser->click("#comment > button");

            $loadingContainer = $browser->element("#loading-container");
            $loadingVisibility = $loadingContainer->getCSSValue("visibility");
            $this->assertEquals("visible", $loadingVisibility);
            $browser->waitForText("The comment field is required", 10);

            $notifyContainer = $browser->element("#notifier-container");
            $notifyVisibility = $notifyContainer->getCSSValue("visibility");
            $this->assertEquals("visible", $notifyVisibility);
        });
    }


    //
    public function testSuccessWhenSendingWithComment()
    {
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser);
            $browser
                ->visit($this->uri);
            $browser->type("new-comment", "hello");
            $browser->scrollIntoView('#comment > button');
            $browser->click("#comment > button");
            $browser->waitForText("Success", 10);
            $this->clean();
            $browser->waitForText('hello', 10);
            $selector = ".media[data-user-id=\"" . 21 . "\"]";
            $comment = $browser->element($selector . " p");
            $commentValue = $comment->getText();
            $this->assertEquals("hello", $commentValue);
        });
    }
}
