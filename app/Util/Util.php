<?php

namespace App\Util;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\AttributeType;

class Util extends Model{
    use HasFactory;	
   
    static function sidebarItem(){


        $setting = [
            ['key' => 'warehouse', 'url' => '/warehouse', 'label' => 'Warehouse', 'icon' => 'fa fa-home' ],
            ['key' => 'uom', 'url' => route('attribute.UOM.list'), 'label' => 'UOM', 'icon' => 'fa fa-barcode' ],
            ['key' => 'attribute', 'url' => '/attribute/list', 'label' => 'Attribute', 'icon' => 'fa fa-circle-o' ],
            
        ];


        $hr = [
            ['key' => 'organization', 'url' => '/org/list', 'label' => 'Organization', 'icon' => 'fa fa-circle-o' ],
            ['key' => 'project', 'url' => route('project.list') , 'label' => 'Project', 'icon' => 'fa fa-circle-o' ],
            ['key' => 'employee', 'url' => '/employee', 'label' => 'Employee', 'icon' => 'fa fa-users' ],
        ];





        return [
         //   ['key' => 'dashboard', 'url' => '/Dashboard', 'label' => 'DashBoard', 'icon' => 'fa fa-tachometer' ],
          //  ['key' => 'product', 'url' => '/product', 'label' => 'Product', 'icon' => 'fa fa-gift' ],
          //  ['key' => 'stock', 'url' => '/stock/list', 'label' => 'Stock', 'icon' => 'fa fa-gift' ],
          //   ['key' => 'category', 'url' => '/category', 'label' => 'Category', 'icon' => 'fa fa-circle-o' ],
            ['key' => 'hr', 'url' => 'javascript:void(0);', 'label' => 'HR', 'icon' => 'fa fa-cogs', 'childs' => $hr ],
            ['key' => 'location', 'url' => '/location', 'label' => 'Location', 'icon' => 'fa fa-globe' ],
            ['key' => 'supplier', 'url' => route('supplier.list'), 'label' => 'Supplier', 'icon' => 'fa fa-cloud-download' ],
            ['key' => 'setting', 'url' => 'javascript:void(0);', 'label' => 'Setting', 'icon' => 'fa fa-cogs', 'childs' => $setting ],
            
        ];
    }



    public static function getAttributeList(){
        return AttributeType::where('status',1)->whereNotNull('slug')->orderBy('id','ASC')->get();
    }
    


    static function statusLabel($id){
        if($id == 0){
            return 'InActive';
        }elseif($id == 1){
            return 'Active';
        }
    }


    static function designations(){
        return ['Store Manager', 'Store Keeper', 'Gate Keeper'];
      }




}