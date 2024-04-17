<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAndReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'SubAndReport';
    public $timestamps = false;

    protected $fillable = [
        'UID',
        'TargetUID',
        'TargetWID',
        'ReportWID',
        'ReportMSGWID',
        'ReportContent',
    ];


    /**
     * 獲取擁有該文章的使用者。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UID', 'id');
    }
}
