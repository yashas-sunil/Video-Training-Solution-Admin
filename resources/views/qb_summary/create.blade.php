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
        <h3 class="card-title">Create Question Bank / Summary</h3>
    </div>

    <div class="card-body">
      <form 
        method="POST" 
        id="question_bank" 
        action="{{ route('qb.summary.store') }}" 
        enctype="multipart/form-data"
      >
        @csrf

        <div class="row">
          <div class="col-md-6">

            {{-- Question Bank Name --}}
            <div class="form-group">
              <label for="name">Question Bank Name *</label>
              <input 
                type="text" 
                class="form-control" 
                value="{{ old('name') }}" 
                required 
                id="name" 
                name="name"
                placeholder="Enter Question Bank Name"
              >
            </div>

            {{-- ZIP File Upload (SIMPLE WAY – PERFECT) --}}
            <div class="form-group">
              <label for="fileupload">Choose ZIP File *</label>

              <input 
                type="file"
                class="form-control @error('fileupload') is-invalid @enderror"
                name="fileupload"
                id="fileupload"
                accept=".zip"
                required
              >

              @error('fileupload')
                <span class="error">{{ $message }}</span>
              @enderror
            </div>

            {{-- Hidden category --}}
            <input type="hidden" name="category" value="1">

            {{-- Status --}}
            <div class="form-group">
              <label>Active / Inactive</label><br>
              <input type="checkbox" id="status" name="status">
            </div>
            
             <a href="{{ route('question.bank') }}" class="btn btn-secondary">
                                Back
                            </a>
                    {{-- Submit --}}
             <button id="submitBtn" type="submit" class="btn btn-success">
                                Save
                            </button>

          </div>

          <div class="col-md-3"></div>

          <div class="col-md-3">
            <a href="/Documents/excel/new_excel.zip" download class="btn btn-primary form-control">
              Download Sample Excel
            </a>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection


@push('third_party_scripts')

<script>
$(function() {

  // Validation
  $('#question_bank').validate({
    rules:{
      name:{ required:true },
      fileupload:{ required:true }
    },
    messages: {
      name: { required: 'Enter question bank name' },
      fileupload: { required: 'Choose a ZIP file to upload' }
    }
  });

});
</script>

@endpush
