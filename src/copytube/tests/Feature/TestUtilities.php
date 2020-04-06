<?php


namespace Tests\Feature;


use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestUtilities
{
    public static function createValidTestUserInDb (array $overrides = [])
    {
        $data = [
            'username' => isset($overrides['username']) ? $overrides['username'] : 'TestUsername',
            'email_address' => isset($overrides['email_address']) ? $overrides['email_address'] : 'TestEmail@hotmail.com',
            'password' => isset($overrides['password']) ? $overrides['password'] : UserModel::generateHash('TestPassword1'),
            'login_attempts' => isset($overrides['login_attempts']) ? $overrides['login_attempts'] : 3,
            'logged_in' => isset($overrides['logged_in']) ? $overrides['logged_in'] : 1
        ];
        $id = DB::table('users')->insertGetId($data);
        return $id;
    }

    public static function removeTestUser (array $query = [])
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
