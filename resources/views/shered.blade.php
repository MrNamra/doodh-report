@extends('layout.app')
@section('title', 'Report')
@section('main')
<div class="card-header"></div>
<div class="form-group" id="report" style="display: block;"></div>
@endsection
@section('script')
<style>
    .ui-datepicker-calendar {
        display: none;
    }
    .content-wrapper {
        margin-left: 0px !important;
    }
</style>
<script>
    const db = {!! $personData !!};
    $(document).ready(function(){
        $("#report").jsGrid({
        height: "100%", // Takes full height of the parent container
        width: "100%",

        autoload: true,
        data: db,

        fields: [
            { name: "date", type: "text", title: "Date", itemTemplate:function(value) {
                if (!value) return '';
                    let dateParts = value.split(" ")[0].split("-");
                    return `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`; // dd-mm-yyyy
                },
            },
            { name: "name", type: "text", title: "Name", itemTemplate: v => v || '—' },
            { name: "qty", type: "number", title: "Quantity" },
            { name: "price", type: "number", title: "Price" },
            { name: "total", type: "number", title: "Total", 
                itemTemplate: function(data, item){
                    if(item.trangaction_type === 'credit'){
                        return $("<span>").css('color', 'green').text(data)
                    } else {
                        return $("<span>").css('color', 'red').text(data)
                    }
                }
            },
            { name: "subTotal", type: "text", title: "Sub Total", 
                itemTemplate: function(data, item){
                    if(item.trangaction_type === 'credit'){
                        return $("<span>").css('color', 'green').text(data)
                    } else {
                        return $("<span>").css('color', 'red').text(data)
                    }
                }
            },
            {
                name: "trangaction_type",
                type: "text",
                title: "Transaction",
                itemTemplate: function(value) {
                    if (value === "credit") {
                        return $("<span>").css("color", "green").text("Credit");
                    } else if (value === "debit") {
                        return $("<span>").css("color", "red").text("Debit");
                    } else {
                        return $("<span>").text("—");
                    }
                }
            }
        ]
    });
    });
</script>
@endsection