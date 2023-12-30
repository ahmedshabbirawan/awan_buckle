@extends('Layout.master')

@section('title','Product Details')
@section('Title','Products')
@section('URL',route("product.list"))
@section('PageName','Product Details')


@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            @yield('PageName')
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                {{ $row->name }}
            </small>
        </h1>
    </div><!-- /.page-header -->


    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <h4 class="blue">
                <span class="middle">{{ $row->name }}</span>
            </h4>

            <div class="profile-user-info profile-user-info-striped">
                <div class="profile-info-row">
                    <div class="profile-info-name"> Product Title </div>
                    <div class="profile-info-value"><span>{{ $row->name }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Categories </div>
                    <div class="profile-info-value"><span>{{ $categories['category']['name'] }} / {{ $categories['sub_category']['name'] }} / {{ $categories['product_category']['name'] }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Attributes </div>
                    <div class="profile-info-value"><span><?php 
                                            foreach($row->product_attribute_value->pluck('value','attribute_type.name') as $key => $value):
                                                echo $key.' : '.$value.'<br>';
                                            endforeach;
                                        ?></span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Unit of Measurement </div>
                    <div class="profile-info-value"><span>{{(isset($row->uom->name))?$row->uom->name:'' }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Created Date & Time </div>
                    <div class="profile-info-value"><span>{{ (isset($row->created_at))?$row->created_at:'' }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Description </div>
                    <div class="profile-info-value"><span>{{ $row->description }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <div class="tabbable">
                <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab">
                    <li class="active">
                        <a data-toggle="tab" href="#deliveries-tab" aria-expanded="false">
                            <i class="blue ace-icon fa fa-question-circle bigger-120"></i>
                            Deliveries
                        </a>
                    </li>

                    <li class=""> 
                        <a data-toggle="tab" href="#suppliers-tab" aria-expanded="false">
                            <i class="orange ace-icon fa fa-truck bigger-120"></i>
                            Suppliers
                        </a>
                    </li>

                    <li class="">
                        <a data-toggle="tab" href="#items-tab" aria-expanded="false">
                            <i class="purple ace-icon fa fa-magic bigger-120"></i>
                            Product Items
                        </a>
                    </li>

                    <li class="">
                        <a data-toggle="tab" href="#shop-wise-stock-tab" aria-expanded="false">
                            <i class="purple ace-icon fa fa-magic bigger-120"></i>
                            Shop Wise Stock
                        </a>
                    </li>
                </ul>
                <div class="tab-content no-border padding-24">
                @include('product.tab.delivery_tab',['product_id' => $row->id])
                @include('product.tab.suppliers_tab',['product_id' => $row->id])
                @include('product.tab.items_tab',['product_id' => $row->id])
                @include('product.tab.shop_wise_available_stock_tab',['product_id' => $row->id])
                </div>
            </div>

            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div>


@endsection



@section('javascript')
<script>
	var table;
	var selectID;
    var historyTable;
</script>

@yield('delivery_script')
@yield('suppliers_script')
@yield('items_script')
@yield('shop_wise_stock_script')
<script>
function historyTableReload(itemID){
    $('#item_id').val(itemID);
    historyTable.ajax.reload();
    $('#item-history-btn').click();
}
</script>
@endsection