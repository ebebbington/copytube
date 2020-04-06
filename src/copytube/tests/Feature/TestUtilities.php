<?php


namespace Tests\Feature;


use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestUtilities
{
    public static string $validUsername = 'TestUsername';
    public static string $validEmail = 'TestEmail@hotmail.com';
    public static string $validPassword = 'Welcome1';
    public static array $invalidPasswords
        = [
            'testpassword1', // must include caps
            'TESTPASSWORD1', // must // include lowercase
            'testPassword', // must include number
            'testPas', // must be min len of 8
        ];

    public static function createTestUserInDb (array $overrides = [])
    {
        $data = [
            'username' => isset($overrides['username']) ? $overrides['username'] : TestUtilities::$validUsername,
            'email_address' => isset($overrides['email_address']) ? $overrides['email_address'] : TestUtilities::$validEmail,
            'password' => isset($overrides['password']) ? $overrides['password'] : UserModel::generateHash(TestUtilities::$validPassword),
            'login_attempts' => isset($overrides['login_attempts']) ? $overrides['login_attempts'] : 3,
            'logged_in' => isset($overrides['logged_in']) ? $overrides['logged_in'] : 1
        ];
        $id = DB::table('users')->insertGetId($data);
        return $id;
    }

    public static function removeTestUsersInDb (array $query = [])
    {
        if (isset($query) && sizeof($query) >= 1)
            DB::table('users')->where($query)->delete();
        else
            DB::table('users')->where(['username' => 'TestUsername'])->delete();
    }

    public static function logUserIn (int $id)
    {
        Auth::loginUsingId($id);
    }
}
