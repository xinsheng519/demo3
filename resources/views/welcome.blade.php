@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="padding:15px;">
                <div>Dashboard</div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card" style="padding:15px;">
                <div class="row">
                    <div class="col-md-2" id="total-orders">Total Order: </div>
                    <div class="col-md-2" id="total-revenue">Total Revenue: </div>
                    <div class="col-md-3" id="average-order-value">Average Order Value: </div>
                    <div class="col-md-4" id="top-products">Top 3 Best Sell Product: </div>
                    <div class="col-md-1">
                        <button type="button" id='export_btn'class="btn btn-secondary " >Export</button>
                    </div>
                </div>
                <br>
                <div class="table-container" >
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order No</th>
                                    <th>Order Date</th>
                                    <th>Customer</th>
                                    <th>State</th>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('asset/js/foundation.min.js')}}"></script>
<script src="{{asset('asset/js/jquery-3.7.1.js')}}"></script>
<script src="{{asset('asset/js/dataTables.js')}}"></script>
<script src="{{asset('asset/js/dataTables.foundation.js')}}"></script>
<script src="{{asset('asset/js/sweetalert2.js')}}"></script>
<script src="{{asset('asset/js/script.js')}}"></script>
<script>

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

$(document).ready(function() {  

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "get",
        url: "{{ route('summary') }}",
        dataType: "json",
        success: function (response) {
            if(response.status === 'success'){
                $('#total-orders').text('Total Order: ' + response.totalNumber);
                $('#total-revenue').text('Total Revenue: $' + response.totalRevenue);
                $('#top-products').text(
                    'Top 3 Best Sell Product: ' + response.topProducts.map(p => p.name).join(', ')
                );
                $('#average-order-value').text('Average Order Value: $' + response.avgAmount);
            } else {
                Toast.fire({
                    icon: "error",
                    title: "Something Wrong!"
                })
            }
        },
        error: function(xhr, error, thrown) {
            console.error('AJAX Error:', xhr.responseText);
            Toast.fire({
                icon: "error",
                title: xhr.responseText
            })
        }
    });
    
    $('#table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        autoWidth: true,
        scrollX: true,
        ajax: {
            url: "{{ route('table') }}",
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'order_date', name: 'order_date' },
            { data: 'customer', name: 'customer' },
			{ data: 'state', name: 'state' },
            { data: 'category', name: 'category' },
            { data: 'product', name: 'product' },
			{ data: 'quantity', name: 'quantity' },
            { data: 'unit_price', name: 'unit_price' },
            { data: 'sub_total', name: 'sub_total' },
        ],
        order: [[6, 'desc']], 
        pageLength: 10,     
        lengthMenu: [5, 10, 25, 50, 100],
    });
});

$(document).on('click','#export_btn',function(){
    window.location.href = "{{ route('exportXlsx') }}";
});


</script>

@endsection
