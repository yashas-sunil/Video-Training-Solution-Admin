<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionBankRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
     public function rules(Request $request)
    {

        if($request->category==1)
        {
        return [
            'name'=>'required',
            'course_id' => 'required|exists:scorm_packages,id',
            'fileupload'=>'required|max:90000',
            // 'languages_id'=>'required|integer',
            'category'=>'required|integer',
            'status'=>''

        ];
        }else{
            return [
                'name'=>'required',
                'fileupload'=>'required|max:90000',
                // 'languages_id'=>'required|integer',
                'category'=>'required|integer',
                'status'=>''
            ];
        }
    }
}
