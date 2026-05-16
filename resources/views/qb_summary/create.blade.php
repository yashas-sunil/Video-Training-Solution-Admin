@extends('adminlte::page')

@section('content')
<style>
  .error {
    color: #FF0000;
  }
  .nav-tabs .nav-link {
    font-weight: 600;
    font-size: 15px;
    padding: 10px 25px;
    color: #555;
    border: 1px solid transparent;
  }
  .nav-tabs .nav-link.active {
    color: #007bff;
    border-color: #dee2e6 #dee2e6 #fff;
  }
  .nav-tabs .nav-link:hover {
    color: #007bff;
  }
  .tab-content {
    padding-top: 20px;
  }
</style>

<div class="container-fluid pt-3">

  <div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Question Bank / Summary</h3>
    </div>

    <div class="card-body">

      {{-- Tabs Navigation --}}
      <ul class="nav nav-tabs" id="importTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="excel-tab" data-toggle="tab" href="#excel" role="tab" aria-controls="excel" aria-selected="true">
            <i class="fas fa-file-excel mr-1"></i> Excel
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pdf-tab" data-toggle="tab" href="#pdfimport" role="tab" aria-controls="pdfimport" aria-selected="false">
            <i class="fas fa-file-pdf mr-1"></i> PDF Import
          </a>
        </li>
      </ul>

      {{-- Tabs Content --}}
      <div class="tab-content" id="importTabsContent">

        {{-- ======================== TAB 1: EXCEL ======================== --}}
        <div class="tab-pane fade show active" id="excel" role="tabpanel" aria-labelledby="excel-tab">
          <form 
            method="POST" 
            id="question_bank_excel" 
            action="{{ route('qb.summary.store') }}" 
            enctype="multipart/form-data"
          >
            @csrf
            <input type="hidden" name="import_type" value="excel">
            <input type="hidden" name="category" value="1">

            <div class="row">
              <div class="col-md-6">

                {{-- Question Bank Name --}}
                <div class="form-group">
                  <label for="excel_name">Question Bank Name *</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    value="{{ old('name') }}" 
                    required 
                    id="excel_name" 
                    name="name"
                    placeholder="Enter Question Bank Name"
                  >
                </div>

                {{-- ZIP File Upload --}}
                <div class="form-group">
                  <label for="excel_fileupload">Choose ZIP File *</label>
                  <input 
                    type="file"
                    class="form-control @error('fileupload') is-invalid @enderror"
                    name="fileupload"
                    id="excel_fileupload"
                    accept=".zip,application/zip,application/x-zip-compressed"
                    required
                  >
                  @error('fileupload')
                    <span class="error">{{ $message }}</span>
                  @enderror
                </div>

                {{-- Status --}}
                <div class="form-group">
                  <label>Active / Inactive</label><br>
                  <input type="checkbox" id="excel_status" name="status">
                </div>

                <a href="{{ route('question.bank') }}" class="btn btn-secondary px-3">
                  <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <button type="submit" class="btn btn-success px-3">
                  Save <i class="fas fa-save mr-1"></i>
                </button>

              </div>

              <div class="col-md-3"></div>

              <div class="col-md-3">
                <a href="{{ route('download.sample.excel') }}" class="btn btn-primary form-control">
                  Download Sample Excel
                </a>
              </div>
            </div>

          </form>
        </div>

        {{-- ======================== TAB 2: PDF IMPORT ======================== --}}
        <div class="tab-pane fade" id="pdfimport" role="tabpanel" aria-labelledby="pdf-tab">
          <form 
            method="POST" 
            id="question_bank_pdf" 
            action="{{ route('qb.summary.store') }}" 
            enctype="multipart/form-data"
          >
            @csrf
            <input type="hidden" name="import_type" value="pdf">
            <input type="hidden" name="category" value="1">

            <div class="row">
              <div class="col-md-6">

                {{-- Row: Question Bank Name + Difficulty Level --}}
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pdf_name">Question Bank Name *</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        value="{{ old('name') }}" 
                        required 
                        id="pdf_name" 
                        name="name"
                        placeholder="Enter Question Bank Name"
                      >
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="difficulty_level">Difficulty Level</label>
                      <select class="form-control" required id="difficulty_level" name="difficulty_level">
                        <option value="">Select Difficulty Level</option>
                        <option value="1">Easy</option>
                        <option value="2">Medium</option>
                        <option value="3">Hard</option>
                      </select>
                    </div>
                  </div>
                </div>

                {{-- Row 1: Course + Subject --}}
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pdf_course">Course *</label>
                      <select 
                        class="form-control" 
                        id="pdf_course" 
                        name="course_id"
                        required
                      >
                        <option value="">Select Course</option>
                        @if(isset($courses))
                          @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title ?? $course->name }}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pdf_subject">Subject *</label>
                      <select 
                        class="form-control" 
                        id="pdf_subject" 
                        name="subject_id"
                        required
                      >
                        <option value="">Select Subject</option>
                      </select>
                    </div>
                  </div>
                </div>

                {{-- Row 2: Chapter + PDF File --}}
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pdf_chapter">Chapter *</label>
                      <select 
                        class="form-control" 
                        id="pdf_chapter" 
                        name="chapter_id"
                        required
                      >
                        <option value="">Select Chapter</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pdf_fileupload">Choose PDF File *</label>
                      <input 
                        type="file"
                        class="form-control @error('fileupload') is-invalid @enderror"
                        name="fileupload"
                        id="pdf_fileupload"
                        accept=".pdf,application/pdf"
                        required
                      >
                      @error('fileupload')
                        <span class="error">{{ $message }}</span>
                      @enderror
                    </div>
                  </div>
                </div>

                {{-- Status
                <div class="form-group">
                  <label>Active / Inactive</label><br>
                  <input type="checkbox" id="pdf_status" name="status">
                </div> --}}

                <a href="{{ route('question.bank') }}" class="btn btn-secondary px-3">
                  <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <button type="submit" class="btn btn-success px-3">
                  Save <i class="fas fa-save mr-1"></i>
                </button>

              </div>
            </div>

          </form>
        </div>

      </div>
      {{-- End tab-content --}}

    </div>
  </div>
</div>
@endsection


@push('js')

<script>
console.log('=== VANILLA JS SCRIPT LOADING ===');

// Wait for DOM to be fully loaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initDropdowns);
} else {
  initDropdowns();
}

function initDropdowns() {
  console.log('=== initDropdowns() called ===');
  
  const pdfCourse = document.getElementById('pdf_course');
  const pdfSubject = document.getElementById('pdf_subject');
  const pdfChapter = document.getElementById('pdf_chapter');
  
  console.log('PDF Course element:', pdfCourse);
  console.log('PDF Subject element:', pdfSubject);
  console.log('PDF Chapter element:', pdfChapter);
  
  if (!pdfCourse) {
    console.error('PDF Course dropdown NOT found!');
    return;
  }
  
  // Course change handler
  pdfCourse.addEventListener('change', function(e) {
    console.log('=== PDF Course changed ===');
    const courseId = this.value;
    console.log('Selected course ID:', courseId);
    
    pdfSubject.disabled = !courseId;
    pdfChapter.disabled = true;
    pdfChapter.innerHTML = '<option value="">Select Chapter</option>';
    
    if (!courseId) {
      pdfSubject.innerHTML = '<option value="">Select Subject</option>';
      return;
    }
    
    // Fetch subjects
    const url = '{{ route("get.subjects.by.course") }}?course_id=' + courseId;
    console.log('Fetching from:', url);
    
    fetch(url)
      .then(response => {
        console.log('Response status:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('Data received:', data);
        
        pdfSubject.innerHTML = '<option value="">Select Subject</option>';
        
        if (data.subjects && data.subjects.length > 0) {
          console.log('Adding ' + data.subjects.length + ' subjects');
          data.subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = subject.name;
            pdfSubject.appendChild(option);
            console.log('Added subject:', subject.name);
          });
        } else {
          console.warn('No subjects in response:', data);
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        alert('Error loading subjects: ' + error.message);
      });
  });
  
  // Subject change handler
  pdfSubject.addEventListener('change', function(e) {
    console.log('=== PDF Subject changed ===');
    const courseId = pdfCourse.value;
    const subjectId = this.value;
    console.log('Course ID:', courseId, 'Subject ID:', subjectId);
    
    pdfChapter.disabled = !subjectId;
    
    if (!courseId || !subjectId) {
      pdfChapter.innerHTML = '<option value="">Select Chapter</option>';
      return;
    }
    
    // Fetch chapters
    const url = '{{ route("get.chapters.by.subject") }}?course_id=' + courseId + '&subject_id=' + subjectId;
    console.log('Fetching from:', url);
    
    fetch(url)
      .then(response => {
        console.log('Response status:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('Data received:', data);
        
        pdfChapter.innerHTML = '<option value="">Select Chapter</option>';
        
        if (data.chapters && data.chapters.length > 0) {
          console.log('Adding ' + data.chapters.length + ' chapters');
          data.chapters.forEach(chapter => {
            const option = document.createElement('option');
            option.value = chapter.id;
            option.textContent = chapter.name;
            pdfChapter.appendChild(option);
            console.log('Added chapter:', chapter.name);
          });
        } else {
          console.warn('No chapters in response:', data);
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        alert('Error loading chapters: ' + error.message);
      });
  });
  
  console.log('=== Event listeners attached ===');
}

// ============ FORM VALIDATION ============

// Keep jQuery validation if jQuery is available
if (typeof jQuery !== 'undefined' && typeof jQuery.fn.validate !== 'undefined') {
  $(document).ready(function() {
    // Validation for Excel form
    $('#question_bank_excel').validate({
      rules: {
        name: { required: true },
        fileupload: { required: true }
      },
      messages: {
        name: { required: 'Enter question bank name' },
        fileupload: { required: 'Choose a ZIP file to upload' }
      }
    });

    // Validation for PDF form
    $('#question_bank_pdf').validate({
      rules: {
        name: { required: true },
        difficulty_level: { required: true },
        course_id: { required: true },
        subject_id: { required: true },
        chapter_id: { required: true },
        fileupload: { required: true }
      },
      messages: {
        name: { required: 'Enter question bank name' },
        difficulty_level: { required: 'Select difficulty level' },
        course_id: { required: 'Select a course' },
        subject_id: { required: 'Select a subject' },
        chapter_id: { required: 'Select a chapter' },
        fileupload: { required: 'Choose a PDF file to upload' }
      }
    });
  });
}
</script>

@endpush
