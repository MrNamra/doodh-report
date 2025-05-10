@extends('layout.app')
@section('title', "Registaion Page")
@section('main')
<!-- Horizontal Form -->
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Registaion Form</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form class="form-horizontal regester-form">
        @csrf
        <div class="card-body">
            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" name="name" class="form-control" id="inputName3" placeholder="Name">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" id="inputEmail3" placeholder="Email">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" name="password" class="form-control" id="inputPassword3" placeholder="Password">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" name="confirmpassword" class="form-control" id="inputConfirmPassword3" placeholder="Confirm Password">
                </div>
            </div>
            <div class="form-group row">
                <div class="offset-sm-2 col-sm-10">
                    <div class="form-check">
                        <label class="form-check-label" id="error" style="color: red;"></label>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" class="btn btn-info">Sign in</button>
            <button type="submit" class="btn btn-default float-right">Cancel</button>
        </div>
        <!-- /.card-footer -->
    </form>
</div>
<!-- /.card -->
@endsection
@section('script')
<script>
    $(document).ready(function(){
        $('.regester-form').on('submit', function(e){
            e.preventDefault();
            var data = new FormData(this)
            $("#error").text('')
            $.ajax({
                url: "{{route('user.register')}}",
                data: data,
                dataType: 'json',
                type:"POST",
                processData: false,
                contentType: false,
                success:function(res){
                    Toast.fire({
                        icon: 'success',
                        title: 'Register SuccessFully!'
                    })
                    setTimeout(()=> {window.location.href = "{{ route('login') }}"},1000)
                },
                error:function(err){
                    var data = JSON.parse(err.responseText)
                    var errorMessages = [];

                    for (var field in data.errors) {
                        if (data.errors.hasOwnProperty(field)) {
                            errorMessages.push(data.errors[field][0]);
                        }
                    }
                    var fullErrorMessage = errorMessages.join('\n');

                    Toast.fire({
                        icon: 'error',
                        title: fullErrorMessage
                    });

                    $("#error").text(data.message);

                    console.log(data.errors);
                }
            })
        })
    })
</script>
@endsection