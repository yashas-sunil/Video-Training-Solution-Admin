<div class="d-flex justify-content-end align-items-center" style="gap: 15px;">
    <a href="{{ route('admins.edit', $id) }}" title="Edit">
        <i class="fas fa-edit text-primary fa-lg"></i>
    </a>

    {{-- <a href="#" id="delete-{{ $id }}" title="Delete">
        <i class="fas fa-trash text-danger fa-lg"></i>
    </a> --}}

    @if ($status === 'active')
        <a href="#" class="text-warning" id="toggle-{{ $id }}" data-status="active" title="Block">
            <i class="fas fa-ban fa-lg"></i>
        </a>
    @else
        <a href="#" class="text-success" id="toggle-{{ $id }}" data-status="blocked" title="Unblock">
            <i class="fas fa-unlock fa-lg"></i>
        </a>
    @endif
</div>

<script>
    (function($) {
        $("#delete-{{ $id }}").click(function() {
            if (!confirm("Delete this admin?")) return;
            let table = $('#datatable');

            $.ajax({
                url: "{{ route('admins.destroy', $id) }}",
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(result) {
                    if (result) {
                        toastr.success('Admin deleted successfully');
                        table.DataTable().ajax.reload(null, false);
                    }
                },
                error: function() {
                    toastr.error('Something went wrong');
                }
            });
        });

        $("#toggle-{{ $id }}").click(function() {
            let currentStatus = $(this).data('status');
            let actionText = currentStatus === 'active' ? 'block' : 'unblock';
            if (!confirm("Are you sure you want to " + actionText + " this admin?")) return;
            let table = $('#datatable');

            $.ajax({
                url: "{{ route('users.toggleStatus', $id) }}",
                type: "PATCH",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Admin status updated successfully');
                        table.DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error('Failed to update status');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong');
                }
            });
        });
    })(jQuery);
</script>
