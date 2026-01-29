@extends('adminlte::page')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 col-md-12 mt-3">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Question Banks</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="col-lg-12 col-md-12 text-left mb-2 create_btn">
                        <a href="{{ route('qb.summary.create') }}" type="button" class="btn btn-success width_btn">Create</a>
                    </div>
                    <div class="email_id">
                        <table id="question-bank" class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th class="fname">Name</th>
                                    <th hidden>Question Bank ID</th>
                                    <th class="email">Status</th>
                                    <th class="cretated">Created At</th>
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionbank as $val)
                                <tr>
                                    <td class="text-start">{{$val->name}}</td>
                                    <td hidden>{{$val->id}}</td>
                                    <td>
                                        @if($val->status==1)
                                            Active 
                                        @else 
                                            Inactive 
                                        @endif
                                    </td>
                                    <td>{{$val->created_at->format('d-m-Y H:i:s')}}</td>
                                    {{-- <td class="edit_delete action1">
                                        <a href="{{ route('question-bank.show',$val->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                    </td> --}}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.fn.dataTable.moment('DD-MM-YYYY HH:mm:ss');

        $('#question-bank').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            order: [
                [1, 'desc']
            ],
        });

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