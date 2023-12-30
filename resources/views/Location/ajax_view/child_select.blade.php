<?php if($request['type'] == 'PR'){ ?>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Type </label>
            <div class="col-sm-9">
                {{ \App\Util\Form::select('type',$countries,old('type'), 'onchange=selectChild(this.val)') }}
                @error('type')<div class="alert alert-danger">{{ $message }}</div>@enderror
            </div>
        </div>
<?php }elseif($request->get('type') == 'CI'){
    $this->viewData['countries']    = Location::where('loc_id',0)->get();
    $this->viewData['province']     = []; //Location::where('loc_id',0)->get();
}

?>