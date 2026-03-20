@if($questions->count())

<form>

<div style="margin-bottom: 15px; display: flex; gap: 10px;">
    <input type="text" id="questionsSearchInput" class="form-control" placeholder="Search questions by text..." style="flex: 1;">
    <select id="difficultyFilter" class="form-control" style="width: 150px;">
        <option value="">All Difficulties</option>
        <option value="easy">Easy</option>
        <option value="medium">Medium</option>
        <option value="hard">Hard</option>
    </select>
</div>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th width="50">
<input type="checkbox" id="selectAll">
</th>

<th>Question</th>

<th style="width: 100px;">Difficulty</th>

</tr>

</thead>

<tbody id="questionsBody">

@foreach($questions as $question)

<tr>

<td>
<input type="checkbox" class="question-checkbox" value="{{ $question->id }}" data-difficulty="{{ strtolower($question->difficultLevel->name ?? 'easy') }}">
</td>

<td>
{{ $question->question }}
</td>

<td>
{{ $question->difficultLevel->name ?? 'Easy' }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>
  {{-- <button type="button" class="btn btn-success" onclick="assignQuestions()">Assign</button> --}}

</form>

@else

<div class="alert alert-warning">
No Questions Found
</div>

@endif