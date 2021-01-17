<?php

namespace Tests\Unit\Mail;

use App\Mail\AccountLocked;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class AccountLockedTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testEmailIsSent()
    {
        $Mail = new Mail();
        $Mail::fake();
        $Mail::assertNothingSent();
        $Mail::to("EdwardSBebbington@hotmail.com")->send(
            new AccountLocked("Test Email", "Generated from unit tests")
        );
        $Mail::assertSent(AccountLocked::class, function ($mail) {
            return $mail->hasTo("EdwardSBebbington@hotmail.com");
        });
        $Mail::assertSent(AccountLocked::class, 1);
    }
}
