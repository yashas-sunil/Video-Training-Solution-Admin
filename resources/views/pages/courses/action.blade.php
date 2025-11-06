<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
        margin-bottom: 0px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 25px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 3.5px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: orange;
    }

    input:checked+.slider:before {
        transform: translateX(24px);
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="action-buttons">
    {{--  Edit Button --}}
    <a href="{{ route('courses.edit', $course->id) }}" title="Edit Course">
        <i class="fas fa-edit text-primary fa-lg"></i>
    </a>

    {{--  Switch Toggle --}}
    <label class="switch">
        <input type="checkbox" class="toggle-status" data-id="{{ $id }}" {{ $is_enabled ? 'checked' : '' }}>
        <span class="slider"></span>
    </label>
</div>

<script>
    (function($) {
        $(document).off('change', '.toggle-status').on('change', '.toggle-status', function(e) {
            let toggle = $(this);
            let courseId = toggle.data('id');
            let newStatus = toggle.is(':checked') ? 1 : 0;
            let actionText = newStatus == 1 ? 'enable' : 'disable';
            let table = $('#datatable');

            if (confirm(`Are you sure you want to ${actionText} this course?`)) {
                $.ajax({
                    url: "/courses/toggle-status/" + courseId,
                    type: "post",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: newStatus
                    },
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.message);
                            table.DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error(result.message || 'Failed to update status');
                            toggle.prop('checked', !toggle.is(
                            ':checked')); // rollback if failed
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong!');
                        toggle.prop('checked', !toggle.is(':checked')); // rollback if error
                    }
                });
            } else {
                // rollback if cancelled
                toggle.prop('checked', !toggle.is(':checked'));
            }
        });
    })(jQuery);
</script>
