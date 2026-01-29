<?php

namespace App;

use App\Audio;
use App\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Concept extends Model
{
     const ACTIVE=1;
    const INACTIVE=0;
    public function insertConcept($concept, $conceptNote){
        $conceptObject=new Concept();
        $conceptObject->name=$concept;
        $conceptObject->concept_note=$conceptNote;
        $conceptObject->status=Concept::ACTIVE;
        $conceptObject->created_by=Auth::id();
        $conceptObject->updated_by=Auth::id();
        $conceptObject->save();
        if(!empty($conceptObject->id)) {
            return $conceptObject->id;
        } else {
        	return 0;
        }
    }

    public function searchMaxConcept(){
        return $concept=Concept::max('id');
    }
    public function bulkConceptUpload($concept_insert_data){
        return $result = Concept::insert($concept_insert_data);
    }
    public function audio(){
        return $this->belongsTo(Audio::class,'concept_id', 'id');
    }
    public function conceptByTopicId($topic,$concept): mixed
    {
        $conceptAudioValue = Audio::whereIn('topic_id', $topic)
                                ->where('flash_card_name', $concept)
                                // ->where('status', Audio::ACTIVE)
                                ->first();

        if (!empty($conceptAudioValue->id)) {
            return $conceptAudioValue->id;
        } else {
            $conceptTopicValue = Audio::whereHas('topic', function($q) use ($topic, $concept){
                $q->where('id', $topic);
                $q->where('name', $concept);
                $q->where('status', Topic::ACTIVE);
            })
            // ->where('status', Audio::ACTIVE)
            ->first();

            if (!empty($conceptTopicValue->id)) {
                return $conceptTopicValue->id;
            } else{
                return 0;
            }
        }
    }
    
}
