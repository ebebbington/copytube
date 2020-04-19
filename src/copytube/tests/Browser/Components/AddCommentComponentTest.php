<?php

namespace Tests\Browser\Component;

use App\UserModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\TestUtilities;

class AddCommentComponentTest extends DuskTestCase
{
    public function testCharacterCountWorksAndTextCanBeWritten()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(UserModel::where('email_address', '=', TestUtilities::$validEmail)->limit(1)->first())
                ->visit('/home')
                ->assertpathIs('/home')
                ->type('new-comment', 'hello');
            $count = $browser->attribute('#comment-character-count', 'innerHTML');
            $comment = $browser->value('#add-comment-input');
            $this->assertEquals('5', $count);
            $this->assertEquals('hello', $comment);
            $browser->clear('new-comment');
            TestUtilities::removeTestUsersInDb();
        });
    }

    public function testErrorWhenSendingWithNoText()
    {
        TestUtilities::createTestUserInDb();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(UserModel::where('email_address', '=', TestUtilities::$validEmail)->limit(1)->first())
                ->visit('/home')
                ->assertpathIs('/home');
            $browser->click('#comment > button')
                ->waitUntil('!$.active');
            $browser->assertSee('The comment field is required');
            TestUtilities::removeTestUsersInDb();
        });
    }
//
    public function testSuccessWhenSendingWithComment()
    {
        TestUtilities::createTestUserInDb(['profile_picture' => 'img/sample.jpg']);
        $this->browse(function (Browser $browser) {
            $browser->loginAs(UserModel::where('email_address', '=', TestUtilities::$validEmail)->limit(1)->first())
                ->visit('/home')
                ->assertpathIs('/home')
                ->type('new-comment', 'hello')
                ->click('#comment > button')
                ->waitUntil('!$.active')
                ->assertSee('Success');
            TestUtilities::removeTestUsersInDb();
        });
    }
}
