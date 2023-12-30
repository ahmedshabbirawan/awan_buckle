<option value="" >-- Select --</option>
<?php foreach($items as $row): ?>

    <?php 
        
        $product = $row->itemProduct;

      //   print_r($product->parent_cat_id); exit;

        $expire = '';

        if($product->parent_cat_id ==  1){
            $expire = ($product->warranty_date)? ' - ('.$product->warranty_date.') ' :''; 
        }
        
        ?>

    <option value="<?=$row->id?>" data-qty="{{ $row->available_qty }}"  >{{ $row->serial_number }} - ({{ $row->available_qty }}) {{ $expire }} </option>
<?php endforeach; ?>