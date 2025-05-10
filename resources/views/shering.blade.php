@extends('layout.app')
@section('title', "Share Link")
@section('main')
<form method="post" id="getLink">
    @csrf
    <select id="person" name="person_id" >
        <option value="null" selected>Select</option>
        @foreach ($persons as $user)
            <option value="{{ $user->id }}">{{ $user->preson_name }}</option>
        @endforeach
    </select>
    <input type="text" id="monthPicker" name="date" class="form-control" placeholder="Select Month and Year" readonly style="width:200px; display:inline-block; margin-left:10px;">
    <button type="submit" class="btn btn-warning">Get Link</button>
</form>
<br><br>
<p id="link"></p>
<br>
<p id="delete" style="display: none"></p>
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
    $("#monthPicker").focus(function () {
        $(".ui-datepicker-calendar").hide();
    });
    $("#getLink").on('submit', function(e){
        e.preventDefault()
        var data = new FormData(this)
        $.ajax({
            url: "{{ route('get.link') }}",
            type:"post",
            data,
            processData: false,
            contentType: false,
            success:function(res){
                console.log(res)
                $("#link").empty().append("{{ url('/c') }}/"+res.data.id)
                $("#delete").removeAttr('style')
                $("#delete").empty().append(`For remove this url <a onClick="deleteUrl('${res.data.id}')">click Here</a>`)
            },
            error(err){
                console.log(err)
                alert("something want wrong")
            }
        })
    })
});
function deleteUrl(url){
    if(confirm("After delete it this link will in valid and now able to recover but data is safe, still Continue?")){
        $.ajax({
            url:"{{ route('remove.link') }}",
            type:"post",
            data:{
                _token: "{{ csrf_token() }}",
                "id":url
            },
            success:function(res){
                Toast.fire({
                    icon:"success",
                    title:res.message
                })
                setTimeout(window.location.reload(), 1000)
            },
            error:function(err){
                console.log(err)
                Toast.fire({
                    icon:"error",
                    title:res.message
                })
            }
        })
    }
}
</script>
@endsection