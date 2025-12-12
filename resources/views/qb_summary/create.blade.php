@extends('adminlte::page')

@section('content')
<style>
  .error {
    color: #FF0000;
  }
</style>

<div class="container-fluid pt-3">

  <div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Question Bank/Summary</h3>
    </div>
    <div class="card-body">
      <form method="POST" id="question_bank" action="{{ route('qb.summary.store') }}" enctype="multipart/form-data" name="question_bank">
        @csrf
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="firstname">Question Bank Name *</label>
              <input type="text" maxlength="88" class="form-control" value="{{old('name')}}" required id="name" name="name" placeholder="Enter Question Bank Name">
            </div>
            <div class="form-group">
              <label for="fileupload">Choose File *</label>
              <div class="custom-file">
                <input type="file" class="form-control @error('fileupload') is-invalid @enderror" name="fileupload" id="fileupload" required>
                <label class="custom-file-label" for="fileupload">Choose file (Must be .zip) *</label>
              </div>
              @error('fileupload')
                <span class="error">{{ $message }}</span>
              @enderror
            </div>
            <input type="hidden" name="category" value="1" id="new">
                <div class="form-group ">
                    <label for="status">Active/Inactive</label>
                    <input type="checkbox" id="status" name="status" >
                </div>
            <button type="submit" class="btn btn-primary width_btn" id="submit">Submit</button>
          </div>
          <div class="col-md-3">
          </div>
          <div class="col-md-3">
            <a href="/Documents/excel/new_excel.zip" download class="btn btn-primary form-control" >Download Sample Excel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
@push('third_party_scripts')

<script type="text/javascript">
  $(function() {
    $('#question_bank').validate({
      onsubmit:false,
      rules:{
        name:{
          required:true,
        },
        fileupload:{
          required:true,
        },
        category:{
          required:true,
        },
        languages_id:{
          required:true,
        }

      },
      messages: {
        name: {
          required: 'Enter question bank name'
        },
        fileupload: {
          required: 'Choose a file to upload',
        },
      }
    });
  
    $('#submit').click(function(event) {

      let isValid = $('#question_bank').valid();
      if (isValid) {

        return true;
      } else {
        event.preventDefault();

        return false;
      }


    });
    $('#fileupload').change(function(e){
      var fileName = e.target.files[0].name;
      $(this).next('.custom-file-label').html(fileName);
    });
  });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $('input[type="file"]').change(function(e){
            var fileName = e.target.files[0].name;
           $('.browerfilename').val(fileName);
        });
    });
</script>
<script>
  $("#fileupload").change(function(){
    var fileName = $(this).val().split("\\").pop();
    if (fileName) {
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    } else {
        $(this).siblings(".custom-file-label").removeClass("selected").html('Choose file (Must be .zip) *');
    }
});
</script>
@endpush