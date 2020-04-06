<?php

namespace Tests\Feature;

use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
    public function testPostCommentWithoutAuth ()
    {
        TestUtilities::removeTestUsersInDb();
        // Run request without auth
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        $res = $this->post('/video/comment', ['comment' => 'test'], $headers);
        $res->assertStatus(302);
        $res->assertRedirect('/login');
    }

    public function testPostCommentWithAuth ()
    {
        // Auth myself
        $id = TestUtilities::createTestUserInDb(['profile_picture' => 'img/sample.jpg']);
        Auth::loginUsingId($id);
        $user = Auth::user();

        //
        // INCORRECT REQUEST
        //

        // No comment but with video title to test validation
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        $res = $this->post('/video/comment', ['videoPostedOn' => 'Something More'], $headers);
        $res->assertStatus(401);
        $res->assertJson(['success' => false, 'message' => 'The comment field is required.']);

        // No date posted but with video title to test validation
        $data = ['comment' => 'hello'];
        $data['videoPostedOn'] = 'Something More';
        $res = $this->post('/video/comment', $data, $headers);
        $res->assertStatus(401);
        $res->assertJson(['success' => false, 'message' => 'The date posted field is required.']);

        // No video posted on
        $data['datePosted'] = '2020-03-02';
        $data['videoPostedOn'] = null;
        $res = $this->post('/video/comment', $data, $headers);
        $res->assertStatus(403);
        $res->assertJson(['success' => false, 'message' => 'Some data wasn\'t provided']);

        // No video found with that title
        $data['videoPostedOn'] = 'I Dont Exist';
        $res = $this->post('/video/comment',  $data, $headers);
        $res->assertStatus(404);
        $res->assertJson(['success' => false, 'message' => 'Video does not exist']);

        //
        // CORRECT REQUEST
        //

        // Run request with correct data
        $data['videoPostedOn'] = 'Something More';
        $res = $this->post('/video/comment', $data, $headers);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        // Remove all comments and user
        DB::table('comments')->where('user_id', '=', $id)->delete();
        TestUtilities::removeTestUsersInDb();

        // TODO :: Listen for the message on the channel using redis. Needs a queue listener

    }

    public function testAutocomplete ()
    {
        // Run request without auth
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        $res = $this->get('/video?title=Hello', $headers);
        $res->assertStatus(302);

        // Auth myself
        $id = TestUtilities::createTestUserInDb();
        Auth::loginUsingId($id);

        // send requestResponds with empty array when no title is passed in e.g. no value in search bar
        $res = $this->get('/video?title=', $headers);
        $res->assertStatus(200);
        $res->assertJson(['success' => true, 'data' => []]);

        // Send request with correct titles and Finds titles based on it
        $res = $this->get('/video?title=S', $headers); // should match lava sample and something more
        $res->assertStatus(200);
        $res->assertJson(['success' => true, 'data' => ['Something More', 'Lava Sample']]);

        // make request with an incorrect title that will respond with no data
        $res = $this->get('/video?title=I Dont exist', $headers);
        $res->assertStatus(200);
        $res->assertJson(['success' => true, 'data' => []]);

        TestUtilities::removeTestUsersInDb();
    }

}
