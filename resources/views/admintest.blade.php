@extends('adminlte::page')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endpush

@section('title', 'EduEdge Test creation')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark">EduEdge Test creation</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('admin-test.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add EduEdgeTest
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="championship" style="overflow-x: auto;">
                        <table id="championship" class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Test Name</th>
                                    <th>Course</th>
                                    <th>Subject</th>
                                    <th>Total Question</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admin_test as $val)
                                    <tr>
                                        <td>{{ $val->id }}</td>
                                        <td>{{ $val->test_name }}</td>
                                        <td>
                                            @php
                                                $course = \App\ScormPackage::where('id', $val->course_id)->first();
                                            @endphp
                                            {{ $course ? $course->title : 'N/A' }}
                                        </td>
                                        <td>
                                            @php
                                                $subjectIds = explode(',', $val->subject_id);
                                            @endphp
                                            @foreach ($subjectIds as $subjectId)
                                                @php
                                                    $subject = \App\Models\Subject::find(trim($subjectId));
                                                @endphp
                                                @if ($subject)
                                                    <p>{{ $subject->name }}</p>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{ $val->total_ques_count }}</td>
                                        <td>
                                            @if ($val->status == 1)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="display:flex; align-items:center; justify-content:center; gap:10px;">
                                                <a href="{{ route('admin-test.preview', $val->id) }}" title="Preview Test Questions">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox"
                                                        class="custom-control-input status-toggle"
                                                        id="status_{{ $val->id }}"
                                                        {{ $val->status == 1 ? 'checked' : '' }}
                                                        data-id="{{ $val->id }}">
                                                    <label class="custom-control-label" for="status_{{ $val->id }}"></label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript">

        // Toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };

        $(function () {

            // ── DataTable init ──
            $('#championship').DataTable({
                "paging":       true,
                "lengthChange": true,
                "searching":    true,
                "ordering":     true,
                "info":         true,
                "autoWidth":    true,
                "responsive":   true,
            });

            // ── STATUS TOGGLE ──
            // WRONG way (old code):
            //   $('.status-toggle').on('change', ...) — binds only to elements
            //   present at load time. DataTable moves rows in/out of DOM when
            //   paginating or searching, so rows not on page 1 never fire.
            //
            // CORRECT way — delegate to document so ALL rows always work:
            $(document).on('change', '.status-toggle', function () {

                let testId    = $(this).data('id');
                let newStatus = $(this).is(':checked') ? 1 : 0;
                let $checkbox = $(this);

                $.ajax({
                    url:      '{{ url('/admin-test') }}' + '/' + testId + '/toggle-status',
                    type:     'POST',
                    dataType: 'json',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            let $badge = $checkbox.closest('tr').find('.badge');
                            if (newStatus == 1) {
                                $badge.removeClass('badge-danger').addClass('badge-success').text('Active');
                            } else {
                                $badge.removeClass('badge-success').addClass('badge-danger').text('Inactive');
                            }
                            toastr.success('Status updated successfully!');
                        } else {
                            toastr.error('Error updating status');
                            $checkbox.prop('checked', !$checkbox.is(':checked'));
                        }
                    },
                    error: function () {
                        toastr.error('Error updating status');
                        $checkbox.prop('checked', !$checkbox.is(':checked'));
                    }
                });
            });

            // ── Flash messages ──
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif
            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
            @if (session('warning'))
                toastr.warning('{{ session('warning') }}');
            @endif
            @if (session('info'))
                toastr.info('{{ session('info') }}');
            @endif

        });

    </script>
@stop

@push('third_party_scripts')
@endpush