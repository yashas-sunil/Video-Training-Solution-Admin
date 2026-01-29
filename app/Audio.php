<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    const INACTIVE=0;
    const ACTIVE=1;

    protected $fillable = [
        'topic_id',
        'name',
        'audio_content',
        'flash_card_name',
        'error_message',
        'status',
    ];
    
    public function topic(){
        return $this->belongsTo(Topic::class,'topic_id','id');
    }
}
