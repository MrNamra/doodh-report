@extends('layout.app')
@section('title', "Report")
@section('main')
    <select id="person">
        <option value="null" selected>Select</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}">{{ $user->preson_name }}</option>
        @endforeach
    </select>
    <input type="text" id="monthPicker" class="form-control" placeholder="Select Month and Year" readonly style="width:200px; display:inline-block; margin-left:10px;">
    <table id="dataTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Date</th>
                <!-- <th>Preson</th> -->
                <th>Item</th>
                <th>Qty</th>
                <th>Ammount</th>
                <th>Total</th>
                <th>subTotal</th>
                <th>Note</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        $('#monthPicker').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'mm-yy',
            onClose: function(dateText, inst) {
                let month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                let year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
                $(this).trigger('change');
            }
        });

        // Force show only month/year, hide the calendar
        $("#monthPicker").focus(function () {
            $(".ui-datepicker-calendar").hide();
        });
        function loadTableData(personId, monthYear) {
            if (personId === "null" || !monthYear) return;

            $('#dataTable').DataTable().clear().destroy();
            $('#dataTable').DataTable({
                responsive: false,
                lengthChange: true,
                autoWidth: false,
                search: false,
                pageLength: 32,
                buttons: ["csv", "excel", "pdf", "print", "colvis"],
                processing: false,
                serverSide: true,
                ajax: '{{ route("user.show.record") }}?id=' + personId + '&month=' + monthYear,
                columns: [
                    { 
                        data: 'created_at', 
                        name: 'created_at',
                        render: function(data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('en-GB');
                        }
                    },
                    //{ data: 'preson_name', name: 'preson_name' },
                    { data: 'name', name: 'name' },
                    { data: 'qty', name: 'qty' },
                    { data: 'price', name: 'price' },
                    { data: 'total', name: 'total' },
                    { data: 'subTotal', name: 'subTotal' },
                    { data: 'note', name: 'note' },
                    { data: 'actions', name: 'Action' },
                ],
                dom: 'Bfrtip'
            }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
        }

        $("#person, #monthPicker").on('change', function () {
            let personId = $("#person").val();
            let monthYear = $("#monthPicker").val();
            loadTableData(personId, monthYear);
        });
        $(document).on('click', '.delete', function() {
            var userId = $(this).data('id');
            if (confirm("Are you sure you want to delete this user?")) {
                $.ajax({
                    url: "{{route('user.remove.record')}}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: userId
                    },
                    success: function(response) {
                        $('#dataTable').DataTable().ajax.reload();
                        alert('User deleted successfully');
                    },
                    error: function() {
                        alert('An error occurred');
                    }
                });
            }
        });
        $(document).on('click', '.edit', function() {
            var id = $(this).data('url');
            window.location.href = id
        });
    })
</script>
@endsection