<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\BaseModel;

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

    public function logout (int $id): void
    {
        session(['user' => null]);
        $this->UpdateQuery(['id' => $id], ['logged_in' => 1]);
        $SessionModel = new SessionModel;
        $SessionModel->DeleteQuery(['user_id' => $id]);
    }
}
