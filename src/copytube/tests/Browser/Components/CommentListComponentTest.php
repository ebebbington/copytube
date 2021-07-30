<?php

namespace Tests\Browser\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class CommentListComponentTest extends DuskTestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private string $test_uri = "/video?requestedVideo=Something+More";

    private string $path = "/video";

    private $comment_list_items_selector = "#comment-list > .media";

    private string $delete_comment_button_class_name = ".delete-comment";

    private string $add_comment_button_selector = "#comment > button";

    public function testANewCommentShowsWhenAddedByCurrentUser()
    {
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser)
                ->visit($this->test_uri)
                ->type("new-comment", "TEST COMMENT FROM DUSK");
            $browser
                ->scrollIntoView($this->add_comment_button_selector)
                ->click($this->add_comment_button_selector)
                ->waitForText("TEST COMMENT FROM DUSK", 10);
            $this->clean();
            $this->assertTrue(true);
        });
    }

    public function testANewCommentShowsWhenAddedByAnotherUser()
    {
        $this->browse(function (Browser $browserOne, Browser $browserTwo) {
            $this->doLogin($browserOne)
                ->visit($this->test_uri)
                ->assertpathIs($this->path);
            $this->doLogin($browserTwo, "test")
                ->visit($this->test_uri)
                ->assertpathIs($this->path);
            $numberOfComments = $browserTwo->elements(
                $this->comment_list_items_selector
            );
            $this->assertEquals(1, count($numberOfComments));
            $browserOne
                ->type("new-comment", "TEST COMMENT FROM DUSK TWO")
                ->scrollIntoView($this->add_comment_button_selector)
                ->click($this->add_comment_button_selector)
                ->waitForText("Success", 10);
            $numberOfComments = $browserTwo->elements(
                $this->comment_list_items_selector
            );
            $this->clean();
            $this->assertEquals(2, count($numberOfComments));
        });
    }

    public function testDeleteAndEditButtonsDisplayWhenCommentIsUsers()
    {
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser)
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $elem1 = $browser->elements(
                $this->delete_comment_button_class_name
            );
            $elem2 = $browser->elements("span.edit-comment");
            $this->assertEquals(2, sizeof($elem1));
            $this->assertEquals(2, sizeof($elem2));
        });
    }

    public function testDeleteAndEditButtonsDontDisplayOnOtherComments()
    {
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser)
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $elem1 = $browser->elements(
                $this->delete_comment_button_class_name
            );
            $elem2 = $browser->elements("span.edit-comment");
            $this->assertEquals(2, sizeof($elem1));
            $this->assertEquals(2, sizeof($elem2));
        });
    }

    public function testDeletingACommentDisplaysAPromptAndThenRemovesItFromDOM()
    {
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser)
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $browser
                ->click(
                    $this->delete_comment_button_class_name .
                        '[data-comment-id="' .
                        1 .
                        '"]'
                )
                ->assertDialogOpened(
                    "Are you sure you want to delete this comment?"
                );
            $this->clean();
            $browser->acceptDialog()->waitUntil(TestUtilities::$active);
            $elems = $browser->elements(
                $this->delete_comment_button_class_name
            );
            $this->assertEquals(1, sizeof($elems));
        });
    }

    public function testClickingEditButtonMakesCommentEditableAndThenCanSaveUpdatedComment()
    {
        $this->browse(function (Browser $browser) {
            $this->doLogin($browser)
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $browser->click('span.edit-comment[data-comment-id="' . 1 . '"]');
            $element = $browser->element(
                '.media > .media-body > p[contenteditable="true"]'
            );
            $this->assertEquals(true, $element !== null);
            $browser
                ->click('span.edit-comment[data-comment-id="' . 1 . '"]')
                ->waitUntil(TestUtilities::$active);
            $this->clean();
            $element = $browser->element(
                '.media > .media-body > p[contenteditable="false"]'
            );
            $this->assertEquals(true, $element !== null);
        });
    }

    // NOTE :: Not actually implemented yet
    public function testClickingDeleteButtonRemovesCommentFromDOM()
    {
        $this->browse(function (Browser $browserOne, Browser $browserTwo) {
            $this->doLogin($browserOne)
                ->visit($this->test_uri)
                ->assertpathIs($this->path);
            $this->doLogin($browserTwo, "test")
                ->visit($this->test_uri)
                ->assertpathIs($this->path);

            // Make sure we can see the comment first
            $this->assertEquals(
                1,
                count($browserTwo->elements($this->comment_list_items_selector))
            );

            $browserOne
                ->click('span.delete-comment[data-comment-id="' . 1 . '"]')
                ->acceptDialog()
                ->waitUntil(TestUtilities::$active)
                ->waitForText("Successfully deleted");
            $this->assertEquals(
                0,
                count($browserTwo->elements($this->comment_list_items_selector))
            );
        });
    }

    /**
     *  Relies on fixing the above. We need to test that when browser one
     *  deletes its account after adding comments, that browser two will not see
     *  those comments in the dom (both browsers need to be logged in to home page
     */
    public function testCommentsAreRemovedWhenAnAccountIsDeleted()
    {
        $this->browse(function (Browser $browserOne, Browser $browserTwo) {
            $this->doLogin($browserOne)
                ->visit($this->test_uri)
                ->assertpathIs($this->path);
            $this->doLogin($browserOne, "test")
                ->visit($this->test_uri)
                ->assertpathIs($this->path);

            // Make sure we can see the comment first
            $this->assertEquals(
                1,
                count($browserTwo->elements($this->comment_list_items_selector))
            );
            // delete user1 acc
            $browserOne->click("#delete-account-trigger");
            $browserOne->acceptDialog();
            $browserOne->waitForLocation("/register", 10);
            // assert comments removed
            $this->assertEquals(
                0,
                count($browserTwo->elements($this->comment_list_items_selector))
            );
        });
    }
}
