<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public $username;

    public $email_address;

    public $password;

    public $logged_in;
    
    public $login_attempts;

    protected $fillable = ['username', 'email_address', 'password', 'logged_in', 'login_attempts'];
    //

    // public function __construct (array $post= array())
    // {
    //     // $this->username = $post['username'];
    //     // $this->email = $post['email'];
    //     // $this->password = $post['password'];
    //     // $this->logged_in = 1;
    //     // $this->login_attempts = 3;
    // }

    public function doesExist ($email, $passwordHash)
    {

    }
}
