<div id="sidebar" class="sidebar responsive ace-save-state">
        <script type="text/javascript">
            try{ace.settings.loadState('sidebar')}catch(e){}
        </script>

        <ul class="nav nav-list">

            <!--------------- Dashboard-------------------->
            <li class="{{request()->is('dashboard*') ? 'active' : '' }}">
                <a href="{{route('dashboard')}}">
                    <i class="menu-icon fa fa-desktop"></i>
                    <span class="menu-text"> Dashboard </span>
                </a>

                <b class="arrow"></b>
            </li>
            <!---------------End Dashboard-------------------->

            <!--------------- Sale Board -------------------->
            <li class="{{request()->is('sale/board*') ? 'active' : '' }}">
                <a href="{{route('sale.board.create')}}">
                    <i class="menu-icon fa fa-cart-plus"></i>
                    <span class="menu-text"> POS </span>
                </a>

                <b class="arrow"></b>
            </li>
            <!---------------Sale Board End-------------------->

            <!--------------- Stock Exchange -------------------->
            <li class="{{request()->is('stock_exchange/*') ? 'active' : '' }}">
                <a href="{{route('stock_exchange.list')}}">
                    <i class="menu-icon fa fa-exchange"></i>
                    <span class="menu-text"> Stock Exchange </span>
                </a>

                <b class="arrow"></b>
            </li>
            <!---------------Sale Board End-------------------->


            <!--------------- Stock Adjustment -------------------->
            <li class="{{request()->is('stock_exchange/*') ? 'active' : '' }}">
                <a href="{{route('stock_adjustment.list')}}">
                    <i class="menu-icon fa fa-balance-scale"></i>
                    <span class="menu-text"> Stock Adjust </span>
                </a>
                <b class="arrow"></b>
            </li>
            <!---------------Sale Board End-------------------->

             <!--------------- Products -------------------->
             <li class="{{request()->is('product*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon glyphicon glyphicon-qrcode "></i>
                    <span class="menu-text">
                        Products
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    @can('product.read')
                    <li class="{{request()->is('Product/list*') ? 'active' : '' }}">
                        <a href="{{ route('product.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a>
                        <b class="arrow"></b>
                    </li>
                    @endcan

                    @can('product.create')
                    <li class="{{request()->is('Products/create*') ? 'active' : '' }}">
                        <a href="{{ route('product.create') }}"><i class="menu-icon fa fa-caret-right"></i>Quick Add</a>
                        <b class="arrow"></b>
                    </li>
                    @endcan
                </ul>
            </li>
            <!--------------- End Products-------------------->

        
            <!--------------- Sale Start -------------------->
            <li class="{{request()->is('sale/order/*') || request()->is('sale/sale-return/*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon glyphicon glyphicon-qrcode "></i>
                    <span class="menu-text">
                        Sale
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    
                    <li class="{{request()->is('sale/order/*') ? 'active' : '' }}">
                        <a href="{{route('sale.order.list')}}"><i class="menu-icon fa fa-caret-right"></i>Sale</a>
                        <b class="arrow"></b>
                    </li>

                    <li class="{{request()->is('sale/sale-return/*') ? 'active' : '' }}">
                        <a href="{{route('sale.sale_return.list')}}"><i class="menu-icon fa fa-caret-right"></i>Sale Return</a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li> <!---------------Sale End-------------------->


             <!--------------- Stock Deliveries -------------------->
             <li class="{{request()->is('Stocks*') ? 'active open' : '' }}">
                <a href="{{ route('stocks.delivery.list') }}" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon glyphicon glyphicon-barcode"></i>
                    <span class="menu-text">
                        Purchase
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                
                    <li class="{{request()->is('Stocks/list*') ? 'active' : '' }}">
                        <a href="{{ route('stocks.delivery.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a><b class="arrow"></b>
                    </li>
                

                
                <li class="{{request()->is('Stocks/list*') ? 'active' : '' }}">
                        <a href="{{route('stocks.purchase_return_list')}}"><i class="menu-icon fa fa-caret-right"></i>Purchase Return</a>
                        <b class="arrow"></b>
                    </li>


                
                    <li class="{{request()->is('Stocks/create*') ? 'active' : '' }}"><a href="{{ route('stocks.simple.delivery.create') }}"><i class="menu-icon fa fa-caret-right"></i> Simple Add New</a><b class="arrow"></b></li>
                
                
                </ul>
            </li>
            <!--------------- End Stock Deliveries-------------------->
            
           

            

            <!--------------- Suppliers -------------------->
            <li class="{{request()->is('supplier*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-truck"></i>
                    <span class="menu-text">
                        Suppliers
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    @can('supplier.read')
                    <li class="{{request()->is('supplier/list*') ? 'active' : '' }}">
                        <a href="{{ route('supplier.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a><b class="arrow"></b>
                    </li>
                    @endcan
                    @can('supplier.create')
                    <li class="{{request()->is('supplier/create*') ? 'active' : '' }}">
                        <a href="{{ route('supplier.create') }}"><i class="menu-icon fa fa-caret-right"></i>Add New</a><b class="arrow"></b>
                    </li>
                    @endcan
                </ul>
            </li>
            <!--------------- End Suppliers-------------------->

            <!--------------- Customer -------------------->
            <li class="{{request()->is('customer*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-user"></i>
                    <span class="menu-text">
                        Customers
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{{request()->is('customer/list*') ? 'active' : '' }}">
                        <a href="{{ route('customer.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a><b class="arrow"></b>
                    </li>
                    <li class="{{request()->is('customer/create*') ? 'active' : '' }}">
                        <a href="{{ route('customer.create') }}"><i class="menu-icon fa fa-caret-right"></i>Add New</a><b class="arrow"></b>
                    </li>
                    @can('customer.read')
                    
                    @endcan
                    @can('customer.create')
                    
                    @endcan
                </ul>
            </li>
            <!--------------- End Customer -------------------->


            <!--------------- Payments -------------------->
            <li class="{{request()->is('finance*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-dollar"></i>
                    <span class="menu-text">
                        Finance & Payments
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{{request()->is('finance/payment/list*') ? 'active' : '' }}">
                        <a href="{{ route('finance.payment.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a><b class="arrow"></b>
                    </li>
                        <!-- <li class="{{request()->is('customer/create*') ? 'active' : '' }}">
                            <a href="{{ route('customer.create') }}"><i class="menu-icon fa fa-caret-right"></i>Add New</a><b class="arrow"></b>
                        </li> -->
                    @can('customer.read')
                    
                    @endcan
                    @can('customer.create')
                    
                    @endcan
                </ul>
            </li>
            <!--------------- End Payments -------------------->


            <!--------------- Shops -------------------->
            <li class="{{request()->is('shop*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-building"></i>
                    <span class="menu-text">
                        Shops / Branches
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{{request()->is('shop/list*') ? 'active' : '' }}">
                        <a href="{{ route('shop.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a><b class="arrow"></b>
                    </li>
                    <li class="{{request()->is('shop/create*') ? 'active' : '' }}">
                        <a href="{{ route('shop.create') }}"><i class="menu-icon fa fa-caret-right"></i>Add New</a><b class="arrow"></b>
                    </li>
                    @can('shop.read')
                    
                    @endcan
                    @can('shop.create')
                    
                    @endcan
                </ul>
            </li>
            <!--------------- End Customer -------------------->




            <!--------------- Category -------------------->
            <li class="{{request()->is('Category*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-tags "></i>
                    <span class="menu-text">
                        Categories
                    </span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">

                    @can('parent_category.read')
                    <li class="{{request()->is('Category/Parent*') ? 'active' : '' }}">
                        <a href="{{route('category.parent.list')}}"><i class="menu-icon fa fa-caret-right"></i>Parent Category</a><b class="arrow"></b>
                    </li>
                    @endcan
                    @can('sub_category.read')
                    <li class="{{request()->is('Category/Sub*') ? 'active' : '' }}">
                        <a href="{{route('category.sub.list')}}"><i class="menu-icon fa fa-caret-right"></i>Sub Category</a><b class="arrow"></b>
                    </li>
                    @endcan
                    @can('product_category.read')
                    <li class="{{request()->is('Category/Product*') ? 'active' : '' }}">
                        <a href="{{route('category.product.list')}}"><i class="menu-icon fa fa-caret-right"></i>Product Category</a>
                        <b class="arrow"></b>
                    </li>
                    @endcan
                </ul>
            </li>
            <!--------------- End Category -------------------->

            


            <!--------------- User Management-------------------->
            <li class="{{request()->is('usermanagement*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon  fa fa-users "></i>
                    <span class="menu-text">
                        User Management
                    </span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">

                    <li class="{{request()->is('usermanagement/role*') ? 'active' : '' }}">
                        <a href="{{route('usermanagement.role.list')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Roles
                        </a>

                        <b class="arrow"></b>
                    </li>

                    @can('permission.read')
                    <li class="{{request()->is('usermanagement/permissions*') ? 'active' : '' }}">
                        <a href="{{route('usermanagement.permission.list')}}"> <i class="menu-icon fa fa-caret-right"></i> Permissions </a><b class="arrow"></b>
                    </li>
                    @endcan
                    @can('user.read')
                    <li class="{{request()->is('usermanagement/users*') ? 'active' : '' }}">
                        <a href="{{route('usermanagement.user.list')}}"><i class="menu-icon fa fa-caret-right"></i>Users</a><b class="arrow"></b>
                    </li>
                    @endcan
                </ul>
            </li>
             <!--------------- End User Management-------------------->


            

            <!--------------- Settings -------------------->
            <li class="{{request()->is('Settings*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon fa fa-cogs"></i>
                    <span class="menu-text">
                        Settings
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    @can('warehouse.read')
                    <li class="{{request()->is('Settings/warehouse*') ? 'active' : '' }}">
                        <a href="{{ route('Settings.warehouse.index') }}"><i class="menu-icon fa fa-caret-right"></i>Warehouses</a><b class="arrow"></b>
                    </li>
                    @endcan


                    <li class="{{request()->is('attribute-type*') ? 'active' : '' }}">
                        <a href="{{ route('attribute_type.list') }}"><i class="menu-icon fa fa-caret-right"></i>Product Attribute</a><b class="arrow"></b>
                    </li>


                    @can('location.read')
                    <li class="{{request()->is('Settings/location*') ? 'active' : '' }}">
                        <a href="{{ route('Settings.location.index') }}"><i class="menu-icon fa fa-caret-right"></i>Locations</a><b class="arrow"></b>
                    </li>
                    @endcan
                    @can('uom.read')
                    <li class="{{request()->is('Settings/uom*') ? 'active' : '' }}">
                        <a href="{{ route('Settings.uom.index') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            UOM
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @endcan
                </ul>
            </li>
            <!--------------- End Settings-------------------->


             <!--------------- Attribute -------------------->
             <li class="{{request()->is('attribute*') ? 'active open' : '' }} hidden">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-tag"></i>
                    <span class="menu-text">
                        Attribute
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">


                <?php if(1 == 0){ foreach(App\Util\Util::getAttributeList() as $attr){ ?>
                    <li class="{{ (  (request('slug') != '') && ( request('slug')  == $attr->slug )  ) ? 'active ' : '' }}">
                        <a href="{{ route('attribute.attribute-list-index', [ 'slug' => $attr->slug ?? 'noooo' ]) }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{ $attr->name }}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <?php } }?>
                </ul>
            </li>
            <!--------------- End Attribute-------------------->
           

           

           

             <!--------------- Reports -------------------->
             <li class="{{request()->is('reports*') ? 'active open' : '' }} ">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-bar-chart-o "></i>
                    <span class="menu-text">
                        Reports
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    
                    <li class="{{request()->is('report*') ? 'active' : '' }}">
                        <a href="{{ route('report.sale.all') }}"><i class="menu-icon fa fa-caret-right"></i>Sale</a><b class="arrow"></b>
                    </li>    

                    <li class="{{request()->is('report*') ? 'active' : '' }}">
                        <a href="{{ route('report.product') }}"><i class="menu-icon fa fa-caret-right"></i>Product</a><b class="arrow"></b>
                    </li>

                    <li class="{{request()->is('report*') ? 'active' : '' }}">
                        <a href="{{ route('report.product_wise') }}"><i class="menu-icon fa fa-caret-right"></i>Product Report</a><b class="arrow"></b>
                    </li>

                </ul>
            </li>
            <!--------------- End Reports-------------------->

            
        </ul><!-- /.nav-list -->

        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
</div>



