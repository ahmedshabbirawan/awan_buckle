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
        Permission::updateOrCreate(['id'=>5],['name'=>'user.status','description'=>'Status User','guard_name'=>'web']);


        /* Role & Permissions */
        /* Role */
        Permission::updateOrCreate(['id'=>6],['name'=>'role.create','description'=>'Create role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>7],['name'=>'role.read','description'=>'Read role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>8],['name'=>'role.update','description'=>'Edit role','guard_name'=>'web']);
       //  Permission::updateOrCreate(['id'=>119],['name'=>'role.delete','description'=>'Delete role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>9],['name'=>'role.status','description'=>'Status role','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>10],['name'=>'role.assign','description'=>'Role Assign','guard_name'=>'web']);
        /* Permission */
        Permission::updateOrCreate(['id'=>11],['name'=>'permission.read','description'=>'Assign Role','guard_name'=>'web']);


        /* Permission - Shop */
        Permission::updateOrCreate(['id'=>12],['name'=>'shop.create','description'=>'Create Shop','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>13],['name'=>'shop.read','description'=>'Read Shop','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>14],['name'=>'shop.update','description'=>'Edit Shop','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>15],['name'=>'shop.delete','description'=>'Delete Shop','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>16],['name'=>'shop.status','description'=>'Status Shop','guard_name'=>'web']);


        /* Permission - product */
        Permission::updateOrCreate(['id'=>17],['name'=>'product.create','description'=>'Create product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>18],['name'=>'product.read','description'=>'Read product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>19],['name'=>'product.update','description'=>'Edit product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>20],['name'=>'product.delete','description'=>'Delete product','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>21],['name'=>'product.status','description'=>'Status product','guard_name'=>'web']);

        /* Permission - POS */
        Permission::updateOrCreate(['id'=>22],['name'=>'pos.create','description'=>'Create POS','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>23],['name'=>'pos.read','description'=>'Read POS','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>24],['name'=>'pos.update','description'=>'Edit POS','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>25],['name'=>'pos.delete','description'=>'Delete POS','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>26],['name'=>'pos.status','description'=>'Status POS','guard_name'=>'web']);
        

        /* Permission - Sale */
        Permission::updateOrCreate(['id'=>27],['name'=>'sale.create','description'=>'Create Sale','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>28],['name'=>'sale.read','description'=>'Read Sale','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>29],['name'=>'sale.update','description'=>'Edit Sale','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>30],['name'=>'sale.delete','description'=>'Delete Sale','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>31],['name'=>'sale.status','description'=>'Status Sale','guard_name'=>'web']);

        /* Permission - Purchase */ 
        Permission::updateOrCreate(['id'=>32],['name'=>'purchase.create','description'=>'Create Purchase','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>33],['name'=>'purchase.read','description'=>'Read Purchase','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>34],['name'=>'purchase.update','description'=>'Edit Purchase','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>35],['name'=>'purchase.delete','description'=>'Delete Purchase','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>36],['name'=>'purchase.status','description'=>'Status Purchase','guard_name'=>'web']);



        /* Permission - Stock Exchange */
        Permission::updateOrCreate(['id'=>37],['name'=>'stock_exchange.create','description'=>'Create Stock Exchange','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>38],['name'=>'stock_exchange.read','description'=>'Read Stock Exchange','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>39],['name'=>'stock_exchange.update','description'=>'Edit Stock Exchange','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>40],['name'=>'stock_exchange.delete','description'=>'Delete Stock Exchange','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>41],['name'=>'stock_exchange.status','description'=>'Status Stock Exchange','guard_name'=>'web']);


        /* Permission - Stock Adjustment */
        Permission::updateOrCreate(['id'=>42],['name'=>'stock_adjustment.create','description'=>'Create Stock Adjustment','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>43],['name'=>'stock_adjustment.read','description'=>'Read Stock Adjustment','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>44],['name'=>'stock_adjustment.update','description'=>'Edit Stock Adjustment','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>45],['name'=>'stock_adjustment.delete','description'=>'Delete Stock Adjustment','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>46],['name'=>'stock_adjustment.status','description'=>'Status Stock Adjustment','guard_name'=>'web']);


        /* Permission - warehouse */
        Permission::updateOrCreate(['id'=>47],['name'=>'warehouse.create','description'=>'Create warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>48],['name'=>'warehouse.read','description'=>'Read warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>49],['name'=>'warehouse.update','description'=>'Edit warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>50],['name'=>'warehouse.delete','description'=>'Delete warehouse','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>51],['name'=>'warehouse.status','description'=>'Status warehouse','guard_name'=>'web']);
       
        /* Permission - location */
        Permission::updateOrCreate(['id'=>52],['name'=>'location.create','description'=>'Create location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>53],['name'=>'location.read','description'=>'Read location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>54],['name'=>'location.update','description'=>'Edit location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>55],['name'=>'location.delete','description'=>'Delete location','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>56],['name'=>'location.status','description'=>'Status location','guard_name'=>'web']);

        /* Permission - uom */
        Permission::updateOrCreate(['id'=>57],['name'=>'uom.create','description'=>'Create uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>58],['name'=>'uom.read','description'=>'Read uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>59],['name'=>'uom.update','description'=>'Edit uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>60],['name'=>'uom.delete','description'=>'Delete uom','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>61],['name'=>'uom.status','description'=>'Status uom','guard_name'=>'web']);

        /* Permission - attribute */
        Permission::updateOrCreate(['id'=>62],['name'=>'attribute.create','description'=>'Create attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>63],['name'=>'attribute.read','description'=>'Read attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>64],['name'=>'attribute.update','description'=>'Edit attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>65],['name'=>'attribute.delete','description'=>'Delete attribute','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>66],['name'=>'attribute.status','description'=>'Status attribute','guard_name'=>'web']);

        /* Permission - supplier */
        Permission::updateOrCreate(['id'=>67],['name'=>'supplier.create','description'=>'Create supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>68],['name'=>'supplier.read','description'=>'Read supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>69],['name'=>'supplier.update','description'=>'Edit supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>70],['name'=>'supplier.delete','description'=>'Delete supplier','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>71],['name'=>'supplier.status','description'=>'Status supplier','guard_name'=>'web']);

        /* Permission - Customers */
        Permission::updateOrCreate(['id'=>72],['name'=>'customer.create','description'=>'Create Customer','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>73],['name'=>'customer.read','description'=>'Read Customer','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>74],['name'=>'customer.update','description'=>'Edit Customer','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>75],['name'=>'customer.delete','description'=>'Delete Customer','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>76],['name'=>'customer.status','description'=>'Status Customer','guard_name'=>'web']);

        // Parent Category // Sub Category // Product Category 
        /* Permission - parent_category */ 
        Permission::updateOrCreate(['id'=>77],['name'=>'parent_category.create','description'=>'Create parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>78],['name'=>'parent_category.read','description'=>'Read parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>79],['name'=>'parent_category.update','description'=>'Edit parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>80],['name'=>'parent_category.delete','description'=>'Delete parent_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>81],['name'=>'parent_category.status','description'=>'Status parent_category','guard_name'=>'web']);

        /* Permission - sub_category */
        Permission::updateOrCreate(['id'=>82],['name'=>'sub_category.create','description'=>'Create sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>83],['name'=>'sub_category.read','description'=>'Read sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>84],['name'=>'sub_category.update','description'=>'Edit sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>85],['name'=>'sub_category.delete','description'=>'Delete sub_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>86],['name'=>'sub_category.status','description'=>'Status sub_category','guard_name'=>'web']);

        /* Permission - product_category */
        Permission::updateOrCreate(['id'=>87],['name'=>'product_category.create','description'=>'Create product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>88],['name'=>'product_category.read','description'=>'Read product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>89],['name'=>'product_category.update','description'=>'Edit product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>90],['name'=>'product_category.delete','description'=>'Delete product_category','guard_name'=>'web']);
        Permission::updateOrCreate(['id'=>91],['name'=>'product_category.status','description'=>'Status product_category','guard_name'=>'web']);

        Role::updateOrCreate(['id'=>1],['name'=>'Super Admin','guard_name'=>'web','status'=>1]);
        
        
        $AllPermissions = Permission::get();
        $superAdmin = Role::where('id',1)->first();
        $superAdmin->syncPermissions($AllPermissions);
        $superAdminUser = User::where('id',1)->first();
        $superAdminUser->syncRoles($superAdmin);

    }
}
