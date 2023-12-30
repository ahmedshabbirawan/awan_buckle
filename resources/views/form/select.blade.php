<select class="col-xs-10 col-sm-5 form-control" name="{{ $name }}" id="{{ $name }}" {{ $attribute }} >
	<option value=""> -- Select -- </option>
	<?php foreach($list as $id => $title): ?>
			<option value="<?=$id?>" <?=($selectedVal == $id)?'selected="selected"':''?> ><?=$title?></option>
	<?php endforeach;?>
</select>