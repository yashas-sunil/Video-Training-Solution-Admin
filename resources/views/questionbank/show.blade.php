@extends('adminlte::page')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')

@push('third_party_stylesheets')
<style>
.square{
    height: 25px;
    width: 25px;
}

img {
    overflow-clip-margin: content-box;
    overflow: clip;
    width: 30% !important;
    height: 20% !important;
}

/* Solution column wider */
#example2 th:nth-child(7),
#example2 tbody td:nth-child(7) {
    min-width: 400px !important;
    max-width: 400px !important;
    width: 400px !important;
}

/* Use fixed layout so columns respect widths and table fits wrapper */
#example2 {
  width: 100% !important;
  table-layout: fixed !important;
  margin-bottom: 0;
}

#example2 td, #example2 th {
  word-wrap: break-word;
  white-space: normal;
  overflow-wrap: break-word;
}

/* Make container scrollable horizontally */
.email_id, .table-responsive-custom {
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch;
  width: 100%;
  box-sizing: border-box;
}

/* ensure buttons inside cells don't force extra height */
.email_id .btn, .table-responsive-custom .btn {
  white-space: normal;
}

/* Constrain solution cell so content wraps inside it */
.solution-cell {
  max-width: 420px;
  overflow: hidden;
  vertical-align: top;
}
.solution-cell .btn {
  display: block;
  width: 100%;
  box-sizing: border-box;
}
</style>
@endpush
@section('content')

    <div class="container-fluid">
    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
</div>
        <div class="row">
            <div class="col-lg-12 col-md-12 mt-3">
               
                <div class="card">
                    <div class="card-header">
                      <div class="row">
                        <div class="col-md-2">
                        <h3 class="card-title">Question Banks</h3>
                        </div>
                        <div class="col-md-8">

                        </div>
                        <div class="col-md-2">
                       
                          <a href="{{ route('question.bank') }}" type="button" class="btn btn-success width_btn">Back</a>
                        
                        </div>
                      </div>
                       
                       
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <div class="table-responsive table-responsive-custom">
                        <table id="example2" class="table table-bordered table-striped text-center">
                            <thead class="blue-bg white-color">
                                <tr> 
                                  <th class="width10">Sr No</th> 
                                  <th class="width10">Course</th>
                                  <th class="width10">Subject</th>
                                  <th class="width10">Chapter</th>
                                  <th class="width25">Question Number</th> 
                                  {{-- <th class="width50">Language</th>  --}}
                                  {{-- <th class="width50">Tags</th>  --}}
                                  <th class="width50">Questions</th> 
                                  {{-- <th class="width20">Topic</th>  --}}
                                  {{-- <th class="width20">Sub Topic</th>  --}}
                                  <th class="width20">Difficulty</th> 
                                  <th class="width20" style="min-width: 400px; width: 400px;">Solution</th>
                                  {{-- <th class="width20">Location</th>  --}}
                                  {{-- <th class="width20">Reviewer</th>  --}}
                                  {{-- <th class="width20">Author</th>  --}}
                                  <th class="width5">Status</th>
                                  <th class="width5">Created At</th>
                                  <th class="width5">Created By</th>
                                  <!-- <th class="width5">Action</th> -->
                                 
                                  </tr>
                                </thead> 

                                <tbody>
                                  
                                    @foreach($questionsList as $val)
                                    <tr>
                                        <td class="text-start">{{$val->srno}}</td>
                                        <td class="text-start">{{$val->course_name}}</td>
                                        <td class="text-start">{{$val->subject_name}}</td>
                                        <td class="text-start">{{$val->chapter_name}}</td>
                                        <td class="text-start">{{$val->question_number}}</td>
                                        {{-- <td class="text-start">{{$val->language}}</td> --}}
                                        {{-- <td class="text-start">{{$val->tags}}</td> --}}
                                        <td class="text-start"><button type="button" class="a-response btn btn-primary" data-id="{{$val->id}}"><?php echo html_entity_decode($val->question); ?></button></td>
                                       
                                        {{-- <td class="text-start">{{$val->topic_name}}</td> --}}
                                        {{-- <td class="text-start">{{$val->sub_topic_name}}</td> --}}
                                        <td class="text-start">{{$val->difficult_level}}</td>
                                        <td class="solution-cell" style="font-size:12px;">
                                          <button type="button" class="a-solution-response btn btn-primary" data-toggle="modal" data-id="{{$val->solution_id}}">
                                            <?php echo html_entity_decode($val->solution_name); ?>
                                          </button>
                                        </td>
                                        {{-- <td class="text-start">{{$val->location}}</td> --}}
                                        {{-- <td class="text-start">{{$val->reviewer}}</td> --}}
                                        {{-- <td class="text-start">{{$val->author}}</td>    --}}
                                        {{--<td class="text-start">{{$val->id}}</td>
                                        <td class="text-start">{{$val->question_banks_id}}</td>--}}
                                       
                                        
                                        <td>@if($val->status==1)Active @else Inactive @endif</td>
                                        <td>{{date("d-m-Y h:i:s A",strtotime($val->created_at))}}</td>
                                        <td class="text-start">{{$val->created_by}}</td>
                                       
                                        {{-- <td class="edit_delete action1">
                                        
                                    
                                            <a href="{{ route('question.show',$val->id) }}" target="_blank" class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                        
                                       
                                       <form action="{{ route('email.destroy',$val->id) }}" method="post">
                                          @csrf
                                         @method('DELETE')
                                         <button class="btn btn-danger delete_rec" type="submit" ><i class="fas fa-trash"></i></button>
                                           </form>
                                         
                                          
                                    </td>--}}
                                  
                                       
                                    </tr>
                                   
                                    @endforeach
                                </tbody>
                            
                            </table>
                          </div>

                        </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function () {
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "responsive": true,
      "width": "100%",
      order: [
        [1, 'asc']
      ],
    });
});

$(document).on('click', '.a-response', function (event) {
    event.preventDefault();

    var questionId = $(this).data('id');
    console.log('Clicking question with ID:', questionId);

    $.ajax({
        url: "{{ route('fetch-answers-by-questions') }}",
        type: "GET",
        data: { id: questionId },
        success: function(response) {
            $('#exampleModalLong .modal-body').html(response.html);
            $('#exampleModalLong').modal('show');
        }
    });
});
</script>
@endsection
