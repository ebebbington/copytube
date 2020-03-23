<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\BaseModel;

class VideosModel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'videos';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

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
    public function getVideoByTitle (string $videoTitle)
    {
        $loggingPrefix = '[VideosModel -' . __FUNCTION__ . '] ';
        Log::info($loggingPrefix . 'Getting video by ' . $videoTitle);
        $query = [
            'where' => "title = '$videoTitle'",
            'limit' => 1,
        ];
        $cacheKey = 'db:videos:title=' . $videoTitle;
        $video = $this->SelectQuery($query, $cacheKey);
        return $video;
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
    public function getRabbitHoleVideos (string $videoToIgnore)
    {
        $loggingPrefix = '[VideosModel -' . __FUNCTION__ . '] ';
        Log::info($loggingPrefix . 'Getting videos by title !== ' . $videoToIgnore);
        $query = [
            'where' => "title != '$videoToIgnore'",
            'limit' => 2
        ];
        $cacheKey = 'db:videos:title!='.$videoToIgnore.'&limit=2';
        $rabbitHoleVideos = $this->SelectQuery($query, $cacheKey);
        return $rabbitHoleVideos;
    }
}
