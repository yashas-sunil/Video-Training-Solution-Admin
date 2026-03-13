<?php

namespace App;

use App\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScormPackage extends Model
{
    use SoftDeletes;
     protected $guarded = ['id'];
    const ACTIVE = 1;
    const INACTIVE = 0;
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

    public function batches()
{
    return $this->belongsToMany(
        Batch::class,
        'batch_course',
        'scorm_package_id',
        'batch_id'
    );
}

}
