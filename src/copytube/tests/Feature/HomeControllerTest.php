<?php

namespace Tests\Feature;

use App\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function testGetWithoutAuth()
    {
        Auth::logout();
        $response = $this->get('/home');
        $response->assertStatus(302);
        $response = $this->get('/');
        $response->assertStatus(302);
    }

    public function testGetWithAuthWithInvalidQuery ()
    {
        $id = TestUtilities::createTestUserInDb(['logged_in' => 0]);
        TestUtilities::logUserIn($id);
        // make request with title but doesnt exist
        $response = $this->get('/home?requestedVideo=Idontexist');
        $response->assertStatus(404);
        TestUtilities::removeTestUsersInDb();
    }

    public function testGetWithAuthWithNoQuery ()
    {
        Cache::flush();
        // create user
        $id = TestUtilities::createTestUserInDb(['logged_in' => 0]);
        // Auth user
        TestUtilities::logUserIn($id);
        // Make request with no video request
        $response = $this->get('/home');
        // Assert the view
        var_dump($response);
        //$response->assertViewIs('Home');
        // Assert the status
        $response->assertStatus(200);
        // assert the data sent back to view
        $content = $response->getOriginalContent();
        $data = $content->getData();
        $this->assertEquals('Home', $data['title']); // defaults to something more
        $this->assertEquals('TestUsername', $data['username']);
        $this->assertEquals('Something More', $data['mainVideo']->title);
        $this->assertEquals('2', sizeof($data['rabbitHoleVideos']));
        // Shouldn't be the main video
        foreach ($data['rabbitHoleVideos'] as $vid) {
            $this->assertEquals(true, $vid->title !== 'Something More');
        }
        $this->assertEquals(9, sizeof($data['comments']));
        foreach ($data['comments'] as $comment) {
            $this->assertEquals(true, $comment->video_posted_on === 'Something More');
        }
        $this->assertEquals(true, $data['email'] === 'TestEmail@hotmail.com');
        TestUtilities::removeTestUsersInDb();
    }

    public function testGetWithAuthWithQuery ()
    {
        Cache::flush();
        $id = TestUtilities::createTestUserInDb();
        TestUtilities::logUserIn($id);
        // make request with correct title
        $response = $this->get('/home?requestedVideo=Lava Sample');
        // Assert the view
        $response->assertViewIs('home');
        // Assert the status
        $response->assertStatus(200);
        // assert the data sent back to view
        $content = $response->getOriginalContent();
        $data = $content->getData();
        $this->assertEquals('Home', $data['title']); // defaults to something more
        $this->assertEquals('TestUsername', $data['username']);
        $this->assertEquals('Lava Sample', $data['mainVideo']->title);
        $this->assertEquals('2', sizeof($data['rabbitHoleVideos']));
        // Shouldn't be the main video
        foreach ($data['rabbitHoleVideos'] as $vid) {
            $this->assertEquals(true, $vid->title !== 'Lava Sample');
        }
        $this->assertEquals(3, sizeof($data['comments']));
        foreach ($data['comments'] as $comment) {
            $this->assertEquals(true, $comment->video_posted_on === 'Lava Sample');
        }
        $this->assertEquals(true, $data['email'] === 'TestEmail@hotmail.com');
        TestUtilities::removeTestUsersInDb();
    }
}
