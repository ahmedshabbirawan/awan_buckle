@extends('Layout.master')

@section('title')
{{ $name }}
@endsection

@section('content')
<div class="page-content">

    <div class="page-header" style="min-height:40px;">
        <div style="float: left;">
            <h1>
                System Settings
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    Configure Global Business & Application Settings
                </small>
            </h1>
        </div>
        <div style="float: right;">
            <a href="{{ route('Settings.setting.create') }}" class="btn btn-xs btn-primary bigger">
                <i class="ace-icon fa fa-plus"></i> Add New Setting
            </a>
            <a href="{{ route('Settings.setting.clear_cache') }}" class="btn btn-xs btn-warning bigger" title="Force refresh cached settings">
                <i class="ace-icon fa fa-refresh"></i> Clear / Refresh Cache
            </a>
        </div>
    </div>

    @include('Layout.alerts')

    <div class="row">
        <div class="col-xs-12">
            <div class="tabbable">
                <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
                    <li class="active">
                        <a data-toggle="tab" href="#general-tab">
                            <i class="ace-icon fa fa-building bigger-120"></i>
                            General Business Profile
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#all-settings-tab">
                            <i class="ace-icon fa fa-cogs bigger-120"></i>
                            All Settings (Key-Value CRUD)
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="border: 1px solid #C5D0DC; padding: 16px 12px; position: relative;">
                    <!-- TAB 1: General Business Profile Form -->
                    <div id="general-tab" class="tab-pane in active">
                        <form method="POST" action="{{ route('Settings.setting.quick_update') }}" class="form-horizontal">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-info">
                                        <i class="ace-icon fa fa-info-circle"></i>
                                        These values are globally available across invoices, bills, and headers using <code>setting('key')</code>. Changes automatically invalidate and synchronize system cache.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="business_name">
                                            <strong>Business Name *</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="business_name" name="settings[business_name]" class="form-control" value="{{ setting('business_name') }}" placeholder="e.g. Awan Buckle" required />
                                            <span class="help-block text-muted" style="font-size:11px;">Primary business brand name shown on POS and invoices.</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="business_phone">
                                            <strong>Phone Number(s) *</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="business_phone" name="settings[business_phone]" class="form-control" value="{{ setting('business_phone') }}" placeholder="e.g. 0321-9999668 / 042-37659668" required />
                                            <span class="help-block text-muted" style="font-size:11px;">Contact numbers printed on sale receipts.</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="business_email">
                                            <strong>Business Email</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="email" id="business_email" name="settings[business_email]" class="form-control" value="{{ setting('business_email') }}" placeholder="e.g. info@awanbuckle.com" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="business_website">
                                            <strong>Website</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="business_website" name="settings[business_website]" class="form-control" value="{{ setting('business_website') }}" placeholder="e.g. https://awanbuckle.com" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="business_tax_number">
                                            <strong>NTN / Tax Number</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="business_tax_number" name="settings[business_tax_number]" class="form-control" value="{{ setting('business_tax_number') }}" placeholder="e.g. NTN-1846001-9" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="currency_symbol">
                                            <strong>Currency Symbol</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="currency_symbol" name="settings[currency_symbol]" class="form-control" value="{{ setting('currency_symbol', 'Rs.') }}" placeholder="e.g. Rs. or PKR" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="currency_code">
                                            <strong>Currency Code</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="currency_code" name="settings[currency_code]" class="form-control" value="{{ setting('currency_code', 'PKR') }}" placeholder="e.g. PKR" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="business_logo">
                                            <strong>Logo Path / URL</strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="business_logo" name="settings[business_logo]" class="form-control" value="{{ setting('business_logo') }}" placeholder="assets/images/logo/favicon-32x32.png" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding-right" for="business_address">
                                            <strong>Business Address *</strong>
                                        </label>
                                        <div class="col-sm-10">
                                            <textarea id="business_address" name="settings[business_address]" class="form-control" rows="2" placeholder="e.g. H#1846 Bazar Ali Center Doctor Line Shoes Market Shah Alam Lahore." required>{{ setting('business_address') }}</textarea>
                                            <span class="help-block text-muted" style="font-size:11px;">Physical store or warehouse address printed on order receipts.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix form-actions" style="margin-top: 15px; margin-bottom: 0px;">
                                <div class="col-md-offset-2 col-md-10">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Save General Settings
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                        Reset Form
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: All Settings CRUD Table -->
                    <div id="all-settings-tab" class="tab-pane">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-sm-6">
                                <p class="text-muted">
                                    Manage individual setting keys and custom values. Essential system keys are protected from accidental deletion.
                                </p>
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="{{ route('Settings.setting.create') }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-plus"></i> Add New Custom Setting
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered yajratable" id="settings-table" style="width:100%"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Delete Action -->
    <form id="delete_form" method="POST" action="" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

</div>
@endsection

@section('script')
<script>
    var settingsTable;

    function deleteConfirmation(id, key) {
        $.confirm({
            title: 'Delete Setting',
            content: 'Do you really want to delete the custom setting "<strong>' + key + '</strong>"?<br><small class="text-danger">This will permanently remove the key and its value from the database and cache.</small>',
            type: 'red',
            buttons: {
                yes: {
                    text: 'Confirm Delete',
                    btnClass: 'btn-danger',
                    action: function () {
                        var deleteForm = $('#delete_form');
                        deleteForm.attr('action', "{{ url('Settings/system/delete') }}/" + id);
                        deleteForm.submit();
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function () {}
                }
            }
        });
    }

    $(document).ready(function() {
        settingsTable = $('#settings-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('Settings.setting.index') }}",
            columns: [
                {
                    data: "id",
                    title: 'Sr.',
                    width: '6%',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'key',
                    name: 'key',
                    title: 'Setting Key',
                    width: '24%',
                    render: function(data) {
                        return '<code>' + data + '</code>';
                    }
                },
                {
                    data: 'formatted_value',
                    name: 'value',
                    title: 'Setting Value',
                    width: '44%'
                },
                {
                    data: 'is_essential_badge',
                    name: 'key',
                    title: 'Classification',
                    width: '14%',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action',
                    width: '12%',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'asc']]
        });
    });
</script>
@endsection
