<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'subject',
        'message',
        'body',
        'error_message',
        'cc',
        'bcc',
        'status',
    ];

}
