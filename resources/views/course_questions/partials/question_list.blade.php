@if($questions->count())

<form>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th width="50">
<input type="checkbox" id="selectAll">
</th>

<th>Question</th>

</tr>

</thead>

<tbody>

@foreach($questions as $question)

<tr>

<td>
<input type="checkbox" class="question-checkbox" value="{{ $question->id }}">
</td>

<td>
{{ $question->question }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>
  <button type="button" class="btn btn-success" onclick="assignQuestions()">Assign</button>

</form>

<br>

<div class="d-flex justify-content-center">
{!! $questions->links() !!}
</div>

@else

<div class="alert alert-warning">
No Questions Found
</div>

@endif