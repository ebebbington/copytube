<?php

namespace App;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Base
{
    use HasFactory;

    const USER_BY_EMAIL_CACHE_KEY = "db:users:email_address=";

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "users";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Fields to be populated
     *
     * @var array
     */
    protected $fillable = [
        "username",
        "email_address",
        "password",
        "logged_in",
        "login_attempts",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ["password"];

    /**
     * Rules for validation
     *
     * @var array
     */
    public static $rules = [
        "username" => "required",
        "email" => "required|email",
        "password" => "required|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/",
        "profile_picture" => ["required", "regex:/.*\.(jpg|jpeg|png)\b/"],
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, "user_id", "id");
    }

    /**
     * @param $rawPassword
     *
     * @return string
     */
    public static function generateHash($rawPassword)
    {
        return Hash::make($rawPassword, [
            "rounds" => 12,
        ]);
    }
}
