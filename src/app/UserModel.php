<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    public function checkAndSave (array $postData = array())
    {
        // first check if user exists
        $user = $this->exists($postData['email']);
        if (isset($user->username)) {
            return false;
        }
        // then save
        if (!isset($user->username)) {
            $this->create([
                'username' => $_POST['username'],
                'email_address' => $_POST['email'],
                'password' => $_POST['password'],
                'logged_in' => 1,
                'login_attempts' => 3]
            );
            return true;
        }
    }
}
