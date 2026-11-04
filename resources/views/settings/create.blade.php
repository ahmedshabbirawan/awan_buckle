@extends('Layout.master')

@section('title')
Add System Setting
@endsection

@section('content')
<div class="page-content">

    <div class="page-header" style="min-height:40px;">
        <div style="float: left;">
            <h1>
                System Settings
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    Add New System Setting
                </small>
            </h1>
        </div>
        <div style="float: right;">
            <a href="{{ route('Settings.setting.index') }}" class="btn btn-xs btn-default bigger">
                <i class="ace-icon fa fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>

    @include('Layout.alerts')

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2" style="margin-top:20px;">
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">
                        <i class="ace-icon fa fa-plus-circle"></i>
                        New Setting Configuration
                    </h4>
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <form method="POST" action="{{ route('Settings.setting.store') }}" class="form-horizontal">
                            @csrf

                            <div class="form-group @error('key') has-error @enderror">
                                <label class="col-sm-3 control-label no-padding-right" for="key">
                                    <strong>Setting Key *</strong>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" id="key" name="key" value="{{ old('key') }}" class="form-control col-xs-12 col-sm-8" placeholder="e.g. invoice_footer_note" required />
                                    <div class="col-xs-12 no-padding" style="margin-top:5px;">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> Use lowercase letters, numbers, and underscores only (e.g. <code>store_receipt_tagline</code>). Must be unique.
                                        </small>
                                    </div>
                                    @error('key')
                                    <div class="help-block col-xs-12 col-sm-reset inline">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group @error('value') has-error @enderror">
                                <label class="col-sm-3 control-label no-padding-right" for="value">
                                    <strong>Setting Value</strong>
                                </label>
                                <div class="col-sm-9">
                                    <textarea id="value" name="value" class="form-control col-xs-12 col-sm-8" rows="4" placeholder="Enter configuration value or text">{{ old('value') }}</textarea>
                                    <div class="col-xs-12 no-padding" style="margin-top:5px;">
                                        <small class="text-muted">
                                            The value stored against this key. Accessible globally via <code>setting('your_key')</code>.
                                        </small>
                                    </div>
                                    @error('value')
                                    <div class="help-block col-xs-12 col-sm-reset inline">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="clearfix form-actions" style="margin-bottom:0px; background-color: #f5f5f5;">
                                <div class="col-md-offset-3 col-md-9">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Create Setting
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <a href="{{ route('Settings.setting.index') }}" class="btn">
                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
