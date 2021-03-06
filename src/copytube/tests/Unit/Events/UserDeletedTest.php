<?php

namespace Tests\Unit\Events;

use App\CommentsModel;
use App\Events\CommentAdded;
use App\Jobs\ProcessNewComment;
use App\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\Feature\TestUtilities;
use Tests\TestCase;
use App\Events\UserDeleted;

class UserDeletedTest extends TestCase
{
    public function testEventFired()
    {
        $Event = new Event();
        $Event::fake();
        $userId = TestUtilities::createTestUserInDb();
        // Send event
        $Event::dispatch(new UserDeleted($userId));
        $Event::assertDispatched(UserDeleted::class, 1);
        TestUtilities::removeTestUsersInDb();
    }
}
