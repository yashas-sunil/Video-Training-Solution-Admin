@extends('adminlte::page')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')

{{-- Toast notifications for flash messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="pdf-success-alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px;">
        <strong><i class="fas fa-check-circle mr-2"></i> Success!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="pdf-error-alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px;">
        <strong><i class="fas fa-times-circle mr-2"></i> Error!</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 col-md-12 mt-3">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Question Banks</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="col-lg-12 col-md-12 text-right mb-2 create_btn">
                        <a href="{{ route('qb.summary.create') }}" type="button" class="btn btn-success width_btn">Add QuestionBank</a>
                    </div>
                    <div class="email_id">
                        <table id="question-bank" class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th class="fname">Name</th>
                                    <th hidden>Question Bank ID</th>
                                    <th class="cretated">Created At</th>
                                    <th class="email">Status</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionbank as $val)
                                <tr>
                                    <td class="text-start">{{$val->name}}</td>
                                    <td hidden>{{$val->id}}</td>
                                    <td>{{$val->created_at->format('d-m-Y H:i:s')}}</td>
                                    <td>
                                        @if($val->status==1)
                                            Active 
                                        @else 
                                            Inactive 
                                        @endif
                                    </td>
                                    <td class="edit_delete action1">
                                        <a href="{{ route('question-bank.show',$val->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                    </td>
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

@endsection
@push('third_party_scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        console.log('DataTables initialization starting...');
        
        $.fn.dataTable.moment('DD-MM-YYYY HH:mm:ss');

        try {
            const table = $('#question-bank').DataTable({
                "paging": true,
                "pageLength": 10,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "columnDefs": [
                    { "orderable": false, "targets": [1] }
                ]
            });
            console.log(' DataTables initialized successfully');
            console.log('Table info:', table.page.info());
        } catch(e) {
            console.error(' DataTables error:', e);
        }

        $(".delete_rec").click(function(e) {
            let confirmation = confirm("Are you sure to enable/disable this?");
            if (confirmation) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
@push('js')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    console.log('datatable loaded');

    $('#question-bank').DataTable({
        paging: true,
        pageLength: 10
    });
});
</script>
@endpush