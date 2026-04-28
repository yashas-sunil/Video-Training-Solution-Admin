<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
    'user_id','role','module','action','message',
    'ip_address','device','browser','platform','url','http_method'
];
}
