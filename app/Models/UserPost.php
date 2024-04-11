<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class UserPost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'UserPost';
    protected $primaryKey = 'WID';

    protected $fillable = [
        'Title', 'Article', 'ItemLink', 'ItemIMG', 'PostTime', 'ChangeTime', 'ConcessionStart', 'ConcessionEnd', 'ReportTimes','product_tag', 'location_tag', 'UID', 
    ];


        public $timestamps = false;

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UID', 'id');
    }
}
