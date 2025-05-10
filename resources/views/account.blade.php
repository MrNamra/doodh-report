@extends('layout.app')
@section('main')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Add Ammount</h3>
    </div>
    <div class="card-body">
        <form actions="" method="post" id="add-ammout">
            @csrf
            <input type="hidden" value="{{ isset($users->logsData) ? $users->logsData->id : null }}" name="id" />
            <div class="form-group" id="person-fields">
                <lable>Select Person</lable>
                <select name="person_id" class="form-control select2 ">
                    <option value="">Select</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}" @if(isset($users->logsData) && $users->logsData->preson_id == $user->id) selected @endif >{{$user->preson_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Date(MM-DD-YYYY):</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                    <input type="text" name="date" class="form-control datetimepicker-input" data-target="#reservationdate" />
                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
            <!-- /.form group -->
    
            <div class="form-group">
                <label>Ammout:</label>
    
                <div class="input-group">
                    <input type="number" name="money" value="{{ isset($users->logsData) ? $users->logsData->price : '' }}" class="form-control" required />
                </div>
            </div>
            <div class="form-group">
                <label>Note:</label>

                <div class="input-group">
                    <input type="text" name="note" value="{{ isset($users->logsData)?$users->where('id', $users->logsData->preson_id)->first()->note : '' }}" class="form-control" placeholder="(optional)" />
                </div>
            </div>
            <!-- /.form group -->
            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ isset($users->logsData) ? 'Update Data' : 'Add Entry' }}</button>
            </div>
            <!-- /.form group -->
        </form>
    </div>
    <div class="card-footer">
        <p id="change-person" data-id="select">Clcik here to register new person</p>
    </div>
    <!-- /.card-body -->
</div>
<table style="width: 100%;">
   <th>Index</th>
   <th>name</th>
   <th>Ammount</th>
   <th>Action</th>
   @forelse ($users as $key => $user)
   <tr>
       <td>{{$key+1}}</td>
       <td>{{ $user->preson_name }}</td>
       <td>{{ $user->money }}</td>
       <td><button class="btn btn-danger delete" data-id="{{ $user->id }}">Delete</button></td>
   </tr>
   @empty
       <tr>No Data Found</tr>
   @endforelse
</table>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        let field=$('#change-person').data('id')
        let defaultDate = '{{ isset($users->logsData) ? \Carbon\Carbon::parse($users->logsData->date)->format('Y-m-d') : null }}';
        $('#reservationdate').datetimepicker({
            format: 'L',
            defaultDate: defaultDate ? moment(defaultDate, 'YYYY-MM-DD') : moment()
        });
        $("#change-person").on('click', function(){
            $('#person-fields').empty()
            if(field == 'input'){
                field = 'select';
                $('#person-fields').html(`
                    <lable>Select Person</lable>
                    <select name="id" class="form-control select2 ">
                    <option value="">Select</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}">{{$user->preson_name}}</option>
                    @endforeach
                `)
                $(this).text('Clcik here to register new person')
            }else{
                $('#person-fields').html(`
                    <lable>Person Name</lable>
                    <input type="text" name="preson_name" class="form-control" />
                `)
                $(this).text('Clcik here to Select From Registered person')
                field = 'input';
            }
        })
        $("#add-ammout").on('submit', function(e){
            e.preventDefault();
            const data = new FormData(this)
            $.ajax({
                url:"{{ route('user.add.person') }}",
                data,
                type: "post",
                processData: false,
                contentType: false,
                success:function(res){
                    if(res.status){
                        Toast.fire({
                            icon: 'success',
                            title: "Opration success!"
                        });
                        setTimeout(window.location.href = "{{ route('user.account') }}", 1000)
                    }
                },
                error:function(err){
                    var data = JSON.parse(err.responseText)
                    console.log(data)
                    var errorMessages = [];
                    for (var field in data.errors) {
                        if (data.errors.hasOwnProperty(field)) {
                            errorMessages.push(data.errors[field][0]);
                        }
                    }
                    var fullErrorMessage = errorMessages.join('\n');
                    if(!fullErrorMessage){
                        fullErrorMessage = data.message
                    }
                    Toast.fire({
                        icon: 'error',
                        title: fullErrorMessage
                    });
                    $("#error").text(data.message);
                    console.log(data.errors);
                }
            })
        })
        $(".delete").on('click', function(){
            if(confirm("Are you sure? if you delete this all data belongs to this will delete all trangation can't be recoverable!")){
                $.ajax({
                    url: "{{ route('user.remove.person') }}",
                    type: "post",
                    data:{
                        _token: "{{ csrf_token() }}",
                        id: $(this).data('id')
                    },
                    success:function(res){
                        Toast.fire({
                            icon: "success",
                            title: "Data Deleted!"
                        })
                        setTimeout(location.reload(),1000)
                    },
                    error:function(err){
                        Toast.fire({
                            icon: "error",
                            title: "Server Error"
                        })
                    }
                })
            }
        })
    })
</script>
@endsection