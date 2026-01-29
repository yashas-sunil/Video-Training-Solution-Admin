<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolutionImage extends Model
{
     const ACTIVE=1;
    const INACTIVE=0;
    public function searchMaxSolutionImageId(){
        return SolutionImage::max('id');
    }
    public function bulkSolutionImageUpload($solution_option) {

        return $result = SolutionImage::insert($solution_option);
    }
}
