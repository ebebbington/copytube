<?php

namespace App;

class Test extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "test";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    public $timestamps = false;

    /**
     * Fields to be populated
     *
     * @var array
     */
    protected $fillable = ["test"];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [""];

    /**
     * Rules for validation
     *
     * @var array
     */
    protected $rules = [
        "test" => "required",
    ];
}
