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

    private string $comment_list_items_selector = "#comment-list media";

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
                ->waitForText("TEST COMMENT FROM DUSK", 10);
            TestUtilities::removeTestUsersInDb();
            TestUtilities::removeTestCommentsInDB();
        });
    }

//    public function testANewCommentShowsWhenAddedByAnotherUser()
//    {
//        TestUtilities::createTestUserInDb([
//            "email_address" => "TestEmail1@hotmail.com",
//            "profile_picture" => $this->profile_picture_path,
//        ]);
//        TestUtilities::createTestUserInDb([
//            "email_address" => "TestEmail2@hotmail.com",
//            "profile_picture" => $this->profile_picture_path,
//        ]);
//        $this->browse(function (Browser $browserOne, Browser $browserTwo) {
//            $browserOne
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        "TestEmail1@hotmail.com"
//                    )
//                        ->limit(1)
//                        ->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path);
//            $browserTwo
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        "TestEmail2@hotmail.com"
//                    )
//                        ->limit(1)
//                        ->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path);
//            $numberOfComments = $browserTwo->elements(
//                $this->comment_list_items_selector
//            );
//            $this->assertEquals(10, count($numberOfComments));
//            $browserOne
//                ->type("new-comment", "TEST COMMENT FROM DUSK TWO")
//                ->click("#comment > button")
//                ->waitForText("Success", 10);
//            $numberOfComments = $browserTwo->elements(
//                $this->comment_list_items_selector
//            );
//            $this->assertEquals(11, count($numberOfComments));
//            TestUtilities::removeTestUsersInDb();
//            TestUtilities::removeTestCommentsInDB();
//        });
//    }
//
//    public function testDeleteAndEditButtonsDisplayWhenCommentIsUsers()
//    {
//        $userId = TestUtilities::createTestUserInDb();
//        $user = TestUtilities::getTestUserInDb($userId);
//        TestUtilities::createTestCommentInDb($user);
//        $this->browse(function (Browser $browser) {
//            $browser
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        TestUtilities::$validEmail
//                    )->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path)
//                ->waitUntil(TestUtilities::$active);
//            $elem1 = $browser->elements(
//                $this->delete_comment_button_class_name
//            );
//            $elem2 = $browser->elements("span.edit-comment");
//            $this->assertEquals(2, sizeof($elem1));
//            $this->assertEquals(2, sizeof($elem2));
//            TestUtilities::removeTestCommentsInDB();
//            TestUtilities::removeTestUsersInDb();
//        });
//    }
//
//    public function testDeleteAndEditButtonsDontDisplayOnOtherComments()
//    {
//        TestUtilities::createTestUserInDb();
//        $this->browse(function (Browser $browser) {
//            $browser
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        TestUtilities::$validEmail
//                    )->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path)
//                ->waitUntil(TestUtilities::$active);
//            $elem1 = $browser->elements(
//                $this->delete_comment_button_class_name
//            );
//            $elem2 = $browser->elements("span.edit-comment");
//            $this->assertEquals(1, sizeof($elem1));
//            $this->assertEquals(1, sizeof($elem2));
//            TestUtilities::removeTestCommentsInDB();
//            TestUtilities::removeTestUsersInDb();
//        });
//    }
//
//    public function testDeletingACommentDisplaysAPromptAndThenRemovesItFromDOM()
//    {
//        Cache::flush();
//        $userId = TestUtilities::createTestUserInDb();
//        $user = TestUtilities::getTestUserInDb($userId);
//        $commentId = TestUtilities::createTestCommentInDb($user);
//        $this->browse(function (Browser $browser) use ($commentId) {
//            $browser
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        TestUtilities::$validEmail
//                    )->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path)
//                ->waitUntil(TestUtilities::$active);
//            $browser
//                ->click(
//                    $this->delete_comment_button_class_name .
//                        '[data-comment-id="' .
//                        $commentId .
//                        '"]'
//                )
//                ->assertDialogOpened(
//                    "Are you sure you want to delete this comment?"
//                );
//            $browser->acceptDialog()->waitUntil(TestUtilities::$active);
//            $elems = $browser->elements(
//                $this->delete_comment_button_class_name
//            );
//            $this->assertEquals(1, sizeof($elems));
//            TestUtilities::removeTestCommentsInDB();
//            TestUtilities::removeTestUsersInDb();
//        });
//    }
//
//    public function testClickingEditButtonMakesCommentEditableAndThenCanSaveUpdatedComment()
//    {
//        Cache::flush();
//        $userId = TestUtilities::createTestUserInDb();
//        $user = TestUtilities::getTestUserInDb($userId);
//        $commentId = TestUtilities::createTestCommentInDb($user);
//        $this->browse(function (Browser $browser) use ($commentId) {
//            $browser
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        TestUtilities::$validEmail
//                    )->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path)
//                ->waitUntil(TestUtilities::$active);
//            $browser->click(
//                'span.edit-comment[data-comment-id="' . $commentId . '"]'
//            );
//            $element = $browser->element(
//                '.media > .media-body > p[contenteditable="true"]'
//            );
//            $this->assertEquals(true, $element !== null);
//            $browser
//                ->click(
//                    'span.edit-comment[data-comment-id="' . $commentId . '"]'
//                )
//                ->waitUntil(TestUtilities::$active);
//            $element = $browser->element(
//                '.media > .media-body > p[contenteditable="false"]'
//            );
//            $this->assertEquals(true, $element !== null);
//            TestUtilities::removeTestCommentsInDB();
//            TestUtilities::removeTestUsersInDb();
//        });
//    }
//
//    // TODO :: fails, fix it
//    public function testClickingDeleteButtonRemovesCommentFromDOM()
//    {
//        Cache::flush();
//        $user1Id = TestUtilities::createTestUserInDb([
//            "email_address" => "TestEmail9@hotmail.com",
//        ]);
//        TestUtilities::createTestUserInDb([
//            "email_address" => "TestEmail10@hotmail.com",
//        ]);
//        $user1 = TestUtilities::getTestUserInDb($user1Id);
//        $commentId1 = TestUtilities::createTestCommentInDb($user1);
//        $this->browse(function (Browser $browserOne, Browser $browserTwo) use (
//            $commentId1
//        ) {
//            $browserTwo
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        "TestEmail9@hotmail.com"
//                    )
//                        ->limit(1)
//                        ->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path);
//            $browserTwo
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        "TestEmail10@hotmail.com"
//                    )
//                        ->limit(1)
//                        ->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path);
//
//            // Make sure we can see the comment first
//            $this->assertEquals(
//                10,
//                count($browserTwo->elements($this->comment_list_items_selector))
//            );
//
//            $browserOne
//                ->click(
//                    'span.delete-comment[data-comment-id="' . $commentId1 . '"]'
//                )
//                ->waitForText("Successfully deleted");
//            $this->assertEquals(
//                9,
//                count($browserTwo->elements($this->comment_list_items_selector))
//            );
//
//            TestUtilities::removeTestCommentsInDB();
//            TestUtilities::removeTestUsersInDb();
//        });
//    }
//
//    /**
//     *  Relies on fixing the above. We need to test that when browser one
//     *  deletes its account after adding comments, that browser two will not see
//     *  those comments in the dom (both browsers need to be logged in to home page
//     */
//    public function testCommentsAreRemovedWhenAnAccountIsDeleted()
//    {
//        Cache::flush();
//        $user1Id = TestUtilities::createTestUserInDb([
//            "email_address" => "TestEmail11@hotmail.com",
//        ]);
//        TestUtilities::createTestUserInDb([
//            "email_address" => "TestEmail12@hotmail.com",
//        ]);
//        $user1 = TestUtilities::getTestUserInDb($user1Id);
//        TestUtilities::createTestCommentInDb($user1);
//        $this->browse(function (Browser $browserOne, Browser $browserTwo) {
//            $browserTwo
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        "TestEmail11@hotmail.com"
//                    )
//                        ->limit(1)
//                        ->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path);
//            $browserTwo
//                ->loginAs(
//                    UserModel::where(
//                        "email_address",
//                        "=",
//                        "TestEmail12@hotmail.com"
//                    )
//                        ->limit(1)
//                        ->first()
//                )
//                ->visit($this->test_uri)
//                ->assertpathIs($this->path);
//
//            // Make sure we can see the comment first
//            $this->assertEquals(
//                10,
//                count($browserTwo->elements($this->comment_list_items_selector))
//            );
//            // delete user1 acc
//            $browserOne->click("#delete-account-trigger");
//            $browserOne->acceptDialog();
//            $browserOne->waitForLocation("/register", 10);
//            // assert comments removed
//            $this->assertEquals(
//                9,
//                count($browserTwo->elements($this->comment_list_items_selector))
//            );
//        });
//    }
}
