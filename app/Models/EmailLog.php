<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog  extends Model
{
    protected $table = 'email_logs';
     protected $fillable = [
        'user_id',
        'email',
        'subject',
        'body',
        'status',
        'error_message',
        'cc',
        'bcc'
    ];
}