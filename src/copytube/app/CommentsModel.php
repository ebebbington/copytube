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

    public $user_id;

    /**
     * Fields to be populated
     *
     * @var array
     */
    protected $fillable = ['comment', 'author', 'date_posted', 'video_posted_on', 'user_id'];

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
        'user_id' => 'required'
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
            $commentList[$i]->date_posted = $this->convertDate($commentList[$i]->date_posted);
        }
        return $commentList;
    }

    public function convertDate (string $date)
    {
        // expected: "yyyy-mm-dd"
        list($year, $month, $day) = explode('-', $date);
        $formattedDate = $day . '/' . $month . '/' . $year;
        return $formattedDate;
    }

    /**
     * @method getAllByVideoTitle
     *
     * @description
     * Gets al comments that match the passed in title. Also formats the dates for you
     *
     * @param string $videoTitle
     *
     * @return array|bool|object
     *
     * @example
     * $comments = $CommentsModel->getAllByVideoTitle('Something More'); array or false
     */
    public function getAllByVideoTitleAndJoinProfilePicture (string $videoTitle)
    {
        $query = [
            'select' => ['comments.*', 'users.profile_picture'],
            'join' => ['users', 'comments.user_id', '=', 'users.id'],
            'where' => "video_posted_on = '$videoTitle'",
            'limit' => -1,
            'orderBy' => ['column' => 'date_posted', 'direction' => 'DESC']
        ];
        $cacheKey = "db:comments:videoTitle=".$videoTitle;
        $comments = $this->SelectQuery($query, $cacheKey);
        $comments = $this->formatDates($comments);
        return $comments;
    }

    public function createComment (array $data)
    {
        $cacheKey = "db:comments:videoTitle=" . $data['video_posted_on'];
        return $this->CreateQuery($data, $cacheKey);
    }
}
