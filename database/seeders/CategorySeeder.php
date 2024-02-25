<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        $parent_query = "INSERT INTO `parent_category` (`id`, `name`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
        (1, 'Common Parent Category', 1, NULL, NULL, NULL, NULL, NULL, NULL)";
        \DB::statement($parent_query);

        $sub_query = "INSERT INTO `sub_category` (`id`, `parentCategoryId`, `name`, `is_tagable`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
        (1, 1, 'Common Sub Category', 'n', 1, NULL, NULL, NULL, NULL, NULL, NULL)";
        \DB::statement($sub_query);

        $product_query = "INSERT INTO `product_category` (`id`, `parentCategoryId`, `subCategoryId`, `name`, `enable_alert`, `sn_require`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
        (1, 1, 1, 'Books', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL)";
        \DB::statement($product_query);


    }
}
