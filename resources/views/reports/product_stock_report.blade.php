@extends('Layout.master')

<?php 

$title = 'Stock Report';

?>

@section('title')
{{ $title }}
@endsection

@section('content')



<div class="page-content">

<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>{{ $title }}</h1>
    </div>
    {{-- <div class="" style="float: right;">
        <a href="{{ route('Settings.project.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
    </div> --}}
</div>


<div class="row">
    <div class="col-xs-12 col-sm-12 widget-container-col" id="widget-container-col-1">
        <div class="widget-box" id="widget-box-1">
            <div class="widget-header widget-header-small">
                <h5 class="widget-title">Filters</h5>
                <div class="widget-toolbar">
             

                  

                    <a href="#" data-action="collapse">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>

                   
                </div>
            </div>

            <div class="widget-body">
                <!---------->
                <div class="widget-main">

<form name="filterForm" > 

                   



                    <div class="row">
                        <!-- Parent Category -->
                      <div class="col-lg-3 col-sm-3">
                        <label class="" for="form-field-1"> Category : </label>
                        <div>
                          <select name="sub_category_id" id="sub_category_id_bluk" class="chosen-select form-control select_chosen select21" required style="width:100%">
                            <option value=""> -- All -- </option>
                            <?php foreach ($sub_category as $cat) : ?><optgroup label="<?= $cat->name ?>">
                                <?php foreach ($cat->subCategories as $subCat) { ?> <option value="<?= $subCat->id ?>"><?= $subCat->name ?></option> <?php } ?>
                              </optgroup> <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
        
                      <!-- Parent Category -->
                      <div class="col-lg-3 col-sm-3">
                        <label class="" for="form-field-1"> Product Category : </label>
                        <div> <select name="product_category_id" id="product_category_id_bluk" class="form-control select_chosen select21" required>
                            <option value=""> -- All -- </option>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-3 col-sm-3">
                        <label>Product</label>
                        <select name="product_id_bluk" id="product_id_bluk" class="col-lg-12 chosen_select select_chosen
                         select21"  required>
                        </select>
                      </div>

                    </div>



                    <div class="space-4"></div>


                    <div class="row">

                            <div class="col-lg-3 col-sm-3">
                            <label>DG</label>
                            <select name="dg_id" id="dg_id" onchange="getProjectByDG(this.value);"  class="col-lg-12 select_chosen select21"  >
                              <option value=""> -- All -- </option>
                              
                            </select>
                            </div>

                            <div class="col-lg-3 col-sm-3">
                            <label>Project</label>
                            <select name="project_id" id="project_id" class="col-lg-12 select_chosen select21" multiple="multiple"  >
                              
                            </select>
                            </div>

                            <div class="col-lg-3 col-sm-3">
                                <label>Date Range</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i><input type="checkbox" class="stock_item_input" name="date_range_cb" id="date_range_cb" value="yes" onchange="date_range_cb_check();" ></i></span>
                                        <div><input type="text" class="form-control" name="date_range" id="date_range" type="text"  placeholder="Date To" placeholder="Date" autocomplete="off"></div>
                                </div>
                              </div>

                    </div>

                    

</form>

                </div>
                <!----------------->

                <div class="widget-toolbox padding-8 clearfix">
                    

                    <button class="btn btn-xs btn-success pull-right" onclick="refershDataTable();">
                        <span class="bigger-110">Search</span>
                        <i class="ace-icon fa fa-check icon-on-right"></i>
                    </button>
                </div>


            </div>

        


        </div></div></div>





<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">


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
@endsection
@section('script')
<script>
var productCategoryURL  =  '{{ route('product.ajax_product_cat') }}';
var productsURL         =  '{{ route('product.ajax_products') }}';
</script>

<script src="{{ asset('assets/css/rowGroup.dataTables.min.css')}}"></script>
<script src="{{ asset('assets/app-js/filters.js')}}?ver=<?=time()?>"></script>
<script src="{{ asset('assets/js/dataTables.rowGroup.min.js')}}"></script>



<script>
    var table;
    function date_range_cb_check(){
            if($('#date_range_cb').is(':checked')){
                console.log('i am checked');
            }else{
                $('input[name=date_range]').val('');
                console.log('i am un checked');
            }
        }
    $(document).ready(function() {

        $('.select21').chosen({
            allow_single_deselect: true
        });

       



        function initDataTableRecord(){
            table = $('#yajra-table').DataTable({
            lengthMenu: [
                    [1, 2, 3, -1],
                    [1, 2, 3, 'All'],
                ],
            "aLengthMenu": [[1,5,10, 25, 50, 100, -1], [1,5,10, 25, 50, 100, "All"]],
            "iDisplayLength": 25,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'csv',
                text: 'Export to Excel',
                     className: 'btn btn-primary text-light',
              //  split: [ 'pdf', 'excel'],
            //     action: newExportAction
            }],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('report.stock.available') }}",
                type: 'GET',
                data: function (d) {
                    d.sub_cat_id = $('#sub_category_id_bluk').val(),     
                    d.pro_cat_id = $('#product_category_id_bluk').val(),   
                    d.prod_id    = $('#product_id_bluk').val(),
                    d.proj_id    = $('#project_id').val(),
                    d.date_range    = $('#date_range').val(),
                    d.date_to    = $('#date_from').val(),
                    d.dg_id = $('#dg_id').val(),
                    d.apply_date = ($('#date_range_cb').is(':checked'))?'1':'';
                }
            },
            columns: [
                {
                    "data": "id",
                    title: 'Sr.',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    'width': '5px'
                },                
                {data: 'name',name: 'name', title: 'Product', 'width': '50%'},
                {data: 'product_available_sum_qty',name: 'product_available_sum_qty', title: 'Available', 'width': '20px'},
                {data: 'stock_delivery_product_sum_quantity',name: 'stock_delivery_product_sum_quantity', title: 'Available', 'width': '20px'}
            ],
            // rowGroup: {
            //     dataSrc: 'project_name'
            // },
             order: [[0, 'asc']]
        });
        }
        initDataTableRecord();
        $('.select_chosen').chosen(); 

            
    });


 





</script>
@endsection