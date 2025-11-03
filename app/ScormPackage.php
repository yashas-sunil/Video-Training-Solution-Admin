<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ScormPackage extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
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
