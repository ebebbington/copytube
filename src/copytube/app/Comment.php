<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Base
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "comments";

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
    protected $fillable = [
        "comment",
        "author",
        "date_posted",
        "video_id",
        "user_id",
    ];

    /**
     * Rules for validation
     *
     * @var array
     */
    public static $rules = [
        "comment" => "required",
        "author" => "required",
        "date_posted" => "required",
        "video_id" => "required",
        "user_id" => "required",
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class, "id", "user_id");
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
