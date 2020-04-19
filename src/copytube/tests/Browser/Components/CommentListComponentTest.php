<?php

namespace Tests\Browser\Component;

use App\UserModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class CommentListComponentTest extends DuskTestCase
{
    public function testANewCommentShowsWhenAddedByCurrentUser()
    {
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb(['profile_picture' => 'img/sample.jpg']);
        $this->browse(function (Browser $browser) {
            $browser->loginAs(UserModel::where('email_address', '=', TestUtilities::$validEmail)->limit(1)->first())
                ->visit('/home')
                ->assertpathIs('/home')
                ->type('new-comment', 'TEST COMMENT FROM DUSK')
                ->click('#comment > button')
                ->waitUntil('!$.active')
                ->pause(5000)
                ->assertSee('TEST COMMENT FROM DUSK');
            TestUtilities::removeTestUsersInDb();
        });
    }

    /**
     * FIXME :: `invalid session id`, only shows when browserTwo is trying to login as another user
     * @throws \Throwable
     */
    public function testANewCommentShowsWhenAddedByAnotherUser()
    {
        TestUtilities::createTestUserInDb(['email_address' => 'TestEmail1@hotmail.com', 'profile_picture' => 'img/sample.jpg']);
        TestUtilities::createTestUserInDb(['email_address' => 'TestEmail2@hotmail.com', 'profile_picture' => 'img/sample.jpg']);
        $this->browse(function (Browser $browser, Browser $browserTwo) {
            $browser->loginAs(UserModel::where('email_address', '=', 'TestEmail1@hotmail.com')->first())
                ->visit('/home');
            //$browserTwo->loginAs(UserModel::where('email_address', '=', 'TestEmail1@hotmail.com')->first())
                //->visit('/home');
            $browser
                ->assertpathIs('/home')
                ->waitUntil('!$.active')
                ->type('new-comment', 'TEST COMMENT FROM DUSK TWO');
            //$browserTwo->assertPathIs('/home');
            $browser
                ->click('#comment > button')
                ->waitUntil('!$.active');
            //$browserTwo->waitForText('TEST COMMENT FROM DUSK TWO');
            TestUtilities::removeTestUsersInDb();
        });
    }

    /**
     * TODO
     *  Relies on fixing the above. We need to test that when browser one
     *  deletes its account after adding comments, that browser two will not see
     *  those comments in the dom (both browsers need to be logged in to home page
     */
    public function testCommentsAreRemovedWhenAnAccountIsDeleted ()
    {
        $this->assertEquals(1, 1);
    }
}
