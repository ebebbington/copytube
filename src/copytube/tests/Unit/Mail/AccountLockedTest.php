<?php

namespace Tests\Unit\Mail;

use App\Mail\AccountLocked;
use Illuminate\Support\Facades\Mail;
use Mockery;
use PHPUnit\Framework\TestCase;

class AccountLockedTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testEmailIsSent ()
    {
        Mail::to('edward.bebbington@intercity.technology')->send(new AccountLocked(
            'Test Email',
            'Generated from unit tests'));
    }
}
