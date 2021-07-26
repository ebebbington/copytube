<?php

namespace Tests\Feature;

use App\UserModel;
use DB;

class TestUtilities
{
    public static string $active = "!$.active";
    public static string $login_path = "/login";
    public static string $register_path = "/register";
    public static string $home_path = "/home";
    public static string $video_path_with_query = "/video?requestedVideo=Something+More";
    public static string $video_path = "/video";
    public static string $video_comment_path = "/video/comment";
    public static string $validUsername = "TestUsername";
    public static string $validEmail = "TestEmail@hotmail.com";
    public static string $validPassword = "Welcome1";
    public static string $validProfilePicture = "img/sample.jpg";
    public static array $invalidPasswords = [
        "testpassword1", // must include caps
        "TESTPASSWORD1", // must // include lowercase
        "testPassword", // must include number
        "testPas", // must be min len of 8
    ];

    public static function removeTestCommentsInDB(int $userId = null)
    {
        if ($userId) {
            DB::table("comments")
                ->where("id", "=", $userId)
                ->delete();
        } else {
            $TestUtilities = new TestUtilities();
            DB::table("comments")
                ->where("author", "=", $TestUtilities::$validUsername)
                ->delete();
        }
    }

    public static function createTestUserInDb(array $overrides = [])
    {
        $TestUtilities = new TestUtilities();
        $UserModel = new UserModel();
        $data = [
            "username" => isset($overrides["username"])
                ? $overrides["username"]
                : $TestUtilities::$validUsername,
            "email_address" => isset($overrides["email_address"])
                ? $overrides["email_address"]
                : $TestUtilities::$validEmail,
            "password" => isset($overrides["password"])
                ? $overrides["password"]
                : $UserModel::generateHash($TestUtilities::$validPassword),
            "login_attempts" => isset($overrides["login_attempts"])
                ? $overrides["login_attempts"]
                : 3,
            "logged_in" => isset($overrides["logged_in"])
                ? $overrides["logged_in"]
                : 1,
            "recover_token" => isset($overrides["recover_token"])
                ? $overrides["recover_token"]
                : "",
            "profile_picture" => isset($overrides["profile_picture"])
                ? $overrides["profile_picture"]
                : "",
        ];
        return DB::table("users")->insertGetId($data); // userId
    }

    public static function createTestCommentInDb($user): int
    {
        $data = [
            "comment" => "TEST COMMENT FROM DUSK",
            "author" => $user->username,
            "date_posted" => "2020-09-04",
            "video_posted_on" => "Something More",
            "user_id" => $user->id,
        ];
        return DB::table("comments")->insertGetId($data); // commentId
    }

    public static function removeTestUsersInDb(array $query = [])
    {
        if (isset($query) && count($query) >= 1) {
            DB::table("users")
                ->where($query)
                ->delete();
        } else {
            $TestUtilities = new TestUtilities();
            DB::table("users")
                ->where(["username" => $TestUtilities::$validUsername])
                ->delete();
        }
    }

    public static function getTestUserInDb(int $userId = null)
    {
        $TestUtilities = new TestUtilities();
        if ($userId) {
            return DB::table("users")
                ->where("id", "=", $userId)
                ->first();
        }
        return DB::table("users")
            ->where("email_address", "=", $TestUtilities::$validEmail)
            ->first();
    }
}
