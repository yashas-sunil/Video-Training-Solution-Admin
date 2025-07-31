<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScormPackage extends Model
{
    protected $table = 'scorm_packages';

    protected $fillable = [
        'title',
        'folder_name',
        'zip_path',
        'user_id',
         'launch_file',
         'watch_time',
         'view_limit',
    ];
}
