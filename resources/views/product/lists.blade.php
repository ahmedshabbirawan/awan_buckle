@extends('Layout.master')

@section('title')
Products
@endsection

@section('content')

<div class="page-content">
<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Products</h1>
    </div>
    <div class="" style="float: right;">
        <a href="javascript:void(0);" onclick="pos_app.import_product_modal();"  class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-file-o"></i> Import Products </a> 
        <a href="{{ route('simple_product.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
    </div>
</div>

<?php 
$actionURL =  route('simple_product.store');
?>




<!-------------------------------------------------------------------->
<div class="row ">

<form method="post" id="product_form" action="{{ isset($product) ? route('simple_product.update', $product->id) : route('simple_product.store') }}" novalidate class="form-horizontal product_form">
                @csrf

<div class="col-12 col-lg-12" >
    <div class="widget-box widget-color-blue ui-sortable-handle" >
    <div class="widget-header"><h4 class="widget-title">Quick Add Product</h4></div>
    <div class="card radius-10 border-top  border-danger">
        <div class="card-body">


        <div class="col-lg-4 col-sm-4">
                <label for="form-field-1"> Product Name: </label>
                <div class="">
                    <input type="text" required class="form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($product))? $product->name : '' )}}" name="name" id="name" placeholder="Name">
                </div>
        </div>
        <div class="col-lg-4 col-sm-4" >
            <label class="" for="form-field-1"> Price:</label>
            <div class="">
                <input type="text" class="col-xs-10 col-sm-5 form-control @error('price') is-invalid @enderror" value="{{ old('price',(isset($price))? $product->price : '') }}" name="price" id="price" placeholder="Price">
            </div>
        </div>

        <div class="col-lg-4 col-sm-4" >
            <label class="" for="form-field-1"> UOM:</label>
            <div class="">
            <select name="uom_id" id="uom_id" class="col-xs-10 col-sm-5 form-control">
                                                @foreach($uoms as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
            </div>
        </div>

        <div class="col-lg-4 col-sm-4" >
            <label class="" for="form-field-1"> &nbsp; </label>
            <div class="">
            <button class="btn btn-info" onclick="checkValidation();" type="button">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Submit
                                        </button>
            </div>
        </div>

        </div>
        <div class="card-footer">
        </div>
    </div>
    </div>
</div>


    
            
</form>
                                


                                    

</div>
<!-------------------------------------------------------------------->





<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">
        <div class="card radius-10 border-top border-0 border-4 border-danger">
          
        <!-- <div class="card-header " style="background-color: #f5f5f5; margin:5px 0px; padding:10px 10px;">

        include('product.category_selector') 

        </div>-->


            <div class="card-body">

                @include('Layout.alerts')

                <div class="table-responsive">
                    <table class="table table-striped table-bordered yajratable" id="yajra-table" style="width:100%"></table>
                </div>
            </div>



        </div>
    </div>
</div>
<!--end row-->
</div>

<div id="import_product_modal_con"></div>


@endsection



@section('script')
@yield('category_script')
<script>


var listTable;
    var selectID;




    function checkValidation() {
        $(".btn-info").LoadingOverlay("show");
        let myform = document.getElementById("product_form");
        let fdata = new FormData(myform);

        $.ajax({
            data: fdata,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType: "JSON",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ $actionURL }}",
            success: function(res, textStatus, jqXHR) {
                $(".btn-info").LoadingOverlay("hide");
                if (jqXHR.status == 200) {
                    if (typeof res.data.product !== 'undefined') {
                        $.confirm({
                            title: 'Success',
                            content: res.message,
                            buttons: {
                                yes: {
                                    text: 'OK',
                                    action: function() {
                                        listTable.ajax.reload();
                                    }
                                }
                            }
                        });
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(".btn-info").LoadingOverlay("hide");
                if (jqXHR.status != 200) {
                    if (typeof jqXHR.responseJSON !== 'undefined') {
                        $.confirm({
                            title: 'Error',
                            content: jqXHR.responseJSON.message
                        });
                    }
                }
            }
        });
}



    function deleteConfirmation(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        var changeURL = "{{ route('product.delete','') }}";
                        // alert(changeURL);
                        window.location = changeURL + '/' + selectID;
                    }
                },
                no: {
                    text: 'No', // With spaces and symbols
                    action: function() {

                    }
                }
            }
        });
    }


    function doChangeStatus(objID){
        var changeURL = "{{ route('product.status','') }}";
        $.ajax({
            dataType: 'json',
            type:'get',
            url : changeURL + '/' + objID,
            success:function(res){
                console.log('yes');
                if(res.status){
                    listTable.ajax.reload();
                   //  $('#statusPopup').modal('hide');
                } 
            }
        });
    }


    function changeStatus(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                       doChangeStatus(id);
                    }
                },
                no: {
                    text: 'No', // With spaces and symbols
                    action: function() {

                    }
                }
            }
        });
    }

    $(document).ready(function() {
        var dataColumns = [
            // {"data": "id",title: 'Sr.',
            //     render: function(data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     }, width: '10%'
            // },
            { data: 'id', name: 'id', title: 'ID.', width: '10%'  },
            {"data": "image_url",title: 'Image',
                render: function(data, type, row, meta) {
                    return '<image src="'+data+'" style="width:75px; height:75px;" >';
                }, width: '10%'
            },
            { data: 'product_name', name: 'product_name', title: 'Name',},
            { data: 'price', name: 'price', title: 'Sale Price',},
            { data: 'code', name: 'code', title: 'UOM', width: '10%'  },
            { data: 'stock_available', name: 'stock_available', title: 'Total Stock', width: '10%'  }
        ];
        <?php 
        foreach($shops as $shop):
        ?>
                dataColumns.push({ data: 'shop_<?=$shop->id?>', name: 'shop_<?=$shop->id?>', title: '<?=$shop->name?>'});
        <?php
        endforeach;
        ?>
        dataColumns.push({ data: 'status_label', name: 'status', title: 'Status', width: '10%'  });
        dataColumns.push({ data: 'action', name: 'action', title: 'Action', width: '15%' });

        setTimeout(() => {
            listTable = $('#yajra-table').DataTable({
            sort:true,
            dom: 'Bfrtip',
            buttons: [
            'excel', 'pdf'
            ],
            // paging: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('product.list') }}",
                type: 'GET',
                data: function (d) {
                    var idz = [];
                    $($('.attributes_id')).each(function(index,ele){
                        idz.push($(ele).val());
                    });
                    d.product_category_id       = $('#product_category_id').val();
                    d.attribute_idz = idz;
                }
            },
            columns: dataColumns,
            order: [[ 2 , 'asc']]
        });
        }, 500);
    });

    function apply_Filter(){
        console.log('hello');
        listTable.ajax.reload();
    }
</script>
@endsection