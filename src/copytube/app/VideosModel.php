<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideosModel extends BaseModel
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
     * The video title
     *
     * @var String
     */
    public $title;

    /**
     * Description for the video
     *
     * @var String
     */
    public $description;

    /**
     * Video path/name
     *
     * @var String
     */
    public $src;

    /**
     * Poster to be associated with the video
     *
     * @var Int
     */
    public $poster;

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
        return $this->hasMany(CommentsModel::class, "video_id", "id");
    }

    private function getLoggingPrefix(string $functionName): string
    {
        return "[VideosModel - " . $functionName . "] ";
    }

    /**
     * @method getVideoByTitle
     *
     * @param string $videoTitle Title if the video to get
     *
     * @return array|bool|object
     *
     * @example
     * $video = $VideosModel->getVideoByTitle('Something More'); // object or false
     */
    public function getVideoByTitle(string $videoTitle)
    {
        $loggingPrefix = $this->getLoggingPrefix(__FUNCTION__);
        Log::info($loggingPrefix . "Getting video by " . $videoTitle);
        $query = [
            "where" => "title = '$videoTitle'",
            "limit" => 1,
        ];
        $cacheKey = "db:videos:title=" . $videoTitle;
        return $this->SelectQuery($query, $cacheKey); // $video
    }

    /**
     * @method getRabbitHoleVideos
     *
     * @description
     * Responsible for getting all rabbit hole videos. Gets 2 where title doesnt equal the one passed in
     *
     * @param string $videoToIgnore Title of video to ignore from selecting
     *
     * @return array|bool|object
     *
     * @example
     * $videos = $VideosModel->getRabbitHoleVideos('Lava Sample'); // array if > 1, object is 1, false if none
     */
    public function getRabbitHoleVideos(string $videoToIgnore)
    {
        $loggingPrefix = $this->getLoggingPrefix(__FUNCTION__);
        Log::info(
            $loggingPrefix . "Getting videos by title !== " . $videoToIgnore
        );
        $query = [
            "where" => "title != '$videoToIgnore'",
            "limit" => 2,
        ];
        $cacheKey = "db:videos:title!=" . $videoToIgnore . "&limit=2";
        return $this->SelectQuery($query, $cacheKey); // $rabbitHoleVideos
    }

    public function getVideosForHomePage()
    {
        $loggingPrefix = $this->getLoggingPrefix(__FUNCTION__);
        Log::info($loggingPrefix . "Getting all videos for home page");
        $query = [
            "limit" => 3,
        ];
        $cacheKey = "db:videos:limit=3";
        return $this->SelectQuery($query, $cacheKey); // $videeos
    }
}
