<?php

namespace Tests\Browse\Component;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VideoChatComponentTest extends DuskTestCase
{
    // FIXME :: Seems navigator doesnt work with dusk (uncomment tests, run, and see console files)
//    public function testUserCanCallAndRemoteVideoShows()
//    {
//        $this->browse(function (Browser $first, Browser $second) {
//            $first->visit('/chat')
//                ->waitForText('Waiting for a friend...');
//            $second->visit('/chat')
//                ->assertSee('Waiting for a friend...')
//                ->click('#call-user');
//            $first->assertSee('End Call');
//            $second->assertSee('End Call');
//        });
//    }
//
//    public function testWhenUserEndsCall ()
//    {
//
//    }
//
//    public function testLocalVideoIsShown ()
//    {
//
//    }
}
