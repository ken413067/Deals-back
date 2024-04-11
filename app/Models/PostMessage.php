<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'PostMessage';

    protected $fillable = [
        'WID',
        'UID',
        'MSGPost',
        'MSGPostTime',
        'MSGChangeTime',
        'MSGReport',
        'MSGHiding',
    ];
    
    public $timestamps = false;

    /**
     * 獲取擁有該文章的使用者。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UID', 'id');
        
    }

    public function userPost()
    {
        return $this->belongsTo(UserPost::class, 'WID', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::updating(function ($postMessage) {
            $postMessage->MSGChangeTime = now();
        });
    }

    
}
