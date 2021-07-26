<?php

namespace App;

class TestModel extends BaseModel
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

    public string $test;

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
