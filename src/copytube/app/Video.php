<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Base
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "videos";

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
    protected $fillable = [];

    /**
     * Rules for validation
     *
     * @var array
     */
    protected $rules = [];

    public $timestamps = false;

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
