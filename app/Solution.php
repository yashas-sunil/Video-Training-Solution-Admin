<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
     const ACTIVE=1;
    const INACTIVE=0;
    public function searchMaxSolutionId(){
        return Solution::max('id');
    }
    public function bulkSolutionsUpload($solution_insert_data) {

        return $result = Solution::insert($solution_insert_data);
    }
    public function concept()
    {
        return $this->hasOne(Concept::class, 'questions_id', 'id');
    }
    public function solutionImages(){
        return $this->hasMany(SolutionImage::class,'solution_id','id');
    }
}
