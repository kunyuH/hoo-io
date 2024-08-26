<?php

namespace hoo\io\common\Models;

use App\Models\Traits\DateFormat;
use App\Models\Traits\IdeHelpers;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use DateFormat, IdeHelpers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
