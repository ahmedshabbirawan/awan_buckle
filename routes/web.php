<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectLedgerController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\Uom\UomController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\DesignationController;
use App\Http\Controllers\Web\Product\ProductController;
use App\Http\Controllers\Stock\StockDeliveryProductController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Stock\ShopWiseProductStockController;



// use App\Http\Controllers\Employee\EmployeeController;

use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Categories\ParentCategoryController;
use App\Http\Controllers\Web\Categories\ProductCategoryController;
use App\Http\Controllers\Web\Categories\SubCategoryController;

use App\Http\Controllers\Web\Auth\RoleController;
use App\Http\Controllers\Web\Auth\PermissionController;
use App\Http\Controllers\Web\Auth\UserController;

use App\Http\Controllers\Stock\StockDeliveryController;
use App\Http\Controllers\Stock\SimpleStockDeliveryController;
use App\Http\Controllers\Issuance\IssuanceController;
use App\Http\Controllers\ReturnItem\ReturnItemController;
use App\Http\Controllers\Sale\SaleBoardController;
use App\Http\Controllers\Sale\SaleOrderController;
use App\Http\Controllers\Stock\StockItemController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Sale\SaleCartController;
use App\Http\Controllers\Sale\SaleReturnController;
use App\Http\Controllers\User\UserRoleController;


use App\Http\Controllers\StockAdjustment\StockAdjustmentController;

use App\Http\Controllers\StockExchange\StockExchangeController;

use App\Http\Controllers\Report\ProductStockController;
use App\Http\Controllers\Sale\Report\SaleReportController;
use App\Http\Controllers\Web\Product\SimpleProductController;
use App\Models\StockItem;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize');

    return "Cleared!";
});



Route::get('/login', [AuthController::class, 'loginPageView'])->name('login');
Route::get('/login-page', [AuthController::class, 'loginPageView'])->name('loginpage');
Route::post('/loginpost', [AuthController::class, 'login'])->name('do-login');





Route::get('/check-test', [ProductStockController::class, 'checkModelAudit']);





Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'view']);
    Route::get('/dashboard', [DashboardController::class, 'view'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard_stats', [DashboardController::class, 'dashboardStats'])->name('dashboard_stats');
    Route::get('/update-product-name', [SimpleProductController::class, 'updateProductName']);
    Route::get('/create_first_purchase', [SimpleProductController::class, 'create_first_purchase']);

    Route::group(['prefix' => 'usermanagement', 'as' => 'usermanagement.'], function () {
        //Role
        Route::group(['prefix' => 'role/', 'as' => 'role.'], function () {
            Route::get('list', [RoleController::class, 'index'])->name('list')->middleware('permission:role.read');
            Route::get('create', [RoleController::class, 'create'])->name('create')->middleware('permission:role.create');
            Route::post('store', [RoleController::class, 'store'])->name('store')->middleware('permission:role.create');
            Route::get('edit/{id}', [RoleController::class, 'edit'])->name('edit')->middleware('permission:role.read');
            Route::post('update/{id}', [RoleController::class, 'update'])->name('update')->middleware('permission:role.update');
            Route::get('status/{id}', [RoleController::class, 'status'])->name('status')->middleware('permission:role.status');
            Route::get('delete/{id}', [RoleController::class, 'destroy'])->name('delete')->middleware('permission:role.delete');

            Route::get('permissions/{id}', [RoleController::class, 'permissions'])->name('permissions')->middleware('permission:user.read');
            Route::post('permission/{id}', [RoleController::class, 'permissionsStore'])->name('permissionStore')->middleware('permission:user.read');
        });

        //Permissions
        Route::group(['prefix' => 'permission/', 'as' => 'permission.'], function () {
            Route::get('list', [PermissionController::class, 'index'])->name('list')->middleware('permission:read.role');
            Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:create.role');
            Route::get('/store', [RoleController::class, 'store'])->name('store')->middleware('permission:create.role');
            Route::get('/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:update.role');
            Route::get('/update', [RoleController::class, 'update'])->name('update')->middleware('permission:update.role');
            Route::get('/status/{id}', [RoleController::class, 'status'])->name('status')->middleware('permission:changestatus.role');
            Route::get('/delete/{id}', [RoleController::class, 'destroy'])->name('delete')->middleware('permission:delete.role');
        });

        //User
        Route::group(['prefix' => 'user/', 'as' => 'user.'], function () {
            Route::get('list', [UserController::class, 'index'])->name('list')->middleware('permission:user.read');
            Route::get('create', [UserController::class, 'create'])->name('create')->middleware('permission:user.create');
            Route::post('store', [UserController::class, 'store'])->name('store')->middleware('permission:user.create');
            Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit')->middleware('permission:user.update');
            Route::post('update/{id}', [UserController::class, 'update'])->name('update')->middleware('permission:user.update');
            Route::get('status/{id}', [UserController::class, 'status'])->name('status')->middleware('permission:user.status');
            Route::get('delete/{id}', [UserController::class, 'destroy'])->name('delete')->middleware('permission:user.delete');
            Route::get('change_password_modal_view/{id}', [UserController::class, 'changePasswordModalView'])->name('change_password_modal')->middleware('permission:user.update');
            Route::post('save_password', [UserController::class, 'savePassword'])->name('save_password')->middleware('permission:user.update');
        });

        Route::group(['prefix' => 'user-role/', 'as' => 'user_role.'], function () {
            Route::get('modal-view-user-shop-role/{id}', [UserRoleController::class, 'assignRoleView']);
            Route::post('save_user_shop_role', [UserRoleController::class, 'assignRolesave'])->name('save_user_shop_role');
            // 
            Route::get('change-user-current-shop', [UserRoleController::class, 'changeUserCurrentShopView']);
            Route::post('change-user-current-shop-save', [UserRoleController::class, 'changeUserCurrentShopSave'])->name('change_user_current_shop_save');
        });
    });


    Route::group(['prefix' => 'product', 'as' => 'product.'], function () {

        Route::get('/list', [ProductController::class, 'index'])->name('list')->middleware('permission:product.read');
        Route::get('/create', [ProductController::class, 'create'])->name('create')->middleware('permission:product.create');
        Route::post('store', [ProductController::class, 'store'])->name('store')->middleware('permission:product.create');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit')->middleware('permission:product.update');
        Route::post('/update/{id}', [ProductController::class, 'update'])->name('update')->middleware('permission:product.update');
        Route::get('/status/{id}', [ProductController::class, 'status'])->name('status')->middleware('permission:product.status');
        Route::get('/delete/{id}', [ProductController::class, 'destroy'])->name('delete')->middleware('permission:product.delete');
        Route::get('/view/{id}', [ProductController::class, 'show'])->name('view')->middleware('permission:product.read');

        Route::get('/ajax/category/sub_category', [ProductController::class, 'getSubCategories'])->name('ajax_sub_cat');
        Route::get('/ajax/category/product_category', [ProductController::class, 'getProductCategories'])->name('ajax_product_cat');
        Route::get('/ajax/attribute/product_attribute', [ProductController::class, 'getProductAttribute'])->name('ajax_product_attribute');
        Route::get('/ajax/ajax_product_by_product_category', [ProductController::class, 'getProductByProductCategory'])->name('ajax_product_by_product_category');
        Route::get('/ajax/category/get_product_cat_detail', [ProductController::class, 'getProductCategoryDetail'])->name('get_product_cat_detail');
        Route::get('/ajax/products', [ProductController::class, 'getProducts'])->name('ajax_products');
    });



    Route::group(['prefix' => 'simple_product', 'as' => 'simple_product.'], function () {
        Route::get('/create_by_modal', [SimpleProductController::class, 'createByModal'])->name('create_by_modal')->middleware('permission:product.create');
        Route::get('/create', [SimpleProductController::class, 'create'])->name('create')->middleware('permission:product.create');
        Route::post('store', [SimpleProductController::class, 'store'])->name('store')->middleware('permission:product.create');
        Route::get('/edit/{id}', [SimpleProductController::class, 'edit'])->name('edit')->middleware('permission:product.update');
        Route::post('/update/{id}', [SimpleProductController::class, 'update'])->name('update')->middleware('permission:product.update');
        Route::get('/ajax/products', [SimpleProductController::class, 'getProducts'])->name('ajax_products');
        Route::get('/import_products_modal', [SimpleProductController::class, 'importProductsModal'])->name('import_products_modal');
        Route::post('/save_import_products', [SimpleProductController::class, 'friday_work'])->name('save_import_products');
    });



    Route::group(['prefix' => 'Settings', 'as' => 'Settings.'], function () {
        Route::resource('warehouse', WarehouseController::class)->middleware('permission:warehouse.read');
        Route::resource('location', LocationController::class)->middleware('permission:warehouse.read');
        Route::resource('uom', UomController::class)->middleware('permission:warehouse.read');
        Route::get('/uom/status/{id}', [UomController::class, 'changeStatus'])->name('uom.changeStatus');

        Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
            Route::get('/list', [EmployeeController::class, 'index'])->name('index')->middleware('permission:employee.read');
            Route::get('/create', [EmployeeController::class, 'create'])->name('create')->middleware('permission:employee.create');
            Route::post('/store', [EmployeeController::class, 'store'])->name('store')->middleware('permission:employee.create');
            Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('edit')->middleware('permission:employee.update');
            Route::post('/update/{id}', [EmployeeController::class, 'update'])->name('update')->middleware('permission:employee.update');
            Route::get('/status/{id}', [EmployeeController::class, 'status'])->name('status')->middleware('permission:employee.status');
            Route::get('/delete/{id}', [EmployeeController::class, 'destroy'])->name('delete')->middleware('permission:employee.delete');
            Route::get('/view/{id}', [EmployeeController::class, 'show'])->name('view')->middleware('permission:employee.read');
            Route::get('/search', [EmployeeController::class, 'search'])->name('search')->middleware('permission:employee.read');
            Route::get('/auto_complete', [EmployeeController::class, 'auto_complete'])->name('auto_complete')->middleware('permission:employee.read');
        });

        Route::group(['prefix' => 'Designations', 'as' => 'designations.'], function () {
            Route::get('/list', [DesignationController::class, 'index'])->name('list')->middleware('permission:designation.read');
            Route::get('/create', [DesignationController::class, 'create'])->name('create')->middleware('permission:designation.create');
            Route::post('/store', [DesignationController::class, 'store'])->name('store')->middleware('permission:designation.create');
            Route::get('/edit/{id}', [DesignationController::class, 'edit'])->name('edit')->middleware('permission:designation.update');
            Route::post('/update/{id}', [DesignationController::class, 'update'])->name('update')->middleware('permission:designation.update');
            Route::get('/status/{id}', [DesignationController::class, 'status'])->name('status')->middleware('permission:designation.status');
            Route::get('/delete/{id}', [DesignationController::class, 'destroy'])->name('delete')->middleware('permission:designation.delete');
            Route::get('/view/{id}', [DesignationController::class, 'show'])->name('view')->middleware('permission:designation.read');
        });

        Route::group(['prefix' => 'org', 'as' => 'org.'], function () {
            Route::get('/list', [OrganizationController::class, 'index'])->name('list')->middleware('permission:organization.read');
            Route::get('/create', [OrganizationController::class, 'create'])->name('create')->middleware('permission:organization.read');
            Route::post('/store', [OrganizationController::class, 'store'])->name('store')->middleware('permission:organization.read');
            Route::get('/edit/{id}', [OrganizationController::class, 'edit'])->name('edit')->middleware('permission:organization.read');
            Route::post('/update/{id}', [OrganizationController::class, 'update'])->name('update')->middleware('permission:organization.read');
            Route::get('/status/{id}', [OrganizationController::class, 'status'])->name('status')->middleware('permission:organization.read');
            Route::get('/delete/{id}', [OrganizationController::class, 'destroy'])->name('delete')->middleware('permission:organization.read');
            Route::get('/view/{id}', [OrganizationController::class, 'show'])->name('view')->middleware('permission:organization.read');
        });


        Route::group(['prefix' => 'project', 'as' => 'project.'], function () {
            Route::get('/list', [ProjectController::class, 'index'])->name('list')->middleware('permission:project.read');
            Route::get('/create', [ProjectController::class, 'create'])->name('create')->middleware('permission:project.create');
            Route::post('/store', [ProjectController::class, 'store'])->name('store')->middleware('permission:project.create');
            Route::get('/edit/{id}', [ProjectController::class, 'edit'])->name('edit')->middleware('permission:project.update');
            Route::post('/update/{id}', [ProjectController::class, 'update'])->name('update')->middleware('permission:project.update');
            Route::get('/status/{id}', [ProjectController::class, 'status'])->name('status')->middleware('permission:project.status');
            Route::get('/delete/{id}', [ProjectController::class, 'destroy'])->name('delete')->middleware('permission:project.delete');
            Route::get('/view/{id}', [ProjectController::class, 'show'])->name('view')->middleware('permission:project.read');
            Route::get('/by-available-stock-and-product-cat', [ProjectController::class, 'getProjectByAvailableStockItemAndProductCat'])->name('by-available-stock-and-product-cat');
            Route::get('sync-project-data', [ProjectController::class, 'sync_project_data'])->middleware('permission:project.update');
        });
    });

    Route::group(['prefix' => 'supplier', 'as' => 'supplier.'], function () {
        Route::get('/list', [SupplierController::class, 'index'])->name('list')->middleware('permission:supplier.read');
        Route::get('/create', [SupplierController::class, 'create'])->name('create')->middleware('permission:supplier.create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store')->middleware('permission:supplier.create');
        Route::get('/edit/{id}', [SupplierController::class, 'edit'])->name('edit')->middleware('permission:supplier.update');
        Route::post('/update/{id}', [SupplierController::class, 'update'])->name('update')->middleware('permission:supplier.update');
        Route::get('/status/{id}', [SupplierController::class, 'status'])->name('status')->middleware('permission:supplier.status');
        Route::get('/delete/{id}', [SupplierController::class, 'destroy'])->name('delete')->middleware('permission:supplier.delete');
        Route::get('/view/{id}', [SupplierController::class, 'show'])->name('view')->middleware('permission:supplier.read');
    });


    Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
        Route::get('/list', [CustomerController::class, 'index'])->name('list'); //->middleware('permission:supplier.read');
        Route::get('/create', [CustomerController::class, 'create'])->name('create'); //->middleware('permission:supplier.create');
        Route::post('/store', [CustomerController::class, 'store'])->name('store'); //->middleware('permission:supplier.create');
        Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit'); //->middleware('permission:supplier.update');
        Route::post('/update/{id}', [CustomerController::class, 'update'])->name('update'); //->middleware('permission:supplier.update');
        Route::get('/status/{id}', [CustomerController::class, 'status'])->name('status'); //->middleware('permission:supplier.status');
        Route::get('/delete/{id}', [CustomerController::class, 'destroy'])->name('delete'); //->middleware('permission:supplier.delete');
        Route::get('/view/{id}', [CustomerController::class, 'show'])->name('view'); //->middleware('permission:supplier.read');
        Route::get('/search', [CustomerController::class, 'search'])->name('search');

        Route::get('/search_select2', [CustomerController::class, 'searchSelectTwo'])->name('search_select2');

        Route::get('/create_view_modal', [CustomerController::class, 'createViewModal'])->name('create_view_modal'); //->middleware('permission:supplier.create');
    });


    Route::group(['prefix' => 'shop', 'as' => 'shop.'], function () {
        Route::get('/list', [ShopController::class, 'index'])->name('list')->middleware('permission:supplier.read');
        Route::get('/create', [ShopController::class, 'create'])->name('create')->middleware('permission:supplier.create');
        Route::post('/store', [ShopController::class, 'store'])->name('store')->middleware('permission:supplier.create');
        Route::get('/edit/{id}', [ShopController::class, 'edit'])->name('edit')->middleware('permission:supplier.update');
        Route::post('/update/{id}', [ShopController::class, 'update'])->name('update')->middleware('permission:supplier.update');
        Route::get('/status/{id}', [ShopController::class, 'status'])->name('status')->middleware('permission:supplier.status');
        Route::get('/delete/{id}', [ShopController::class, 'destroy'])->name('delete')->middleware('permission:supplier.delete');
        Route::get('/view/{id}', [ShopController::class, 'show'])->name('view')->middleware('permission:supplier.read');
    });


    // Route::group(['prefix'=>'attribute','as' => 'attribute.'],function(){
    //   //  Route::group(['prefix'=>'{slug}','as' => '{slug}.'],function(){
    //         Route::get('{slug}/list', [AttributeValueController::class, 'index'] )->name('attribute-list-index')->middleware('permission:attribute.read');
    //         Route::get('{slug}/create', [AttributeValueController::class, 'create'] )->name('create')->middleware('permission:attribute.read');
    //         Route::post('{slug}/store', [AttributeValueController::class, 'store'] )->name('store')->middleware('permission:attribute.read');
    //         Route::get('{slug}/edit/{id}',[AttributeValueController::class,'edit'])->name('edit')->middleware('permission:attribute.update');
    //         Route::post('{slug}/update/{id}',[AttributeValueController::class,'update'])->name('update')->middleware('permission:attribute.update');
    //         Route::get('{slug}/status/{id}',[AttributeValueController::class,'status'])->name('status')->middleware('permission:attribute.status');
    //   //  });
    //     Route::get('/list', [AttributeController::class, 'index'] )->name('list')->middleware('permission:attribute.read');
    // });


    Route::prefix('suppliers')->group(function () {
        Route::get('datatable', [SupplierController::class, 'getDatatable'])->name('suppliers/datatable');
        Route::get('change-status/{eid}', [SupplierController::class, 'changeStatus']);
    });

    Route::prefix('locations')->group(function () {
        Route::get('datatable', [LocationController::class, 'getDatatable'])->name('locations/datatable');
        Route::get('change-status/{eid}', [LocationController::class, 'changeStatus']);
    });

    Route::prefix('stock')->group(function () {
        Route::get('list', [AddStockController::class, 'list']);
        Route::get('create', [AddStockController::class, 'create']);
    });

    Route::prefix('ajax_view')->group(function () {
        Route::get('location_child', [LocationController::class, 'location_child']);
        Route::get('get_province', [LocationController::class, 'getProvince']);
        Route::get('get_city', [LocationController::class, 'getCity']);
        Route::get('get_child_category', [CategoryController::class, 'get_child_category']);
        Route::get('get_child_category_select', [CategoryController::class, 'get_child_category_select']);
        Route::get('get_manager_by_organization', [ManagerController::class, 'getManagerByOrganization']);
    });

    Route::group(['prefix' => 'Category/', 'as' => 'category.'], function () {

        Route::group(['prefix' => 'Parent/', 'as' => 'parent.'], function () {

            Route::get('list', [ParentCategoryController::class, 'list'])->name('list');
            Route::get('create', [ParentCategoryController::class, 'create'])->name('create');
            Route::post('store', [ParentCategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ParentCategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ParentCategoryController::class, 'update'])->name('update');
            Route::get('status/{id}', [ParentCategoryController::class, 'status'])->name('status');
            Route::get('delete/{id}', [ParentCategoryController::class, 'destroy'])->name('destroy');
            Route::get('view/{id}', [ParentCategoryController::class, 'viewDetail'])->name('viewDetail');
        });

        Route::group(['prefix' => 'Sub/', 'as' => 'sub.'], function () {

            Route::get('list', [SubCategoryController::class, 'list'])->name('list');
            Route::get('create', [SubCategoryController::class, 'create'])->name('create');
            Route::post('store', [SubCategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [SubCategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [SubCategoryController::class, 'update'])->name('update');
            Route::get('status/{id}', [SubCategoryController::class, 'status'])->name('status');
            Route::get('delete/{id}', [SubCategoryController::class, 'destroy'])->name('destroy');
            Route::get('view/{id}', [SubCategoryController::class, 'viewDetail'])->name('viewDetail');
            Route::get('getSpecificSubCategories', [SubCategoryController::class, 'getSubCategoryByParentId'])->name('getSubCategoryByParentId'); //Ajax call


        });

        Route::group(['prefix' => 'Product/', 'as' => 'product.'], function () {

            Route::get('list', [ProductCategoryController::class, 'list'])->name('list');
            Route::get('create', [ProductCategoryController::class, 'create'])->name('create');
            Route::post('store', [ProductCategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ProductCategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ProductCategoryController::class, 'update'])->name('update');
            Route::get('status/{id}', [ProductCategoryController::class, 'status'])->name('status');
            Route::get('delete/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy');
            Route::get('view/{id}', [ProductCategoryController::class, 'viewDetail'])->name('viewDetail');

            Route::get('/ajax/products', [ProductController::class, 'getProducts'])->name('ajax_products');
        });
    });




    Route::group(['prefix' => 'Stocks/', 'as' => 'stocks.'], function () {


        Route::get('delivery/create', [StockDeliveryController::class, 'create'])->name('delivery.create');
        Route::post('delivery/save', [StockDeliveryController::class, 'save'])->name('delivery.save');
        Route::get('delivery/list', [StockDeliveryController::class, 'index'])->name('delivery.list');
        Route::get('delivery/{id}', [StockDeliveryController::class, 'detail'])->name('delivery.detail');
        Route::post('delivery/upload_document', [StockDeliveryController::class, 'upload_document'])->name('delivery.upload_document');


        //////
        Route::group(['prefix' => 'simple', 'as' => 'simple.'], function () {
            Route::get('delivery/create', [SimpleStockDeliveryController::class, 'create'])->name('delivery.create');
            Route::post('delivery/save', [SimpleStockDeliveryController::class, 'save'])->name('delivery.save');
            Route::get('delivery/list', [SimpleStockDeliveryController::class, 'index'])->name('delivery.list');
            Route::get('delivery/{id}', [SimpleStockDeliveryController::class, 'detail'])->name('delivery.detail');
            Route::post('delivery/upload_document', [SimpleStockDeliveryController::class, 'upload_document'])->name('delivery.upload_document');
        });
        //////


        Route::get('dp/list', [StockDeliveryProductController::class, 'list'])->name('dp.list');
        Route::post('dp/save', [StockDeliveryProductController::class, 'save'])->name('dp.save');
        Route::get('dp/create', [StockDeliveryProductController::class, 'create'])->name('dp.create');
        Route::get('dp/delete/{id}', [StockDeliveryProductController::class, 'destroy'])->name('dp.delete');
        Route::get('dp/stock-item-by-product/{id}', [StockDeliveryProductController::class, 'getStockItemByProductID'])->name('dp.stock_item_by_product');

        Route::get('dp/download-sample', [StockDeliveryProductController::class, 'downloadSample'])->name('dp.download');
        Route::post('dp/read-sn-file', [StockDeliveryProductController::class, 'readSerialNumber'])->name('dp.read_sn_file');


        // Bill Wise
        Route::get('purchase-return/{id}', [StockDeliveryProductController::class, 'purchaseReturnView'])->name('purchase_return_view');
        Route::post('purchase-return-save', [StockDeliveryProductController::class, 'purchaseReturnSave'])->name('purchase_return_save');

        // Unknow Wise
        Route::get('unknow-purchase-return', [StockDeliveryProductController::class, 'unknowPurchaseReturnView'])->name('unknow_purchase_return_view');
        Route::post('unknow-purchase-return-save', [StockDeliveryProductController::class, 'unknowPurchaseReturnSave'])->name('unknow_purchase_return_save');

        Route::get('purchase-return-list', [StockDeliveryProductController::class, 'purchaseReturnList'])->name('purchase_return_list');


        //--------------------------------------
        Route::get('item/search', [StockItemController::class, 'searchStockItem'])->name('item.search');
        Route::get('item/available_item_by_product_ajax', [StockItemController::class, 'available_item_by_product_ajax'])->name('item.by_product');
        Route::get('item/issuance_history', [StockItemController::class, 'itemIssuanceHistory'])->name('item.issuance_history');
        Route::get('item/all_item_by_product_datatable', [StockItemController::class, 'all_item_by_product_datatable'])->name('all_item.by_product');

        Route::get('item/history/{id}', [StockItemController::class, 'itemHistory'])->name('item.history');
        Route::get('shop_wise_product_stock/list', [ShopWiseProductStockController::class, 'shopWiseStock'])->name('shop_wise_product_stock.list');
    });

    Route::group(['prefix' => 'Issuance/', 'as' => 'issuance.'], function () {
        Route::get('list', [IssuanceController::class, 'index'])->name('list');
        Route::get('view/{key}', [IssuanceController::class, 'detail'])->name('detail');
        Route::get('create', [IssuanceController::class, 'create'])->name('create');
        Route::post('save', [IssuanceController::class, 'save'])->name('save');
        Route::post('items-return', [IssuanceController::class, 'itemReturn'])->name('items-return');
        // 
        Route::post('submit_item_remarks', [IssuanceController::class, 'submitRemarks'])->name('submit_item_remarks');
        Route::post('upload_hard_copy', [IssuanceController::class, 'upload_hard_copy'])->name('upload_hard_copy');

        Route::get('item-by-employee', [IssuanceController::class, 'itemByEmployee'])->name('item_by_employee');
        Route::group(['prefix' => 'temp_item/', 'as' => 'temp_item.'], function () {
            Route::post('add', [IssuanceController::class, 'addItemToSession'])->name('add');
            Route::post('add_by_sn', [IssuanceController::class, 'addItemBySN'])->name('add_by_sn');
            Route::get('remove', [IssuanceController::class, 'removeItemFromSession'])->name('remove');
            Route::get('list', [IssuanceController::class, 'getItemFromSession'])->name('list');
        });
    });

    Route::group(['prefix' => 'Return/', 'as' => 'return.'], function () {
        Route::get('list', [ReturnItemController::class, 'index'])->name('list');
        Route::get('detail/{id}', [ReturnItemController::class, 'detail'])->name('detail');
    });




    Route::group(['prefix' => 'sale/', 'as' => 'sale.'], function () {


        Route::group(['prefix' => 'cart/', 'as' => 'cart.'], function () {
            Route::get('get-cart-item', [SaleCartController::class, 'index'])->name('index');
            Route::get('get-bill-total-amount', [SaleCartController::class, 'getBillTotalAmounts'])->name('get_bill_total_amounts');
            Route::post('add-to-cart', [SaleCartController::class, 'addToCart'])->name('add');
            Route::post('delete-cart-item', [SaleCartController::class, 'deleteCartItem'])->name('delete_cart_item');
            Route::get('preview/{sale_key}', [SaleCartController::class, 'cartPreview'])->name('preview');
            Route::post('place_order', [SaleCartController::class, 'finalPlaceOrder'])->name('place_order');

            Route::post('attach_customer_with_cart', [SaleCartController::class, 'attachCustomerWithCart'])->name('attach_customer_with_cart');
            Route::get('search_product', [SaleCartController::class, 'searchProducts'])->name('search_product');
            Route::post('apply_dicount_on_single_product', [SaleCartController::class, 'applyDiscountOnSingleProduct'])->name('apply_dicount_single');
            Route::post('update_price_on_single_product', [SaleCartController::class, 'updatePriceOnSingleProduct'])->name('update_product_price');
        });

        Route::group(['prefix' => 'board/', 'as' => 'board.'], function () {
            Route::get('product_items', [SaleBoardController::class, 'getProductItems'])->name('product_items');
            Route::get('list', [SaleBoardController::class, 'index'])->name('list');
            Route::get('on_hold_modal_view', [SaleBoardController::class, 'onHoldModalView'])->name('on_hold_modal_view');
            Route::get('create/{sale_key?}', [SaleBoardController::class, 'create'])->name('create');
            Route::get('delete/board_item/{sale_key?}', [SaleBoardController::class, 'deleteKeyItems'])->name('delete.board_item');
        });



        Route::group(['prefix' => 'order/', 'as' => 'order.'], function () {
            Route::get('list', [SaleOrderController::class, 'index'])->name('list');
            Route::get('detail/{id}', [SaleOrderController::class, 'detail'])->name('detail');
            Route::get('order/search_order', [SaleOrderController::class, 'detail'])->name('order.search_order');
            Route::get('order_back_search_modal', [SaleOrderController::class, 'OrderBackSearchModal'])->name('order_back_search_modal');
            Route::get('sale-return/{id}', [SaleOrderController::class, 'saleReturnView'])->name('sale_return_view');
            Route::post('sale-return-save', [SaleOrderController::class, 'saleReturnSave'])->name('sale_return_save');
            Route::get('unknow-sale-return', [SaleOrderController::class, 'unknowSaleReturnView'])->name('unkonw_sale_return_view');
            Route::post('unknow-sale-return-save', [SaleOrderController::class, 'unknowSaleReturnSave'])->name('unkonw_sale_return_save');
        });

        Route::group(['prefix' => 'sale-return/', 'as' => 'sale_return.'], function () {
            Route::get('list', [SaleReturnController::class, 'index'])->name('list');
            Route::get('detail/{id}', [SaleReturnController::class, 'detail'])->name('detail');
        });
    });




    // StockExchangeController  
    Route::group(['prefix' => 'stock-exchange/', 'as' => 'stock_exchange.'], function () {

        Route::post('add_item_to_exchange', [StockExchangeController::class, 'addItemToExchange'])->name('add_item_to_exchange');
        Route::get('get_request_cart_data', [StockExchangeController::class, 'getRequestCart'])->name('get_request_cart_data');
        Route::post('delete_cart_item', [StockExchangeController::class, 'deleteCartItem'])->name('delete_cart_item');
        Route::post('place_request', [StockExchangeController::class, 'placeRequest'])->name('place_request');
        Route::get('records', [StockExchangeController::class, 'getRecords'])->name('records');
        Route::post('mark_approve_cart_item', [StockExchangeController::class, 'markApproveReq'])->name('mark_approve_cart_item');
        Route::get('list', [StockExchangeController::class, 'indexList'])->name('list');
        Route::get('create_req_view_modal', [StockExchangeController::class, 'createReqViewModal'])->name('create_req_view_modal');
        Route::get('request-form', [StockExchangeController::class, 'requestForm'])->name('req_form');

        /*

        Route::post('request-post-form',[StockExchangeController::class,'requestPost'])->name('req_post');
        Route::get('request-send-list',[StockExchangeController::class,'requestSendList'])->name('req_send_list');
        Route::get('request-receive-list',[StockExchangeController::class,'requestReceiveList'])->name('req_receive_list');
        Route::get('request-detail/{exchange_key}',[StockExchangeController::class,'requestDetail'])->name('req_detail');
        Route::post('request-aprove',[StockExchangeController::class,'requestAprove'])->name('req_aprove');
        Route::get('request-deny',[StockExchangeController::class,'requestDeny'])->name('req_deny');
        
        Route::get('darft-list',[StockExchangeController::class,'darftList'])->name('darft_list');
        Route::post('final-submit',[StockExchangeController::class,'finalSubmit'])->name('final_submit');
        

        Route::get('request-mark-receive/{exchange_key}',[StockExchangeController::class,'markReceive'])->name('mark_receive');
        */
    });




    Route::group(['prefix' => 'stock-adjustment/', 'as' => 'stock_adjustment.'], function () {
        Route::get('form', [StockAdjustmentController::class, 'form'])->name('form');
        Route::post('save', [StockAdjustmentController::class, 'save'])->name('save');
        Route::get('list', [StockAdjustmentController::class, 'index'])->name('list');
    });

    Route::group(['prefix' => 'report/', 'as' => 'report.'], function () {
        Route::group(['prefix' => 'sale/', 'as' => 'sale.'], function () {
            Route::get('all', [SaleReportController::class, 'all'])->name('all');
        });

        // OLD
        Route::get('product', [ProductStockController::class, 'product'])->name('product');
        Route::get('product_wise', [ProductStockController::class, 'productWise'])->name('product_wise');
        Route::get('product_order_table', [ProductStockController::class, 'saleOrder'])->name('product_order_table');
        Route::get('product_purchase_table', [ProductStockController::class, 'purchase'])->name('product_purchase_table');
    });



    Route::group(['prefix' => 'report/', 'as' => 'report.'], function () {
        Route::group(['prefix' => 'sale/', 'as' => 'sale.'], function () {
            Route::get('all', [SaleReportController::class, 'all'])->name('all');
        });
    });
}); // main auth end
