<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SaleOrder;
use App\Models\ParentCategory;
use App\Models\ProductAvailable;
use App\Models\SaleOrderItem;
use App\Models\Customer;
use App\Models\StockDelivery;
use App\Models\StockDeliveryItem;
use App\Models\StockDeliveryProduct;
use App\Models\SaleReturnItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class ProductStockController extends Controller{
    


    function productStock(Request $request){

        $shopID = $request->get('shop_id');


        if($request->ajax()){
            // withSum('comments', 'votes'); // ->with(['productQty'])
            $results    = Product::withSum('productAvailable','qty')->withSum('StockDeliveryProduct','quantity')->where(function($query) use ($shopID) {

                
            })->get();
                return Datatables::of($results)->addColumn('product_name', function ($row) {
                    return optional($row->product)->name;
                })->addColumn('product_attribute', function ($row) {
                    return ''; //str_replace(' / ','</br>',strip_tags($row->product->attributeHTML()));
                })->addColumn('uom_code', function ($row) {
                    return $row->uom_code;
                })->addColumn('description', function ($row) {
                    return $row->description; // optional($row->product)->name;
                })->addColumn('created', function ($row) {
                    return $row->created_at->format('h:i d-m-Y');
                })->rawColumns(['action','product_category_name','product_name','product_attribute','sn_tags'])
                    //  ->orderColumn('id', 'DESC')
                ->make(true);
        } 
        $data['sub_category']   = ParentCategory::with(['subCategories'])->orderBy('name','DESC')->get();
        return view('reports.product_stock_report',$data);
    }


    function product(Request $request){
        $data['sub_category'] = ParentCategory::with(['subCategories'])->orderBy('name','DESC')->get();
        $data['products']     = Product::all();
        if ($request->ajax()) {
            $productId     = $request->get('product_id');
            $productDetail = Product::findOrFail($productId);
            $dateRange     = ($request->has('date_range') && !empty($request->get('date_range'))) 
                ? $request->get('date_range') 
                : optional($productDetail)->created_at->format('d/m/Y') . ' - ' . date('d/m/Y');

            $startDate = null;
            $endDate   = null;

            try {
                if (strpos($dateRange, '-') !== false) {
                    $dateString = explode('-', $dateRange);
                    $startDate  = Carbon::createFromFormat('d/m/Y H:i:s', trim($dateString[0]) . ' 00:00:01');
                    $endDate    = Carbon::createFromFormat('d/m/Y H:i:s', trim($dateString[1]) . ' 23:59:59');
                }
            } catch (\Exception $e) {
                $startDate = null;
                $endDate   = null;
            }

            $dates = function($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            };

            $response['product_detail'] = $productDetail;

            $costPrice = (float)($productDetail->cost_price ?: 0);
            $salePrice = (float)($productDetail->price ?: 0);

            // Global product statistics
            $saleCount = (int)SaleOrderItem::where('product_id', $productId)->where($dates)->sum('qty');
            $saleAmount = (float)SaleOrderItem::where('product_id', $productId)->where($dates)
                ->selectRaw('SUM(qty * price) as total')->value('total') ?: ($saleCount * $salePrice);

            $returnCount = (int)SaleReturnItem::where('product_id', $productId)->where($dates)->sum('qty');
            $returnAmount = $returnCount * $salePrice;

            $netSaleCount = max(0, $saleCount - $returnCount);
            $netSaleAmount = max(0, $saleAmount - $returnAmount);

            $purchaseCount = (int)StockDeliveryProduct::where('product_id', $productId)->where($dates)->sum('quantity');
            $purchaseAmount = (float)StockDeliveryProduct::where('product_id', $productId)->where($dates)
                ->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?: ($purchaseCount * $costPrice);

            $availableCount = (int)ProductAvailable::where('product_id', $productId)->sum('qty');
            $availableAmount = $availableCount * $salePrice;

            $cogs = (float)SaleOrderItem::where('product_id', $productId)->where($dates)
                ->selectRaw('SUM(qty * COALESCE(cost_price, 0)) as total')->value('total') ?: ($netSaleCount * $costPrice);

            $grossProfit = $netSaleAmount - $cogs;
            $profitMargin = $netSaleAmount > 0 ? round(($grossProfit / $netSaleAmount) * 100, 1) : 0;

            $productStat['sale_count']        = $saleCount;
            $productStat['return_count']      = $returnCount;
            $productStat['net_sale_count']    = $netSaleCount;
            $productStat['sale_price']        = round($netSaleAmount);
            $productStat['purchase_count']    = $purchaseCount;
            $productStat['purchase_price']    = round($purchaseAmount);
            $productStat['available_count']   = $availableCount;
            $productStat['available_price']   = round($availableAmount);
            $productStat['cogs']              = round($cogs);
            $productStat['gross_profit']      = round($grossProfit);
            $productStat['profit_margin']     = $profitMargin;

            // Per-shop statistics with accurate shop filters
            $shops = Shop::all();
            $shops->map(function($shop) use ($productId, $dates, $costPrice, $salePrice) {
                $shopAvailable = (int)ProductAvailable::where('product_id', $productId)->where('shop_id', $shop->id)->sum('qty');
                
                $shopPurchases = (int)StockDeliveryProduct::where('product_id', $productId)
                    ->where('shop_id', $shop->id)
                    ->where($dates)->sum('quantity');

                $shopPurchaseAmount = (float)StockDeliveryProduct::where('product_id', $productId)
                    ->where('shop_id', $shop->id)
                    ->where($dates)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?: ($shopPurchases * $costPrice);

                $shopSales = (int)SaleOrderItem::where('product_id', $productId)
                    ->whereHas('saleOrder', function($q) use ($shop) {
                        $q->where('shop_id', $shop->id);
                    })->where($dates)->sum('qty');

                $shopSaleAmount = (float)SaleOrderItem::where('product_id', $productId)
                    ->whereHas('saleOrder', function($q) use ($shop) {
                        $q->where('shop_id', $shop->id);
                    })->where($dates)->selectRaw('SUM(qty * price) as total')->value('total') ?: ($shopSales * $salePrice);

                $shopReturns = (int)SaleReturnItem::where('product_id', $productId)
                    ->where('shop_id', $shop->id)
                    ->where($dates)->sum('qty');

                $shopReturnAmount = $shopReturns * $salePrice;
                $shopNetSales = max(0, $shopSales - $shopReturns);
                $shopNetSaleAmount = max(0, $shopSaleAmount - $shopReturnAmount);

                $shopCogs = (float)SaleOrderItem::where('product_id', $productId)
                    ->whereHas('saleOrder', function($q) use ($shop) {
                        $q->where('shop_id', $shop->id);
                    })->where($dates)->selectRaw('SUM(qty * COALESCE(cost_price, 0)) as total')->value('total') ?: ($shopNetSales * $costPrice);

                $shopProfit = $shopNetSaleAmount - $shopCogs;

                $shop->available_qty    = $shopAvailable;
                $shop->available_price  = round($shopAvailable * $salePrice);
                $shop->purchase_count   = $shopPurchases;
                $shop->purchase_price   = round($shopPurchaseAmount);
                $shop->sale_count       = $shopNetSales;
                $shop->sale_price       = round($shopNetSaleAmount);
                $shop->profit           = round($shopProfit);
            });

            $response['product_stats_shops_wise'] = $shops;
            $response['product_stats']            = $productStat;
            return response()->json($response);
        } else {
            return view('reports.product_report', $data);
        }
    }





    function productWise(){
        $data['sub_category']   = ParentCategory::with(['subCategories'])->orderBy('name','DESC')->get();
        return view('reports.product_wise_stock_report',$data);
    }


    function saleOrder(Request $request){
        
        $groupBy    = $request->get('group_by');
        $productId = $request->get('product_id');
        // $productItem
        // whereBelongsTo()
        // $query->where('product_id', $productId);
        $items      = SaleOrder::with(['shop', 'saleOrderItem'])->whereHas('saleOrderItem',function($query) use ($productId){
            $query->where('product_id', $productId);
        })->where(function($query) use ($request) {
            if(auth()->user()->hasRole('Super Admin')){
                
            }else{
                $query->where('shop_id',auth()->user()->shop_id);
            }
        });
        
        $items = $items->get();
        return Datatables::of($items)
        ->addColumn('title', function ($row) {
            return '<a href="'.route('sale.order.detail',$row->id).'" target="_blank" >'.$row->sale_key.'</a>';
        })->addColumn('this_product_unit_price', function ($row) use ($productId) {
            return $row->saleOrderItem->where('product_id',$productId)->first()->price;
        })->addColumn('this_product_item_count', function ($row) use ($productId) {
            return $row->saleOrderItem->where('product_id',$productId)->first()->qty;
        })->addColumn('this_product_total_bill', function ($row) use ($productId) {
            return $row->saleOrderItem->where('product_id',$productId)->first()->price * $row->saleOrderItem->where('product_id',$productId)->first()->qty;
        })->addColumn('item_count', function ($row) {
            return $row->item_count;
        })->addColumn('order_time', function ($row) {
            return $row->created_at->format('h:i / d-m-Y');
        })->addColumn('shop_name', function ($row) {
            return $row->shop->name;
        })->addColumn('customer_name', function ($row) {
            if($row->customer_id == 0){
                return 'Walking Customer';
            }else{
                return Customer::find($row->customer_id)->name;
            }
        })
        ->addColumn('action', function ($row) {
            $action = '<div class="btn-group">';
            $action .= '<a href="'.route('sale.order.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
            $action .= '<a href="'.route('sale.order.sale_return_view',$row->id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Sale Return" ><i class="ace-icon fa fa-undo bigger-120"></i></a>';
            $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation('.$row->sale_key.');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" ><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
            $action .= '</div>';
            return $action;
        })->rawColumns(['title','product_attribute','status_label','action'])
        ->make(true);
    }


    function purchase(Request $request){
            $productID = $request->get('product_id');
        
            $results =  StockDelivery::with(['Supplier'])->select();
            if($productID != ''){
                // dd('product_id',$productID);
                $results =  $results->whereRelation('deliveryProduct', 'product_id',$productID); 
            }
            return Datatables::of($results)
            ->addColumn('info_no', function ($row) {
                if($row->po_loa_loi != ''){
                    return $row->po_loa_loi;
                }else{
                    return $row->delivery_challan_no;
                }
            })
            ->addColumn('supplier', function ($row) {
                return  optional($row->supplier)->name;
            })
            ->addColumn('rec_info', function ($row) {
                return '<b> Name : '; // .$row->rec_by_name.' <br> Designation : '.$row->rec_by_designation.' <br>   Cell : '.$row->rec_by_phone.'</b>';
            })->addColumn('hand_info', function ($row) {
                return '<b> Name : ';//m.$row->hand_name.' <br> Designation : '.$row->hand_designation.' <br>   Cell : '.$row->hand_phone.'</b>';
            })->addColumn('project_name', function ($row) {
                    return '';
            })->addColumn('items_count', function ($row) {
                return StockDeliveryProduct::where('stock_delivery_id', $row->id)->count();
            })->addColumn('status_label', function ($row) {
                return '';
            })
            ->addColumn('purchased_date_new', function ($row) {
                return Carbon::parse($row->purchased_date)->format('d-m-Y');
            })->addColumn('create', function ($row) {
                return Carbon::parse($row->created_at)->format('d-m-Y');
            })
            ->addColumn('action', function ($row) {
                $statusIcon = 'fa fa-ban';
                if($row->status == 1){
                    $statusIcon = 'fa fa-check-circle-o';
                }
                $action = '<div class="btn-group">';
                $action .= '<a href="'.route('stocks.delivery.detail',$row->id).'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Detail" ><i class="ace-icon fa fa-eye bigger-120"></i></a>';
                $action .= '<a href="'.route('stocks.purchase_return_view',$row->id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Sale Return" ><i class="ace-icon fa fa-undo bigger-120"></i></a>';
                $action .= '</div>';
                return $action;
            })->rawColumns(['status_label','rec_info','hand_info','action'])
        //  ->orderColumn('id', 'DESC')
            ->make(true);
    }

}
