@extends('adminlte::page')

@section('title', 'Assign Questions To Course')

@section('content')

<div class="container-fluid">

<div class="card card-primary">

<div class="card-header">
<h4 class="card-title">Assign Questions To Course</h4>
</div>

<div class="card-body">

<div class="row">

<!-- Course -->
<div class="col-md-4">
<label><b>Course</b></label>
<select id="course_id" class="form-control">
<option value="">Select Course</option>
@foreach($courses as $course)
<option value="{{ $course->id }}">{{ $course->title }}</option>
@endforeach
</select>
</div>

<!-- Question Bank -->
<div class="col-md-4">
<label><b>Question Bank</b></label>
<select id="bank_id" class="form-control">
<option value="">Select Bank</option>
@foreach($banks as $bank)
<option value="{{ $bank->id }}">{{ $bank->name }}</option>
@endforeach
</select>
</div>

<!-- Difficulty -->
<div class="col-md-4">
<label><b>Difficulty Level</b></label>

<select id="difficulty_level" class="form-control">

<option value="">Select Level</option>

@foreach($levels as $level)

<option value="{{ $level->id }}">
    {{ $level->name }}
</option>

@endforeach

</select>

</div>

</div>

<br>

<div class="text-right">
<button class="btn btn-primary" onclick="filterQuestions()">
<i class="fas fa-filter"></i> Filter Questions
</button>
</div>

<hr>

<div id="question-list"></div>

</div>

</div>

</div>

@endsection


@section('js')
<script>

let selectedQuestions = [];
let selectAllGlobal = false;

/* FILTER QUESTIONS */

function filterQuestions(page = 1){

let course_id = document.getElementById('course_id').value;
let bank_id = document.getElementById('bank_id').value;
let difficulty = document.getElementById('difficulty_level').value;

if(course_id === ""){
alert("Please select course");
return;
}

if(bank_id === ""){
alert("Please select question bank");
return;
}

fetch(`{{ route('course.questions.filter') }}?course_id=${course_id}&bank_id=${bank_id}&difficulty=${difficulty}&page=${page}`)
.then(res => res.text())
.then(data => {

document.getElementById('question-list').innerHTML = data;


/* RESTORE CHECKBOX STATE */

document.querySelectorAll('.question-checkbox').forEach(cb => {

if(selectAllGlobal){
cb.checked = true;

if(!selectedQuestions.includes(cb.value)){
selectedQuestions.push(cb.value);
}

}else{

if(selectedQuestions.includes(cb.value)){
cb.checked = true;
}

}

});


/* SELECT ALL */

let selectAll = document.getElementById('selectAll');

if(selectAll){

if(selectAllGlobal){
selectAll.checked = true;
}

selectAll.addEventListener('change', function(){

selectAllGlobal = this.checked;

document.querySelectorAll('.question-checkbox').forEach(cb => {

cb.checked = this.checked;

let id = cb.value;

if(this.checked){

if(!selectedQuestions.includes(id)){
selectedQuestions.push(id);
}

}else{

selectedQuestions = [];

}

});

});

}

});

}


/* CHECKBOX TRACKING */

document.addEventListener('change', function(e){

if(e.target.classList.contains('question-checkbox')){

let id = e.target.value;

if(e.target.checked){

if(!selectedQuestions.includes(id)){
selectedQuestions.push(id);
}

}else{

selectedQuestions = selectedQuestions.filter(q => q != id);
selectAllGlobal = false;

}

}

});


/* AJAX PAGINATION */

document.addEventListener('click', function(e){

if(e.target.closest('.pagination a')){

e.preventDefault();

let url = e.target.closest('a').getAttribute('href');

let page = url.split('page=')[1];

filterQuestions(page);

}

});


/* ASSIGN QUESTIONS */

function assignQuestions(){

if(selectedQuestions.length === 0){

alert("Please select at least one question");

return;

}

let course_id = document.getElementById('course_id').value;

fetch(`{{ route('course.questions.assign') }}`, {

method: "POST",

headers: {

"Content-Type": "application/json",

"X-CSRF-TOKEN": "{{ csrf_token() }}"

},

body: JSON.stringify({

course_id: course_id,
question_ids: selectedQuestions

})

})

.then(res => res.json())

.then(data => {

alert("Questions Assigned Successfully");

selectedQuestions = [];
selectAllGlobal = false;

filterQuestions();

});

}

</script>

@endsection