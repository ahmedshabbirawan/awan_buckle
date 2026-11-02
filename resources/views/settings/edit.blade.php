@extends('Layout.master')

@section('title')
Edit System Setting
@endsection

@section('content')
<div class="page-content">

    <div class="page-header" style="min-height:40px;">
        <div style="float: left;">
            <h1>
                System Settings
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    Edit Setting: <code>{{ $setting->key }}</code>
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
                        <i class="ace-icon fa fa-pencil-square-o"></i>
                        Edit Setting: <code>{{ $setting->key }}</code>
                    </h4>
                    @if($setting->isEssential())
                    <span class="widget-toolbar">
                        <span class="badge badge-warning">
                            <i class="fa fa-lock"></i> Protected System Setting
                        </span>
                    </span>
                    @endif
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <form method="POST" action="{{ route('Settings.setting.update', $setting->id) }}" class="form-horizontal">
                            @csrf

                            @if($setting->isEssential())
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="key">
                                    <strong>Setting Key</strong>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" id="key" value="{{ $setting->key }}" class="form-control col-xs-12 col-sm-8" readonly style="background-color:#eee; cursor:not-allowed;" />
                                    <div class="col-xs-12 no-padding" style="margin-top:5px;">
                                        <small class="text-warning">
                                            <i class="fa fa-lock"></i> Essential system key names cannot be renamed to preserve application consistency.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="form-group @error('key') has-error @enderror">
                                <label class="col-sm-3 control-label no-padding-right" for="key">
                                    <strong>Setting Key *</strong>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" id="key" name="key" value="{{ old('key', $setting->key) }}" class="form-control col-xs-12 col-sm-8" required />
                                    <div class="col-xs-12 no-padding" style="margin-top:5px;">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> Use lowercase letters, numbers, and underscores only.
                                        </small>
                                    </div>
                                    @error('key')
                                    <div class="help-block col-xs-12 col-sm-reset inline">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="form-group @error('value') has-error @enderror">
                                <label class="col-sm-3 control-label no-padding-right" for="value">
                                    <strong>Setting Value</strong>
                                </label>
                                <div class="col-sm-9">
                                    <textarea id="value" name="value" class="form-control col-xs-12 col-sm-8" rows="5" placeholder="Enter configuration value">{{ old('value', $setting->value) }}</textarea>
                                    <div class="col-xs-12 no-padding" style="margin-top:5px;">
                                        <small class="text-muted">
                                            Accessed across views, controllers, and services using <code>setting('{{ $setting->key }}')</code>.
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
                                        Update Setting
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
