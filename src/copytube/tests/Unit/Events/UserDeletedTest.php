<?php

namespace Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\UserDeleted;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserDeletedTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testEventFired()
    {
        $Event = new Event();
        $Event::fake();
        $user = User::factory()->create();
        // Send event
        $Event::dispatch(new UserDeleted($user["id"]));
        $Event::assertDispatched(UserDeleted::class, 1);
    }
}
