<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\BaseModel;
use Illuminate\Support\Str;

class UserModel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public $id;

    /**
     * Username of the user
     *
     * @var String
     */
    public $username;

    /**
     * Email address for user
     *
     * @var String
     */
    public $email_address;

    /**
     * Hashed password of user
     *
     * @var String
     */
    public $password;

    /**
     * Logged in value (0 = logged in)
     *
     * @var Int
     */
    public $logged_in;

    /**
     * Number of login attempts for the user
     *
     * @var int
     */
    public $login_attempts;

    public $recover_token;

    /**
     * The path for the profile picture. Can be "sample.jpg" for default, or "<id>/filename".
     * The current directory where these lie is "/public/img/", so an example value: "img/<id>/<filename>". see register controller
     *
     * @var string
     */
    public $profile_picture;

    /**
     * Fields to be populated
     *
     * @var array
     */
    protected $fillable = ['username', 'email_address', 'password', 'logged_in', 'login_attempts'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Rules for validation
     *
     * @var array
     */
    protected $rules = [
      'username' => 'required',
      'email'    => 'required|email',
      'password' => 'required|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/',
        'profile_picture' => ['required', 'regex:/.*\.(jpg|jpeg|png)\b/']
    ];

    /**
     * Check a user exists by a given email address
     *
     * @param {String} $email Email of user to find
     *
     * @return bool
     */
    public static function exists(string $email): bool
    {
        $loggingPrefix = "[UserModel - ".__FUNCTION__.'] ';
        $result = UserModel::where('email_address', $email)->first();
        Log::info($loggingPrefix . 'User exists: ' . $result ? true : false);
        return $result ? true : false;
    }

//    public function logout (int $id): void
//    {
//        $loggingPrefix = "[UserModel - ".__FUNCTION__.'] ';
//        session(['user' => null]);
//        $this->UpdateQuery(['id' => $id], ['logged_in' => 1]);
//        $SessionModel = new SessionModel;
//        $SessionModel->DeleteQuery(['user_id' => $id]);
//    }

    /**
     * @method getByEmail
     *
     * @description
     * Get a user from the data base by the email. This should only be used when the user isn't authed, e.g. logging in
     *
     * @param string $email
     *
     * @return array|bool|object
     *
     * @example
     * $user = $UserModel->getByEmail('edward.bebbington...')' // object or false
     */
    public function getByEmail (string $email)
    {
        $query = [
            'where' => "email_address = '$email'",
            'limit' => 1
        ];
        $cacheKey = 'db:users:email_address='.$email;
        $user = $this->SelectQuery($query, $cacheKey);
        return $user;
    }

    /**
     * @method lockAccount
     *
     * @description
     * Lock the users account
     *
     * @param mixed  $id    Users id
     * @param string $email Users email
     *
     * @return bool|string
     *
     * @example
     * $token = $UserModel->lockAccount($user->id, $user->email_address); // token if success, false if failed
     */
    public function lockAccount ($id, string $email)
    {
        $recoverToken = Str::random(32);
        $success = $this->UpdateQuery(['id' => $id], ['recover_token' => $recoverToken], 'db:users:email_address='.$email);
        return $recoverToken;
    }

    /**
     * @method updateLoggedIn
     *
     * @description
     * Update the users logged in value. 1 = not logged in, 0 = logged in
     *
     * @param int    $loggedInValue
     * @param string $email
     *
     * @return bool success
     *
     * @example
     * // Log user in
     * $UserModel->updateLoggedIn(0, $user->email);
     */
    public function updateLoggedIn (int $loggedInValue, string $email)
    {
        $query = [
            'email_address' => $email
        ];
        $updateData = [
            'logged_in' => $loggedInValue
        ];
        $cacheKey = 'db:users:email_address='.$email;
        $updated = $this->UpdateQuery($query, $updateData, $cacheKey);
        return $updated;
    }

    /**
     * @method updateLoginAttempts
     *
     * @description
     * Update the users login attempts. 3 is max, 0 means account is to be locked
     *
     * @param string $email
     * @param int    $loginAttempts
     */
    public function updateLoginAttempts (string $email, int $loginAttempts)
    {
        $query = [
            'email_address' => $email
        ];
        $updateData = [
            'login_attempts' => $loginAttempts
        ];
        $cacheKey = 'db:users:email_address='.$email;
        $this->UpdateQuery($query, $updateData, $cacheKey);
    }

    public function getByToken ($token)
    {
        $query = [
            'where' => "recover_token = '$token'",
            'limit' => 1
        ];
        $cacheKey = "db:users:recover_token=$token'";
        $user = $this->SelectQuery($query, $cacheKey);
        return $user;
    }

    /**
     * @method updateAfterRecover
     *
     * @description
     * Update the users data after recovering the account
     *
     * @param string $email
     * @param string $rawPassword
     */
    public function updateAfterRecover (string $email, string $rawPassword)
    {
        $query = [
            'email_address' => $email
        ];
        $updateData = [
            'password' => UserModel::generateHash($rawPassword),
            'login_attempts' => 3,
            'recover_token' => null
        ];
        $this->UpdateQuery($query, $updateData);
    }

    /**
     * @param $rawPassword
     *
     * @return string
     */
    public static function generateHash ($rawPassword)
    {
        $hash = Hash::make($rawPassword, [
            'rounds' => 12,
        ]);
        return $hash;
    }
}
