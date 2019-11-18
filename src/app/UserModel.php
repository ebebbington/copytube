<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * @var Int
     */
    public $login_attempts;

    /**
     * Fields to be populated
     * 
     * @var Array
     */
    protected $fillable = ['username', 'email_address', 'password', 'logged_in', 'login_attempts'];

    // public function __construct (array $post= array())
    // {
    //     // $this->username = $post['username'];
    //     // $this->email = $post['email'];
    //     // $this->password = $post['password'];
    //     // $this->logged_in = 1;
    //     // $this->login_attempts = 3;
    // }

    public function __construct()
    {
        Log::debug('USER MODEL');
    }

    /**
     * Check if a user exists
     * 
     * @param {string} $email The email to check by
     * 
     * @return {Object} The user object, found or not
     */
    private function exists (string $email = '')
    {
        return DB::table('users')->where('email_address', $email)->first();
    }

    /**
     * Save a user if they dont already exist
     * 
     * @param {array} $postData Holds the username, email and hashed pass
     * 
     * @return {bool} Success of the result
     */
    public function checkAndSave ($username, $email, $hash)
    {
        // first check if user exists
        $user = $this->exists($email);
        if (isset($user->username)) {
            Log::debug('User already exists');
            return ['success' => false];
        }
        // then save
        if (!isset($user->username)) {
            Log::debug('Saving the new user...');
            $this->create([
                'username' => $username,
                'email_address' => $email,
                'password' => $hash,
                'logged_in' => 1,
                'login_attempts' => 3
            ]);

            // check it saved
            $user = DB::table('users')
                ->where('email_address', $email);
            $success = isset($user->username) ? true : false;
            Log::debug(['message' => "User saved to the database", 'data' => $success]);
            return [
                'success' => $success,
                'data' => $user
            ];
        }
    }
}
