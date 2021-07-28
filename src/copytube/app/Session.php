<?php

namespace App;

class Session extends Base
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

    public function user()
    {
        return $this->hasOne(User::class, "id", "user_id");
    }
}
