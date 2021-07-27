<?php

namespace Tests\Feature;

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
}
