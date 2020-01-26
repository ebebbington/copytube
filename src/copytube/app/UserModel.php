<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserModel extends Model
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

    /**
     * Fields to be populated
     *
     * @var array
     */
    protected $fillable = ['username', 'email_address', 'password', 'logged_in', 'login_attempts'];

    /**
     * Rules for validation
     *
     * @var array
     */
    private static $rules = [
      'username' => 'required',
      'email'    => 'required|email',
      'password' => 'required|regex:/[0-9a-zA-Z]{8,}/',
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
        $result = UserModel::where('email_address', $email)->first();

        return $result ? true : false;
    }

    /**
     * Add a new user to the database
     *
     * @param $username
     * @param $email
     * @param $hash
     *
     * @return mixed
     */
    public function createUser(string $username, string $email, string $hash)
    {
        Log::debug('Going to validate input');
        Log::debug('Saving the new user...');
        $user = $this->create([
          'username'       => $username,
          'email_address'  => $email,
          'password'       => $hash,
          'logged_in'      => 1,
          'login_attempts' => 3,
        ]);

        return $user;
    }

    /**
     * Validate User register credentials
     *
     * @param {Array} $registerData containing all register field values
     *
     * @return bool
     */
    public static function validate(array $registerData)
    {
        $validator = Validator::make($registerData, static::$rules);
        return $validator->fails();
    }

    public static function getAllUsers ()
    {
      $result = UserModel::get();
      return $result;
    }
}
