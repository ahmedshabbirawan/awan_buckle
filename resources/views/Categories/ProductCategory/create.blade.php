@extends('Layout.master')
@section('title','Create Product Category')
@section('Title','ProductCategory')
@section('URL',route("category.product.list"))
@section('PageName','Create')
@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            Product Category
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Create
            </small>
        </h1>
        {{-- <a href="{{route('category.parent.create')}}"><button class="btn btn-primary" style="position:absolute;right:20px;top:15px;">Add New</button></a> --}}
    </div><!-- /.page-header -->
    @include('Layout.alerts')
    <div class="row">
        <div class="col-xs-12">

            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div class="col-xs-12">

                    <form class="form-horizontal" method="POST" action="{{route('category.product.store')}}">
                        @csrf

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right">Select Parent Category</label>

                            <div class="col-sm-9">
                                <div class="pos-rel">
                                    <select name="parentCategoryName" id="CategoryName" class="select2" required>
                                        <option hidden selected value="">Select Parent Category</option>
                                        @foreach ( $ParentCategories as $ParentCategory )
                                        <option value="{{$ParentCategory->id}}">{{$ParentCategory->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="space-6"></div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3 no-padding-right" for="food">Enter SubCategory Name</label>

                            <div class="col-xs-12 col-sm-9">
                                <select name="SubCategoryName" id="SubCategoryName" class="select2" required>
                                    <option hidden selected value="">Select Sub Category</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-6"></div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3 no-padding-right" for="food">Enter Product Category Name</label>

                            <div class="col-xs-12 col-sm-9">
                                <input type="text" name="name" placeholder="Product Category Name" size="24" required>

                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3 no-padding-right" for="food">Enable Alerts</label>
                            <div class="col-xs-12 col-sm-9">
                            <div class="control-group" >
                                    <div class="radio">
                                        <label>
                                            <input name="enable_alert" value="1" type="radio" class="ace">
                                            <span class="lbl"> Yes </span>
                                        </label>
                                        <label>
                                            <input name="enable_alert" value="0" checked="checked" type="radio" class="ace">
                                            <span class="lbl"> No </span>
                                        </label>
                                    </div>
                            </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3 no-padding-right" for="food">Serial Number Required</label>
                            <div class="col-xs-12 col-sm-9">
                            <div class="control-group" >
                                    <div class="radio">
                                        <label>
                                            <input name="sn_require" value="1" checked="checked" type="radio" class="ace">
                                            <span class="lbl"> Yes </span>
                                        </label>
                                        <label>
                                            <input name="sn_require" value="0"  type="radio" class="ace">
                                            <span class="lbl"> No </span>
                                        </label>
                                    </div>
                            </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-top" for="duallist"> Product Category Attributes</label>

                            <div class="col-sm-8">
                                <select multiple="multiple" size="10" name="duallistbox_demo1[]" id="duallist">
                                    @foreach ($attributeTypes as $attributeType)
                                    <option value="{{$attributeType->id}}">{{$attributeType->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-6"></div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-top">&nbsp;</label>
                            <input type="submit" class="btn btn-success col-sm-1" style="margin-left: 1%;">
                        </div>
                    </form>
	

                </div><!-- /.span -->
            </div><!-- /.row -->

            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div>
    <!-- /.row -->
</div><!-- /.page-content -->
@stop

@section('script')
<script>

jQuery(function($){
    var demo1 = $('select[name="duallistbox_demo1[]"]').bootstrapDualListbox({infoTextFiltered: '<span class="label label-purple label-lg">Filtered</span>'});
    var container1 = demo1.bootstrapDualListbox('getContainer');
    container1.find('.btn').addClass('btn-white btn-info btn-bold');

    /**var setRatingColors = function() {
        $(this).find('.star-on-png,.star-half-png').addClass('orange2').removeClass('grey');
        $(this).find('.star-off-png').removeClass('orange2').addClass('grey');
    }*/



    //////////////////
    //select2
    $('.select2').css('width','200px').select2({allowClear:true})
    $('#select2-multiple-style .btn').on('click', function(e){
        var target = $(this).find('input[type=radio]');
        var which = parseInt(target.val());
        if(which == 2) $('.select2').addClass('tag-input-style');
         else $('.select2').removeClass('tag-input-style');
    });

    //////////////////
    $('.multiselect').multiselect({
     enableFiltering: true,
     enableHTML: true,
     buttonClass: 'btn btn-white btn-primary',
     templates: {
        button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> &nbsp;<b class="fa fa-caret-down"></b></button>',
        ul: '<ul class="multiselect-container dropdown-menu"></ul>',
        filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
        filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default btn-white btn-grey multiselect-clear-filter" type="button"><i class="fa fa-times-circle red2"></i></button></span>',
        li: '<li><a tabindex="0"><label></label></a></li>',
        divider: '<li class="multiselect-item divider"></li>',
        liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
     }
    });


    ///////////////////

    //typeahead.js
    //example taken from plugin's page at: https://twitter.github.io/typeahead.js/examples/
    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            // an array that will be populated with substring matches
            matches = [];

            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');

            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function(i, str) {
                if (substrRegex.test(str)) {
                    // the typeahead jQuery plugin expects suggestions to a
                    // JavaScript object, refer to typeahead docs for more info
                    matches.push({ value: str });
                }
            });

            cb(matches);
        }
     }

     $('input.typeahead').typeahead({
        hint: true,
        highlight: true,
        minLength: 1
     }, {
        name: 'states',
        displayKey: 'value',
        source: substringMatcher(ace.vars['US_STATES']),
        limit: 10
     });


    ///////////////


    //in ajax mode, remove remaining elements before leaving page
    $(document).one('ajaxloadstart.page', function(e) {
        $('[class*=select2]').remove();
        $('select[name="duallistbox_demo1[]"]').bootstrapDualListbox('destroy');
        $('.rating').raty('destroy');
        $('.multiselect').multiselect('destroy');
    });
});

$('#CategoryName').on('change', function (e) {
    var parentID = $(this).val();
        $.ajax({
            url: "{{ route('category.sub.getSubCategoryByParentId') }}",
            method: "get",
            data: {parentID: parentID},
            success: function (econ) {
                console.log(econ);
                    $('#SubCategoryName').empty();
                    $('#SubCategoryName').append('<option value="">Choose...</option>');
                    $.each(econ, function (index, item) {
                        $('#SubCategoryName').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                    $('#SubCategoryName').trigger("chosen:updated");
            },
            statusCode: {
                404: function () {
                    $('#incidentSubType').empty();
                //    alert("Sub Incident Type not found!");
                }
            }

        });
});
</script>
@stop
