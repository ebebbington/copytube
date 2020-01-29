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
}