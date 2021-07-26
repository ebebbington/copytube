<?php

namespace App;

class CommentsModel extends BaseModel
{
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
    public $video_id;

    public $user_id;

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
    protected $rules = [
        "comment" => "required",
        "author" => "required",
        "date_posted" => "required",
        "video_id" => "required",
        "user_id" => "required",
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id");
    }

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
    public function formatDates(object $commentList)
    {
        $size = sizeof($commentList);
        for ($i = 0; $i < $size; $i++) {
            $commentList[$i]->date_posted = $this->convertDate(
                $commentList[$i]->date_posted
            );
        }
        return $commentList;
    }

    public function convertDate(string $date)
    {
        // expected: "yyyy-mm-dd"
        list($year, $month, $day) = explode("-", $date);
        return $day . "/" . $month . "/" . $year; // the formatted date
    }

    public function getAllByVideoIdJoinUserProfilePic(int $videoId)
    {
        $query = [
            "select" => ["comments.*", "users.profile_picture"],
            "join" => ["users", "comments.user_id", "=", "users.id"],
            "where" => "video_id = $videoId",
            "limit" => -1,
            "orderBy" => ["column" => "date_posted", "direction" => "DESC"],
        ];
        $cacheKey = "db:comments:videoId=" . $videoId;
        $comments = $this->SelectQuery($query, $cacheKey);
        if (!$comments) {
            return [];
        }
        return $this->formatDates($comments);
    }

    public function createComment(array $data)
    {
        $cacheKey = "db:comments:videoId=" . $data["video_id"];
        return $this->CreateQuery($data, $cacheKey);
    }
}
