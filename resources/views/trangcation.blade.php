@extends('layout.app')
@section('title', 'Trangaction')
@section("main")
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Add Trangaction</h3>
    </div>
    <div class="card-body">
        <form actions="" method="post" id="add-trangcation">
            @csrf
            <input type="hidden" name="id" value="{{ ($data!=null) ? $data->id : '' }}" />
            <div class="form-group">
                <label>Date(MM-DD-YYYY):</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                    <input type="text" name="date" class="form-control datetimepicker-input" value="{{ ($data!=null)? Carbon\Carbon::parse($data->created_at)->format('m-d-yyy'):'' }}" data-target="#reservationdate" />
                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
            <div class="form-group" id="person-fields">
                <lable>Select Person</lable>
                <select name="preson_id" class="form-control select2">
                    <option value="">Select</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if($data!=null && $user->id == $data->preson_id) selected @endif>{{ $user->preson_name }}</option>                        
                    @endforeach
                </select>
            </div>
            <!-- /.form group -->

            <div class="form-group">
                <label>Name:</label>
    
                <div class="input-group">
                    <input type="text" name="name" value="{{ ($data) ? $data->name : '' }}" class="form-control" required placeholder="Gold doodh" />
                </div>
            </div>

            <div class="form-group">
                <label>Quantity:</label>
    
                <div class="input-group">
                    <input type="number" name="qty" value="{{ ($data)? $data->qty : '' }}" class="form-control" required />
                </div>
            </div>

            <div class="form-group">
                <label>Ammout (price of one):</label>
    
                <div class="input-group">
                    <input type="number" name="price" value="{{ ($data)? $data->price : '' }}" class="form-control" required />
                </div>
            </div>
            <div class="form-group">
                <label>Note:</label>
    
                <div class="input-group">
                    <input type="text" name="note" value="{{ ($data) ? $data->note : '' }}" class="form-control" placeholder="(optional)" />
                </div>
            </div>
            <!-- /.form group -->
            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ ($data) ? 'Update' : 'Add Entry' }}</button>
            </div>
            <!-- /.form group -->
        </form>
    </div>
    <div class="card-footer">
        <p id="change-person" data-id="select"></p>
    </div>
    <!-- /.card-body -->
</div>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        let field=$('#change-person').data('id')
        $('#reservationdate').datetimepicker({
            format: 'L',
            defaultDate: moment()
        });
        $("#add-trangcation").on('submit', function(e){
            e.preventDefault();
            const data = new FormData(this)
            $.ajax({
                url:"{{ route('user.add.record') }}",
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
                        setTimeout(window.location.reload(), 1000)
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
    });
</script>
@endsection