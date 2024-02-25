<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(['id'=>1],['name'=>'admin','email'=>'admin@sellme.pk','userName'=>'admin','password'=>'$2y$10$RnHh5.2nR99CTLxpTxc2du.7SwcqO4wI8YVPYHJCzvqnD4ojRRNa.','status'=>1]);
        // lahore
        /* Permission - user */
        Permission::updateOrCreate(['id'=>1],['name'=>'user.create','description'=>'Create User','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>2],['name'=>'user.read','description'=>'Read User','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>3],['name'=>'user.update','description'=>'Edit User','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>4],['name'=>'user.delete','description'=>'Delete User','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>5],['name'=>'user.status','description'=>'Delete User','guard_name'=>'web']);



        /* Permission - warehouse */
        Permission::updateOrCreate(['id'=>36],['name'=>'warehouse.create','description'=>'Create warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>37],['name'=>'warehouse.read','description'=>'Read warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>38],['name'=>'warehouse.update','description'=>'Edit warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>39],['name'=>'warehouse.delete','description'=>'Delete warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>40],['name'=>'warehouse.status','description'=>'Delete warehouse','guard_name'=>'web']);
       
        /* Permission - location */
        Permission::updateOrCreate(['id'=>41],['name'=>'location.create','description'=>'Create location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>42],['name'=>'location.read','description'=>'Read location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>43],['name'=>'location.update','description'=>'Edit location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>44],['name'=>'location.delete','description'=>'Delete location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>45],['name'=>'location.status','description'=>'Delete location','guard_name'=>'web']);

        /* Permission - uom */
        Permission::updateOrCreate(['id'=>46],['name'=>'uom.create','description'=>'Create uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>47],['name'=>'uom.read','description'=>'Read uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>48],['name'=>'uom.update','description'=>'Edit uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>49],['name'=>'uom.delete','description'=>'Delete uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>50],['name'=>'uom.status','description'=>'Delete uom','guard_name'=>'web']);

        /* Permission - attribute */
        Permission::updateOrCreate(['id'=>51],['name'=>'attribute.create','description'=>'Create attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>52],['name'=>'attribute.read','description'=>'Read attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>53],['name'=>'attribute.update','description'=>'Edit attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>54],['name'=>'attribute.delete','description'=>'Delete attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>55],['name'=>'attribute.status','description'=>'Delete attribute','guard_name'=>'web']);

        /* Permission - supplier */
        Permission::updateOrCreate(['id'=>56],['name'=>'supplier.create','description'=>'Create supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>57],['name'=>'supplier.read','description'=>'Read supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>58],['name'=>'supplier.update','description'=>'Edit supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>59],['name'=>'supplier.delete','description'=>'Delete supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>50],['name'=>'supplier.status','description'=>'Delete supplier','guard_name'=>'web']);

        // Parent Category // Sub Category // Product Category 
        /* Permission - parent_category */ 
        Permission::updateOrCreate(['id'=>71],['name'=>'parent_category.create','description'=>'Create parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>72],['name'=>'parent_category.read','description'=>'Read parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>73],['name'=>'parent_category.update','description'=>'Edit parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>74],['name'=>'parent_category.delete','description'=>'Delete parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>75],['name'=>'parent_category.status','description'=>'Delete parent_category','guard_name'=>'web']);

        /* Permission - sub_category */
        Permission::updateOrCreate(['id'=>76],['name'=>'sub_category.create','description'=>'Create sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>77],['name'=>'sub_category.read','description'=>'Read sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>78],['name'=>'sub_category.update','description'=>'Edit sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>79],['name'=>'sub_category.delete','description'=>'Delete sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>80],['name'=>'sub_category.status','description'=>'Delete sub_category','guard_name'=>'web']);

        /* Permission - product_category */
        Permission::updateOrCreate(['id'=>91],['name'=>'product_category.create','description'=>'Create product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>92],['name'=>'product_category.read','description'=>'Read product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>93],['name'=>'product_category.update','description'=>'Edit product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>94],['name'=>'product_category.delete','description'=>'Delete product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>95],['name'=>'product_category.status','description'=>'Delete product_category','guard_name'=>'web']);

        /* Permission - product */
        Permission::updateOrCreate(['id'=>96],['name'=>'product.create','description'=>'Create product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>97],['name'=>'product.read','description'=>'Read product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>98],['name'=>'product.update','description'=>'Edit product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>99],['name'=>'product.delete','description'=>'Delete product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>100],['name'=>'product.status','description'=>'Delete product','guard_name'=>'web']);

        /* Permission - stock_delivery */ 
        Permission::updateOrCreate(['id'=>101],['name'=>'stock_delivery.create','description'=>'Create stock_delivery','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>102],['name'=>'stock_delivery.read','description'=>'Read stock_delivery','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>103],['name'=>'stock_delivery.update','description'=>'Edit stock_delivery','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>104],['name'=>'stock_delivery.delete','description'=>'Delete stock_delivery','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>105],['name'=>'stock_delivery.status','description'=>'Delete stock_delivery','guard_name'=>'web']);

        /* Permission - stock_issuance */
        Permission::updateOrCreate(['id'=>106],['name'=>'stock_issuance.create','description'=>'Create stock_issuance','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>107],['name'=>'stock_issuance.read','description'=>'Read stock_issuance','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>108],['name'=>'stock_issuance.update','description'=>'Edit stock_issuance','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>109],['name'=>'stock_issuance.delete','description'=>'Delete stock_issuance','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>110],['name'=>'stock_issuance.status','description'=>'Delete stock_issuance','guard_name'=>'web']);

        /* Permission - item_return */ 
        Permission::updateOrCreate(['id'=>111],['name'=>'item_return.create','description'=>'Create item_return','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>112],['name'=>'item_return.read','description'=>'Read item_return','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>113],['name'=>'item_return.update','description'=>'Edit item_return','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>114],['name'=>'item_return.delete','description'=>'Delete item_return','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>115],['name'=>'item_return.status','description'=>'Delete item_return','guard_name'=>'web']);
        

        /* Role & Permissions */
        /* Role */
        Permission::updateOrCreate(['id'=>116],['name'=>'role.create','description'=>'Create role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>117],['name'=>'role.read','description'=>'Read role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>118],['name'=>'role.update','description'=>'Edit role','guard_name'=>'web']);
       //  Permission::updateOrCreate(['id'=>119],['name'=>'role.delete','description'=>'Delete role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>120],['name'=>'role.status','description'=>'Delete role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>121],['name'=>'role.assign','description'=>'Role Assign','guard_name'=>'web']);
        /* Permission */
        Permission::updateOrCreate(['id'=>122],['name'=>'permission.read','description'=>'Assign Role','guard_name'=>'web']);
        







        Role::updateOrCreate(['id'=>1],['name'=>'Super Admin','guard_name'=>'web','status'=>1]);
        
        
        $AllPermissions = Permission::get();
        $superAdmin = Role::where('id',1)->first();
        $superAdmin->syncPermissions($AllPermissions);
        $superAdminUser = User::where('id',1)->first();
        $superAdminUser->syncRoles($superAdmin);

    }
}
