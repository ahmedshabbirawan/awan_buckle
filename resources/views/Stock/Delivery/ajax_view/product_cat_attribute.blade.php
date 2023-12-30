<?php foreach($attributes as $row): ?>
    <div class="col-lg-3 col-sm-3">
        <label class="" for="form-field-1"> {{ $row->name }} </label>
        <div class="">
            <select name="attribute_value_ids[]" id="attribute_value_id_<?=$row->id?>" required class="col-xs-10 col-sm-5 form-control attributes_id">
                <option value=""> -- Select -- </option>
                <?php foreach($row->attributeValue as $subRow): ?>
                    <option value="<?=$subRow->id?>" <?=( in_array($subRow->id,$selected_id)?'selected="selected"':'' )?> ><?=$subRow->value?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
<?php endforeach; ?>