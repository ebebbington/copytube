<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\BaseModel;

class CommentsModel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The comment
     *
     * @var String
     */
    public $comment;

    /**
     * Username of the comment creator
     *
     * @var String
     */
    public $author;

    /**
     * Date the comment was posted on
     *
     * @var String
     */
    public $date_posted;

    /**
     * Video title of the comment it was posted on
     *
     * @var Int
     */
    public $video_posted_on;

    /**
     * Fields to be populated
     *
     * @var array
     */
    protected $fillable = ['comment', 'author', 'date_posted', 'video_posted_on'];

    /**
     * Rules for validation
     *
     * @var array
     */
    protected $rules = [
      'title' => 'required',
      'author'    => 'required',
      'date_posted' => 'required',
      'video_posted_on' => 'required',
    ];

    public $timestamps = false;

    /**
     * @method formatDates
     *
     * @description
     * Format the dates posted for each comment into format is dd/mm/yyyy
     *
     * @param object $commentList Array of objects containing the comments retrieved from the db
     *
     * @return object The same list but with modified date formats
     */
    public function formatDates (object $commentList)
    {
        $loggingPrefix = "[CommentsModel - ".__FUNCTION__.'] ';
        for ($i = 0; $i < sizeof($commentList); $i++) {
            list($year, $month, $day) = explode('-', $commentList[$i]->date_posted);
            $formattedDate = $day . '/' . $month . '/' . $year;
            $commentList[$i]->date_posted = $formattedDate;
        }
        return $commentList;
    }
}
