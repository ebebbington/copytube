<?php

namespace Tests\Unit;

use App\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    private string $test_username = "Test User";

    private function createTestUser($recoverToken = null)
    {
        DB::table("users")->insert([
            "username" => $this->test_username,
            "password" => "Test",
            "logged_in" => 1,
            "login_attempts" => 3,
            "email_address" => "testemail",
            "recover_token" => $recoverToken,
        ]);
    }

    private function deleteTestUser()
    {
        DB::table("users")
            ->where("username", "=", $this->test_username)
            ->delete();
    }

    public function testExistsMethod()
    {
        $UserModel = new UserModel();
        $exists = $UserModel::exists("EdwardSBebbington@hotmail.com");
        $this->assertEquals(true, $exists);
        $exists = $UserModel::exists("idontexist");
        $this->assertEquals(false, $exists);
    }

    public function testGetMyEmailMethod()
    {
        $UserModel = new UserModel();
        $user = $UserModel->getByEmail("EdwardSBebbington@hotmail.com");
        $this->assertEquals(true, isset($user) && !empty($user));
        $user = $UserModel->getByEmail("idonteixst");
        $this->assertEquals(false, !isset($user) && empty($user));
    }

    public function testLockAccountMethod()
    {
        $UserModel = new UserModel();
        $this->createTestUser();
        $user = DB::table("users")
            ->whereRaw("username = '$this->test_username'")
            ->first();
        $success = $UserModel->lockAccount($user->id, $user->email_address);
        $this->assertEquals(true, isset($success));
        //        $success = $UserModel->lockAccount('none', 'none');
        //        $this->assertEquals(false, $success);
        $this->deleteTestUser();
    }

    public function testUpdateLoginAttemps()
    {
        $this->createTestUser();
        $UserModel = new UserModel();
        $UserModel->updateLoginAttempts("testemail", 2);
        $user = DB::table("users")
            ->where("username", "=", $this->test_username)
            ->first();
        $this->assertEquals(2, $user->login_attempts);
        $this->deleteTestUser();
    }

    public function testGetByToken()
    {
        $this->createTestUser("testtoken");
        $UserModel = new UserModel();
        $user = $UserModel->getByToken("testtoken");
        $this->assertEquals(true, isset($user) && !empty($user));
        $this->deleteTestUser();
    }

    public function testUpdateAfterRecover()
    {
        $this->createTestUser("testtoken");
        $UserModel = new UserModel();
        $UserModel->updateAfterRecover("testemail", "testpassword");
        $user = DB::table("users")
            ->where("email_address", "=", "testemail")
            ->first();
        $this->assertEquals(3, $user->login_attempts);
        $this->assertEquals(null, $user->recover_token);
        $this->deleteTestUser();
    }

    public function testGenerateHashMethod()
    {
        $rawPass = "Hello";
        $UserModel = new UserModel();
        $hash = $UserModel::generateHash($rawPass);
        $Hash = new Hash();
        $this->assertEquals(true, $Hash::check($rawPass, $hash));
    }
}
