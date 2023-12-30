<option value=""> -- Select -- </option>
<?php foreach($subCategories as $val => $title): ?>
    <option value="<?=$val?>" data-dd="{{ $selected_id }}" <?= ($val == $selected_id)?'selected="selected"':'' ?> ><?=$title?></option>
<?php endforeach; ?>