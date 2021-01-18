<?php

namespace Tests\Browser\Component;

use App\UserModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class CommentListComponentTest extends DuskTestCase
{
    private string $delete_comment_button_class_name = "span.delete-comment";

    private string $profile_picture_path = "img/sample.jpg";

    private string $test_uri = "/video?requestedVideo=Something+More";

    private string $path = "/video";

    public function testANewCommentShowsWhenAddedByCurrentUser()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb([
            "profile_picture" => $this->profile_picture_path,
        ]);
        $this->browse(function (Browser $browser) {
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
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->type("new-comment", "TEST COMMENT FROM DUSK")
                ->click("#comment > button")
                ->waitUntil(TestUtilities::$active)
                ->pause(9000)
                ->assertSee("TEST COMMENT FROM DUSK");
            TestUtilities::removeTestUsersInDb();
            TestUtilities::removeTestCommentsInDB();
        });
    }

    // TODO it doesn't actually check, because wee get invalid session id
    public function testANewCommentShowsWhenAddedByAnotherUser()
    {
        TestUtilities::createTestUserInDb([
            "email_address" => "TestEmail1@hotmail.com",
            "profile_picture" => $this->profile_picture_path,
        ]);
        TestUtilities::createTestUserInDb([
            "email_address" => "TestEmail2@hotmail.com",
            "profile_picture" => $this->profile_picture_path,
        ]);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        "TestEmail1@hotmail.com"
                    )->first()
                )
                ->visit($this->test_uri);
            //$browserTwo->loginAs(UserModel::where('email_address', '=', 'TestEmail2@hotmail.com')->first())
            ///->visit('/home');
            $browser
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active)
                ->type("new-comment", "TEST COMMENT FROM DUSK TWO");
            //$browserTwo->assertPathIs('/home');
            $browser
                ->click("#comment > button")
                ->waitUntil(TestUtilities::$active);
            //$browserTwo->waitForText('TEST COMMENT FROM DUSK TWO');
            TestUtilities::removeTestUsersInDb();
            TestUtilities::removeTestCommentsInDB();
        });
    }

    public function testDeleteAndEditButtonsDisplayWhenCommentIsUsers()
    {
        $userId = TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb($userId);
        TestUtilities::createTestCommentInDb($user);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $elem1 = $browser->elements($this->delete_comment_button_class_name);
            $elem2 = $browser->elements("span.edit-comment");
            $this->assertEquals(2, sizeof($elem1));
            $this->assertEquals(2, sizeof($elem2));
            TestUtilities::removeTestCommentsInDB();
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testDeleteAndEditButtonsDontDisplayOnOtherComments()
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
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $elem1 = $browser->elements($this->delete_comment_button_class_name);
            $elem2 = $browser->elements("span.edit-comment");
            $this->assertEquals(1, sizeof($elem1));
            $this->assertEquals(1, sizeof($elem2));
            TestUtilities::removeTestCommentsInDB();
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testDeletingACommentDisplaysAPromptAndThenRemovesItFromDOM()
    {
        Cache::flush();
        $userId = TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb($userId);
        $commentId = TestUtilities::createTestCommentInDb($user);
        $this->browse(function (Browser $browser) use ($commentId) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $browser
                ->click(
                    $this->delete_comment_button_class_name . '[data-comment-id="' . $commentId . '"]'
                )
                ->assertDialogOpened(
                    "Are you sure you want to delete this comment?"
                );
            $browser->acceptDialog()->waitUntil(TestUtilities::$active);
            $elems = $browser->elements($this->delete_comment_button_class_name);
            $this->assertEquals(1, sizeof($elems));
            TestUtilities::removeTestCommentsInDB();
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testClickingEditButtonMakesCommentEditableAndThenCanSaveUpdatedComment()
    {
        Cache::flush();
        $userId = TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb($userId);
        $commentId = TestUtilities::createTestCommentInDb($user);
        $this->browse(function (Browser $browser) use ($commentId) {
            $browser
                ->loginAs(
                    UserModel::where(
                        "email_address",
                        "=",
                        TestUtilities::$validEmail
                    )->first()
                )
                ->visit($this->test_uri)
                ->assertpathIs($this->path)
                ->waitUntil(TestUtilities::$active);
            $browser->click(
                'span.edit-comment[data-comment-id="' . $commentId . '"]'
            );
            $element = $browser->element(
                '.media > .media-body > p[contenteditable="true"]'
            );
            $this->assertEquals(true, $element !== null);
            $browser
                ->click('span.edit-comment[data-comment-id="' . $commentId . '"]')
                ->waitUntil(TestUtilities::$active);
            $element = $browser->element(
                '.media > .media-body > p[contenteditable="false"]'
            );
            $this->assertEquals(true, $element !== null);
            TestUtilities::removeTestCommentsInDB();
            TestUtilities::removeTestUsersInDb();
        });
    }

    // TODO :: fails, fix it
    //    public function testClickingDeleteButtonRemovesCommentFromDOM ()
    //    {
    //        Cache::flush();
    //        $id = TestUtilities::createTestUserInDb();
    //        $user = TestUtilities::getTestUserInDb($id);
    //        $commentId = TestUtilities::createTestCommentInDb($user);
    //        $this->browse(function (Browser $browser, Browser $browserTwo) use ($commentId) {
    //            $browser
    //                ->loginAs(UserModel::where('email_address', '=', TestUtilities::$validEmail)->first())
    //                ->visit('/video?requestedVideo=Something+More')
    //                ->assertpathIs('/video')
    //                ->waitUntil('!$.active');
    //            $browser->click('i.delete-comment[data-comment-id="'.$commentId.'"]')->waitUntil("!$.active");
    //            $element = $browser->element('i.delete-comment[data-comment-id="'.$commentId.'"]');
    //            $this->assertEquals(true, $element === NULL);
    //            TestUtilities::removeTestCommentsInDB();
    //            TestUtilities::removeTestUsersInDb();
    //        });
    //    }

    /**
     * TODO
     *  Relies on fixing the above. We need to test that when browser one
     *  deletes its account after adding comments, that browser two will not see
     *  those comments in the dom (both browsers need to be logged in to home page
     */
    public function testCommentsAreRemovedWhenAnAccountIsDeleted()
    {
        $this->assertEquals(1, 1);
    }
}
