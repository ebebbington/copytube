<?php

namespace App;


class SessionModel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "sessions";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

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
    protected $fillable = ["session_id", "user_id"];

    public $timestamps = false;

    /**
     * Rules for validation
     *
     * @var array
     */
    protected $rules = [
        "session_id" => "required",
        "user_id" => "required",
    ];
}
