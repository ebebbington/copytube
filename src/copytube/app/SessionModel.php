<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SessionModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sessions';

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
    public $session_id;

    /**
     * Hashed password of user
     *
     * @var String
     */
    public $user_id;

    /**
     * Fields to be populated
     *
     * @var array
     */
    protected $fillable = ['session_id', 'user_id'];

    public $timestamps = false;

    /**
     * Rules for validation
     *
     * @var array
     */
    private static $rules = [
      'session_id' => 'required',
      'user_id' => 'required',
    ];

    public function insert (array $data)
    {
      Log::debug('Going to create a new session');
        $session = $this->create([
            'session_id'       => $data['sessionId'],
            'user_id'       => $data['userId']
          ]);
          return $session;
    }
  }
